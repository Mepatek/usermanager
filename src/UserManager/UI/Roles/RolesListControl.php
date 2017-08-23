<?php
/**
 * Created by PhpStorm.
 * User: pepa
 * Date: 05.08.2017
 * Time: 17:56
 */

namespace Mepatek\UserManager\UI\Roles;


use App\Mepatek\UserManager\Entity\Role;
use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\Model\Roles;
use Ublaboo\DataGrid\DataGrid;

class RolesListControl extends RoleControl
{
	/** @var GridFactory */
	private $gridFactory;
	/** @var string */
	private $linkEdit;

	/** @var boolean */
	private $permittedDelete = true;

	/**
	 * RolesListControl constructor.
	 *
	 * @param Roles       $rolesModel
	 * @param GridFactory $gridFactory
	 * @param string      $linkEdit
	 */
	public function __construct(Roles $rolesModel, GridFactory $gridFactory, $linkEdit)
	{
		$this->rolesModel = $rolesModel;
		$this->gridFactory = $gridFactory;
		$this->linkEdit = $linkEdit;
		parent::__construct();
	}

	public function render()
	{
		$template = $this->getTemplate();
		if (isset($this->parent->translator)) {
			$template->setTranslator($this->parent->translator);
		}

		$template->render(__DIR__ . '/' . substr(__CLASS__, strrpos(__CLASS__, '\\') + 1) . '.latte');
	}


	/**
	 * @param $name
	 *
	 * @return DataGrid
	 */
	public function createComponentRolesListGrid($name)
	{
		$roles = $this->rolesModel->getCachedRoles();

		$grid = $this->gridFactory->create(
			$roles,
			"role",
			20,
			[],
			$this,
			$name
		);

		$grid->setColumnsHideable();

		$grid->addColumnText("role", "rolemanager.role");
		$grid->addColumnText("name", "rolemanager.role_name")
			->setFilterText();
		$grid->addColumnText("description", "rolemanager.role_description")
			->setDefaultHide();;

		if ($this->linkEdit) {
			$grid->addAction("roleEdit", "")
				->setTitle("rolemanager.role_action_edit")
				->setIcon("pencil");
		}
		if ($this->permittedDelete) {
			$grid->addAction("roleDelete", "")
				->setConfirm("rolemanager.role_action_delete_confirm", "role")
				->setTitle("rolemanager.role_action_delete")
				->setIcon("trash");
		}


		return $grid;

	}

	public function handleRoleEdit($role)
	{
		$this->getPresenter()->redirect($this->linkEdit, ["role" => $role]);
	}

	public function handleRoleDelete($role)
	{
		$role = $this->findRole($role);
		$this->deleteRole($role);
	}

}
