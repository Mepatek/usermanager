<?php

namespace Mepatek\UserManager\UI;


use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\UI\Roles\RoleEditControl;
use Mepatek\UserManager\UI\Roles\RolesListControl;
use Nette\SmartObject;

class RoleManagerFormFactory
{
	use SmartObject;

	/** @var EntityManager */
	private $em;
	/** @var GridFactory */
	private $gridFactory;
	/** @var FormFactory */
	private $formFactory;
	/** @var string */
	private $linkRolesList;
	/** @var string */
	private $linkRoleEdit;

	/** @var array */
	public $onBeforeRoleSave = [];
	/** @var array  */
	public $onAfterRoleSave = [];
	/** @var array  */
	public $onBeforeRoleDelete = [];
	/** @var array  */
	public $onAfterRoleDelete = [];


	/**
	 * RoleManagerFormFactory constructor.
	 *
	 * @param EntityManager $em
	 * @param GridFactory   $gridFactory
	 * @param FormFactory   $formFactory
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		FormFactory $formFactory
	)
	{
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
	}

	/**
	 * @return RolesListControl
	 */
	public function createUsersList()
	{
		$rolesListControl = new RolesListControl(
			$this->em,
			$this->gridFactory,
			$this->linkRoleEdit
		);
		$rolesListControl->onBeforeRoleSave = $this->onBeforeRoleSave;
		$rolesListControl->onAfterRoleSave = $this->onAfterRoleSave;
		$rolesListControl->onBeforeRoleDelete = $this->onBeforeRoleDelete;
		$rolesListControl->onAfterRoleDelete = $this->onAfterRoleDelete;
		return $rolesListControl;
	}


	/**
	 * @return RoleEditControl
	 */
	public function createUserEdit()
	{
		$roleEditControl = new RoleEditControl(
			$this->em,
			$this->gridFactory,
			$this->formFactory,
			$this->linkRolesList
		);
		$roleEditControl->onBeforeRoleSave = $this->onBeforeRoleSave;
		$roleEditControl->onAfterRoleSave = $this->onAfterRoleSave;
		$roleEditControl->onBeforeRoleDelete = $this->onBeforeRoleDelete;
		$roleEditControl->onAfterRoleDelete = $this->onAfterRoleDelete;
		return $roleEditControl;
	}

}
