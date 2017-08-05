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
	 * @param EntityManager $em
	 * @param GridFactory   $gridFactory
	 * @param string        $linkEdit
	 */
	public function __construct(EntityManager $em, GridFactory $gridFactory, $linkEdit)
	{
		$this->em = $em;
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
		$qb = $this->em->getRepository(Role::class)
			->createQueryBuilder("role")
			->select("role")
			->where("role.deleted = 0");

		$grid = $this->gridFactory->create(
			$qb,
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
				->setConfirm(
					function ($item) {
						return "Are you sure to delete role " . $item->getRole() . "?";
					}
				)
				->setTitle("Delete role")
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
		$role = $this->findRoleById($role);
		$this->deleteRole($role);
	}

}
