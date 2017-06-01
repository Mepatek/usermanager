<?php

namespace Mepatek\UserManager\UI\Users;


use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

abstract class UserControl extends Control
{

	/** @var EntityManager */
	protected $em;

	/** @var ITranslator */
	protected $translator;

	/** @var array */
	public $onBeforeUserSave = [];
	/** @var array */
	public $onAfterUserSave = [];
	/** @var array */
	public $onBeforeUserDelete = [];
	/** @var array */
	public $onAfterUserDelete = [];
	/** @var array  */
	public $onBeforeUserChangePassword = [];
	/** @var array  */
	public $onAfterUserChangePassword = [];

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
	 * @param bool $runEvents
	 */
	protected function saveUser(User $user, $runEvents = true)
	{
		if ($runEvents) {
			$this->onBeforeUserSave($user);
		}
		$this->em->persist($user);
		$this->em->flush();
		if ($runEvents) {
			$this->onAfterUserSave($user);
		}
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
