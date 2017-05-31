<?php

namespace Mepatek\UserManager\UI\Users;


use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\SmartObject;

abstract class UserControl extends Control
{

	use SmartObject;

	/** @var EntityManager */
	protected $em;

	/** @var array */
	public $onBeforeUserSave = [];
	/** @var array  */
	public $onAfterUserSave = [];
	/** @var array  */
	public $onBeforeUserDelete = [];
	/** @var array  */
	public $onAfterUserDelete = [];

	/**
	 * Find user by ID
	 *
	 * @param integer $id
	 *
	 * @return User
	 */
	protected function findUserById($id)
	{
		$user = $this->em->find(User::class, $id);
		return $user;
	}


	/**
	 * Save user
	 *
	 * @param User $user
	 */
	protected function saveUser(User $user)
	{
		$this->onBeforeUserSave($user);
		$this->em->persist($user);
		$this->em->flush();
		$this->onAfterUserSave($user);
	}

	/**
	 * Delete user
	 *
	 * @param User $user
	 */
	protected function deleteUser(User $user)
	{
		$this->onBeforeUserDelete($user);
		$user->setDeleted(true);
		$this->em->persist($user);
		$this->em->flush();
		$this->onAfterUserDelete($user);
	}

}
