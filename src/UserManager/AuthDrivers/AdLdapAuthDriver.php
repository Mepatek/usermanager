<?php

namespace Mepatek\UserManager\AuthDrivers;

use Adldap\Models,
	Adldap\Adldap,
	Adldap\Connections\Configuration;

use Kdyby\Doctrine\EntityManager;
use App\Mepatek\UserManager\Entity\User;
use App\Mepatek\UserManager\Entity\Role;
use App\Mepatek\UserManager\Entity\UserActivity;

class AdLdapAuthDriver implements IAuthDriver
{
	/** @var Adldap */
	protected $ad = null;
	/** @var array */
	protected $adConfig;
	/** @var false|true|array true = all users, array = user in group [group1, group2, ...] */
	protected $autoAddNewUsersInGroups;
	/** @var boolean true = auto update roles in authenticate method */
	protected $autoUpdateRole = false;
	/** @var boolean true = auto create role if not exist */
	protected $autoCreateRole = false;
	/** @var array group=>role mapping */
	protected $group2Role;

	/** @var EntityManager */
	protected $em;

	/**
	 * AdLdapAuthDriver constructor.
	 *
	 * @param array           $adConfig
	 * @param bool            $autoUpdateRole
	 * @param bool            $autoCreateRole
	 * @param null|true|array $autoAddNewUsersInGroups
	 * @param array           $group2Role
	 */
	public function __construct(
		array $adConfig,
		$autoUpdateRole = false,
		$autoCreateRole = false,
		$autoAddNewUsersInGroups = null,
		$group2Role = []
	)
	{
		$this->adConfig = $adConfig;
		$this->autoUpdateRole = $autoUpdateRole;
		$this->autoCreateRole = $autoCreateRole;
		$this->autoAddNewUsersInGroups = $autoAddNewUsersInGroups;
		$this->group2Role = $group2Role;
	}

	/**
	 * Set Up event
	 *
	 * @param EntityManager $em
	 */
	public function setUp(EntityManager $em)
	{
		$this->em = $em;
	}

	/**
	 * @param string $username
	 * @param string $password
	 * @param User   $user
	 *
	 * @return boolean
	 */
	public function authenticate($username, $password, $user)
	{
		if ($this->ad === null) {
			$this->ad = new Adldap($this->adConfig);
		}
		$authSuccess = false;
		if ($this->ad->auth()->attempt($username, $password, true)) {
			$adUser = $this->ad->search()->find($username);
			$sid = $adUser->getConvertedSid();

			if ($user === null and $this->hasAutoAddUser($adUser)) {
				$user = $this->createUserFromAd($adUser);
			}

			if ($user !== null) {
				if ($this->autoUpdateRole) {
					$this->updateRole($user, $adUser);
				}
				$user->addAuthDriver($this->getName(), $sid);
				$authSuccess = true;
			}
		}
		return $authSuccess;
	}

	/**
	 * If autoAddNewUsersInGroups==true or user member of grou in autoAddNewUsersInGroups return true
	 *
	 * @param Models\User $adUser
	 *
	 * @return boolean
	 */
	protected function hasAutoAddUser(Models\User $adUser)
	{
		if ($this->autoAddNewUsersInGroups === true) {
			return true;
		}
		if (!is_array($this->autoAddNewUsersInGroups)) {
			return false;
		}

		foreach ($adUser->getGroupNames() as $group) {
//			$group = \Adldap\Classes\Utilities::unescape($group);
			if (in_array($group, $this->autoAddNewUsersInGroups, true)) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Create User Entity from adUser
	 *
	 * @param Models\User $adUser
	 *
	 * @return null|user
	 */
	protected function createUserFromAd(Models\User $adUser)
	{
		$user = new User();

		$user->setFullName($adUser->getDisplayName());
		$user->setUserName($adUser->getAccountName());
		$user->setEmail($adUser->getEmail());
		$user->setPhone($adUser->getTelephoneNumber());
		$user->setTitle($adUser->getTitle());
		$user->setThumbnail($adUser->getThumbnailEncoded());

		// save user
		try {
			$this->em->persist($user);

			$userActivity = new UserActivity();
			$userActivity->setUser($user);
			$userActivity->setType("createFromAuthDriver");
			$userActivity->setDescription("Auto create from " . $this->getName());
			$this->em->persist($userActivity);

			$this->em->flush();

		} catch (\Exception $e) {
			$user = null;
		}

		return $user;
	}

	/**
	 * Update roles
	 *
	 * @param User        $user
	 * @param Models\User $adUser
	 */
	protected function updateRole(User $user, Models\User $adUser)
	{
		$memberOf = $adUser->getGroupNames();
		foreach ($this->group2Role as $group => $role) {
			if (in_array($group, $memberOf, true)) {
				if ($this->roleExists($role)) {
		      $roleObj = $this->em->find(Role::class, $role);
					$user->addRole($roleObj);
				}
			}
		}
	}


	/**
	 * True if role exists
	 * If not exists and autoCreateRole=true, create it
	 *
	 * @param string $roleId
	 *
	 * @return boolean
	 */
	protected function roleExists($roleId)
	{
		$role = $this->em->find(Role::class, $roleId);
		if (!$role and $this->autoCreateRole) {
			$role = new Role();
			$role->setRole($roleId);
			$this->em->persist($role);
			$this->em->flush();
		}
		return $role ? true : false;
	}

	/**
	 * Get auth driver name (max 30char)
	 * @return string
	 */
	public function getName()
	{
		return "AdLDAP";
	}

	/**
	 * @param string $username
	 * @param string $authId
	 * @param string $newPassword
	 *
	 * @return boolean
	 */
	public function changePassword($username, $authId, $newPassword)
	{
		return false;
	}

	/**
	 * @return boolean
	 */
	public function hasChangePassword()
	{
		return false;
	}

}
