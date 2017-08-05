<?php

namespace Mepatek\UserManager\AuthDrivers;

use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;

/**
 * Interface IAuthDriver
 *
 * @package Mepatek\UserManager\AuthDrivers
 */
interface IAuthDriver
{
	/**
	 * Set Up event
	 *
	 * @param EntityManager $em
	 */
	public function setUp(EntityManager $em);

	/**
	 * @param string $username
	 * @param string $password
	 * @param User   $user (finded user before authenticate)
	 *
	 * @return boolean
	 */
	public function authenticate($username, $password, $user);

	/**
	 * Get auth driver name (max 30char)
	 * @return string
	 */
	public function getName();

	/**
	 * @param string $username
	 * @param string $authId
	 * @param string $newPassword
	 *
	 * @return boolean
	 */
	public function changePassword($username, $authId, $newPassword);

	/**
	 * @return boolean
	 */
	public function hasChangePassword();

}
