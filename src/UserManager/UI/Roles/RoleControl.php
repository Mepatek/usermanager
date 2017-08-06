<?php

namespace Mepatek\UserManager\UI\Roles;


use App\Mepatek\UserManager\Entity\Role;
use Mepatek\UserManager\Model\Roles;
use Nette\Application\UI\Control;
use Nette\Localization\ITranslator;

abstract class RoleControl extends Control
{
	/** @var Roles */
	protected $rolesModel;

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
	 * @param string $id
	 *
	 * @return Role
	 */
	protected function findRole($id)
	{
		return $this->rolesModel->find($id);
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
		$this->rolesModel->save($role);
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
		$this->rolesModel->delete($role);
		$this->onAfterRoleDelete($role);
	}

}
