<?php

namespace Mepatek\UserManager;

use App\Mepatek\UserManager\Entity\User;
use App\Mepatek\UserManager\Entity\UserActivity;
use Kdyby\Doctrine\EntityManager;
use Nette\Security\AuthenticationException;
use Nette\Security\IAuthenticator,
	Mepatek\UserManager\AuthDrivers\IAuthDriver;
use Nette\Security\Identity;
use Nette\Security\Passwords;


/**
 * Users authenticator.
 */
class Authenticator implements IAuthenticator
{
	/** @var EntityManager */
	protected $em;

	/** @var IAuthDriver[] */
	protected $authDrivers = [];

	/**
	 * Authenticator constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * Performs an authentication.
	 *
	 * @param array $credentials
	 *
	 * @return Identity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$user = $this->em->getRepository(User::class)
			->findOneBy(
				[
					"userName" => $username,
				]
			);

		$authExt = false;

		foreach ($this->authDrivers as $authDriver) {
			$authDriver->setUp(
				$this->em
			);
			if ($authExt = $authDriver->authenticate($username, $password, $user)) {
				if ($user) {
					$user->authMethod = $authDriver->getName();
					break;
				} else {
					$authExt = false;
				}
			}
		}

		if (!$authExt) {
			if (!$user) {
				throw new AuthenticationException('Wrong username.', self::IDENTITY_NOT_FOUND);
			} elseif (!Passwords::verify($password, $user->getPwHash())) {
				throw new AuthenticationException('Wrong password.', self::INVALID_CREDENTIAL);
			}
		}

		// update lastLogged
		$user->setLastLogged(new \DateTime());
		$this->em->persist($user);

		$userActivity = new UserActivity();
		$userActivity->setUser($user);
		$userActivity->setType("login");
		$this->em->persist($userActivity);
		$this->em->flush();

		bdump($user);
		return new Identity($user->getId(), $user->getIdentityRoles(), $user->getIdentityData());
	}

	/**
	 * Log logout activity
	 *
	 * @param $userId
	 */
	public function logout($userId)
	{
		$user = $this->em->find(User::class, $userId);
		if ($user) {
			$userActivity = new UserActivity();
			$userActivity->setUser($user);
			$userActivity->setType("logout");
			$this->em->persist($userActivity);
			$this->em->flush($userActivity);
		}
	}

	/**
	 * Generate token for change password.
	 *
	 * @param string $email
	 *
	 * @return string|false
	 */
	public function resetPasswordToken($email)
	{
		$user = $this->em->getRepository(User::class)
			->findOneBy(["email" => $email]);
		// userExist?
		if ($user) {
			$tokenExpires = new \DateTime();
			$tokenExpires->add(new \DateInterval('PT60M'));     // 60 min for expire

			try {
				$token = $user->resetPwToken(new \DateInterval('PT30M'));
				$this->em->flush($user);
			} catch (\Exception $e) {
				$token = null;
			}

			return $token ? $token : false;
		} else {
			return false;
		}
	}


	/**
	 * Change password for $token
	 * Set $id to finded user id
	 *
	 * @param string  $token
	 * @param string  $newPassword
	 * @param integer $id
	 *;
	 *
	 * @return boolean
	 */
	public function changePasswordToken($token, $newPassword, &$id)
	{
		$user = $this->em->getRepository(User::class)
			->createQueryBuilder("user")
			->where("user.pwToken=:pwToken AND user.pwTokenExpire>=:pwTokenExpire")
			->getQuery()
			->setParameters(
				[
					":pwToken" => $token,
					":pwTokenExpire" => new \DateTime(),
				]
			)
			->setMaxResults(1)
			->getOneOrNullResult()
			;
		if ($user) {
			$id = $user->getId();
			$user->changePassword(Passwords::hash($newPassword));
			$this->em->flush();
		} else {
			return false;
		}
	}


	/**
	 * Check password length and check password complexity
	 *
	 * @param string  $password
	 * @param integer $minLength Minimum length of chatacter
	 * @param integer $minLevel Minimum level safe of password
	 *
	 * @return int 0 -password is OK, 2 -password is short, 4 -password is not safe, 6 -password is short and not safe
	 */
	public function isPasswordSafe($password, $minLength, $minLevel)
	{
		$passwordLevel = 0;

		if (preg_match('`[A-Z]`', $password)) // at least one big sign
		{
			$passwordLevel++;
		}
		if (preg_match('`[a-z]`', $password)) // at least one small sign
		{
			$passwordLevel++;
		}
		if (preg_match('`[0-9]`', $password)) // at least one digit
		{
			$passwordLevel++;
		}
		if (preg_match('`[-!"#$%&\'()* +,./:;<=>?@\[\] \\\\^_\`{|}~]`', $password)) // at least one special character
		{
			$passwordLevel++;
		}

		$retValue = 0;

		if ($minLength > strlen($password)) {
			$retValue += 2;
		}
		if ($minLevel > $passwordLevel) {
			$retValue += 4;
		}

		return $retValue;
	}

	/**
	 * Generate random password with length
	 *
	 * @param integer $length length of password
	 * @param integer $minLevel Minimum level safe of password
	 *
	 * @return string
	 */
	public function generateRandomPassword($length, $minLevel)
	{
		$sets = [];
		if ($minLevel >= 1) {
			$sets[] = 'abcdefghijklmnopqrstuvwxyz';
		}
		if ($minLevel >= 2) {
			$sets[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		if ($minLevel >= 3) {
			$sets[] = '0123456789';
		}
		if ($minLevel >= 4) {
			$sets[] = '!@#$%&*?';
		}

		$all = '';
		$password = '';
		foreach ($sets as $set) {
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for ($i = 0; $i < $length - count($sets); $i++) {
			$password .= $all[array_rand($all)];
		}
		$password = str_shuffle($password);
		return $password;

	}

	/**
	 * Add authDriver
	 *
	 * @param IAuthDriver $authDriver
	 */
	public function addAuthDriver(IAuthDriver $authDriver)
	{
		$this->authDrivers[] = $authDriver;
	}

}
