<?php

namespace Mepatek\UserManager;

use Nette,
	Nette\Security,
	Nette\Security\IAuthenticator,
	Nette\Utils\DateTime,
	Mepatek\UserManager\Repository\UserRepository,
	Mepatek\UserManager\Repository\RoleRepository,
	Mepatek\UserManager\Repository\UserActivityRepository,
	Mepatek\UserManager\Entity\User,
	Mepatek\UserManager\Entity\UserActivity,
	Mepatek\UserManager\AuthDrivers\IAuthDriver;


/**
 * Users authenticator.
 */
class Authenticator implements IAuthenticator
{
	/** @var UserRepository */
	protected $userRepository;
	/** @var RoleRepository */
	protected $roleRepository;
	/** @var UserActivityRepository */
	protected $userActivityRepository;

	/** @var IAuthDriver[] */
	protected $authDrivers = array();

	/**
	 * Authenticator constructor.
	 *
	 * @param UserRepository         $userRepository
	 * @param RoleRepository         $roleRepository
	 * @param UserActivityRepository $userActivityRepository
	 */
	public function __construct(
		UserRepository $userRepository,
		RoleRepository $roleRepository,
		UserActivityRepository $userActivityRepository
	)
	{
		$this->userRepository = $userRepository;
		$this->roleRepository = $roleRepository;
		$this->userActivityRepository = $userActivityRepository;
	}

	/**
	 * Performs an authentication.
	 *
	 * @param array $credentials
	 *
	 * @return Nette\Security\Identity
	 * @throws Nette\Security\AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;

		$user = $this->userRepository->findOneBy(
			[
				"userName" => $username,
			]
		);

		$authExt = false;

		foreach ($this->authDrivers as $authDriver) {
			$authDriver->setUp(
				$this->userRepository,
				$this->roleRepository,
				$this->userActivityRepository
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
				throw new Security\AuthenticationException('Wrong username.', self::IDENTITY_NOT_FOUND);
			} elseif (!Security\Passwords::verify($password, $this->userRepository->getPassword($user))) {
				throw new Security\AuthenticationException('Wrong password.', self::INVALID_CREDENTIAL);
			}
		}

		// update lastLogged
		$user->lastLogged = new DateTime();
		$this->userRepository->save($user);

		$userActivity = new UserActivity();
		$userActivity->userId = $user->id;
		$userActivity->type = "login";
		$this->userActivityRepository->save($userActivity);

		return new Security\Identity($user->id, $user->roles, $user);
	}

	/**
	 * Log logout activity
	 *
	 * @param $userId
	 */
	public function logout($userId)
	{
		$userActivity = new UserActivity();
		$userActivity->userId = $userId;
		$userActivity->type = "logout";
		$this->userActivityRepository->save($userActivity);
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
		$user = $this->userRepository->findOneBy(["email" => $email]);
		// userExist?
		if ($user) {
			$tokenExpires = new DateTime();
			$tokenExpires->add(new \DateInterval('PT60M'));     // 60 min for expire

			$token = $this->userRepository->resetPasswordToken($user, $tokenExpires);

			return $token ? $token : false;
		} else {
			return false;
		}
	}

	/**
	 * Change password for $token
	 * Set $id to finded user id
	 *
	 * @param string $token
	 * @param string $newPassword
	 * @param integer $id
	 *
	 * @return boolean
	 */
	public function changePasswordToken($token, $newPassword, &$id)
	{
		$user = $this->userRepository->findUserByToken($token);
		if ($user) {
			$id = $user->id;
			return $this->changePassword($user->id, $newPassword);
		} else {
			return false;
		}
	}

	/**
	 * Change password and reset tokens.
	 *
	 * @param integer $userId
	 * @param string  $newPassword
	 *
	 * @return boolean
	 */
	public function changePassword($userId, $newPassword)
	{
		return $this->userRepository->changePassword($userId, Nette\Security\Passwords::hash($newPassword));
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
		$sets = array();
		if($minLevel>=1) {
			$sets[] = 'abcdefghijklmnopqrstuvwxyz';
		}
		if($minLevel>=2) {
			$sets[] = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		}
		if($minLevel>=3) {
			$sets[] = '0123456789';
		}
		if($minLevel>=4) {
			$sets[] = '!@#$%&*?';
		}

		$all = '';
		$password = '';
		foreach($sets as $set)
		{
			$password .= $set[array_rand(str_split($set))];
			$all .= $set;
		}
		$all = str_split($all);
		for($i = 0; $i < $length - count($sets); $i++)
			$password .= $all[array_rand($all)];
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
