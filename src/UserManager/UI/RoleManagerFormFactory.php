<?php

namespace Mepatek\UserManager\UI;


use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\Authorizator;
use Mepatek\UserManager\UI\Roles\RoleEditControl;
use Mepatek\UserManager\UI\Roles\RolesListControl;
use Nette\SmartObject;

class RoleManagerFormFactory
{
	use SmartObject;

	/** @var Authorizator */
	private $authorizator;
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
	 * @param Authorizator $authorizator
	 * @param GridFactory   $gridFactory
	 * @param FormFactory   $formFactory
	 */
	public function __construct(
		Authorizator $authorizator,
		GridFactory $gridFactory,
		FormFactory $formFactory
	)
	{
		$this->authorizator = $authorizator;
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
	}

	/**
	 * @return RolesListControl
	 */
	public function createRolesList()
	{
		$rolesListControl = new RolesListControl(
			$this->authorizator->getRolesModel(),
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
	public function createRoleEdit()
	{
		$roleEditControl = new RoleEditControl(
			$this->authorizator->getRolesModel(),
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

	/**
	 * @return string
	 */
	public function getLinkRolesList()
	{
		return $this->linkRolesList;
	}

	/**
	 * @param string $linkRolesList
	 */
	public function setLinkRolesList($linkRolesList)
	{
		$this->linkRolesList = $linkRolesList;
	}

	/**
	 * @return string
	 */
	public function getLinkRoleEdit()
	{
		return $this->linkRoleEdit;
	}

	/**
	 * @param string $linkRoleEdit
	 */
	public function setLinkRoleEdit($linkRoleEdit)
	{
		$this->linkRoleEdit = $linkRoleEdit;
	}

}
