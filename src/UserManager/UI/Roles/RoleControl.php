<?php

namespace Mepatek\UserManager\UI\Roles;


use App\Mepatek\UserManager\Entity\Role;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

abstract class RoleControl extends Control
{
	/** @var EntityManager */
	protected $em;

	/** @var ITranslator */
	protected $translator;

	/** @var array */
	public $onBeforeRoleSave = [];
	/** @var array */
	public $onAfterRoleSave = [];
	/** @var array */
	public $onBeforeRoleDelete = [];
	/** @var array */
	public $onAfterRoleDelete = [];

	/**
	 * Find role by ID
	 *
	 * @param string $role
	 *
	 * @return Role
	 */
	protected function findUserById($role)
	{
		$role = null;
		if ($role) {
			$role = $this->em->find(Role::class, $role);
		}
		return $role;
	}


	/**
	 * Save role
	 *
	 * @param Role $role
	 * @param bool $runEvents
	 */
	protected function saveRole(Role $role, $runEvents = true)
	{
		if ($runEvents) {
			$this->onBeforeRoleSave($role);
		}
		$this->em->persist($role);
		$this->em->flush();
		if ($runEvents) {
			$this->onAfterRoleSave($role);
		}
	}

	/**
	 * Delete role
	 *
	 * @param Role $role
	 */
	protected function deleteRole(Role $role)
	{
		$this->onBeforeRoleDelete($role);
		$role->setDeleted(true);
		$this->em->persist($role);
		$this->em->flush();
		$this->onAfterRoleDelete($role);
	}

}
