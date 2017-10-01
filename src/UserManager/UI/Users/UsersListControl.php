<?php

namespace Mepatek\UserManager\UI\Users;


use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\UI\GridFactory;
use Ublaboo\DataGrid\DataGrid;


class UsersListControl extends UserControl
{

	/** @var GridFactory */
	private $gridFactory;
	/** @var string */
	private $linkEdit;
	/** @var string */
	private $linkChangePassword;

	/** @var boolean */
	private $permittedDelete = true;
	/** @var boolean */
	private $permittedLockUnlock = true;


	/**
	 * UsersListControl constructor.
	 *
	 * @param EntityManager $em
	 * @param GridFactory   $gridFactory
	 * @param string        $linkEdit
	 * @param string        $linkChangePassword
	 */
	public function __construct(EntityManager $em, GridFactory $gridFactory, $linkEdit, $linkChangePassword)
	{
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->linkEdit = $linkEdit;
		$this->linkChangePassword = $linkChangePassword;
		parent::__construct();
	}

	public function render()
	{
		$template = $this->getTemplate();
		if (isset($this->parent->translator)) {
			$template->setTranslator($this->parent->translator);
		}

//		if (isset($this->parent->translator)) {
//			$template->setTranslator($this->parent->translator);
//		}


		$template->render(__DIR__ . '/' . substr(__CLASS__, strrpos(__CLASS__, '\\') + 1) . '.latte');
	}


	/**
	 * @param $name
	 *
	 * @return DataGrid
	 */
	public function createComponentUsersListGrid($name)
	{
		$qb = $this->em->getRepository(User::class)
			->createQueryBuilder("user")
			->select("user")
			->where("user.deleted = 0");

		$grid = $this->gridFactory->create(
			$qb,
			"id",
			20,
			[],
			$this,
			$name
		);

		$grid->setColumnsHideable();

		$grid->addColumnText("userName", "usermanager.user_name")
			->setDefaultHide();
		$grid->addColumnText("fullName", "usermanager.user_full_name")
			->setFilterText();
		$grid->addColumnText("title", "usermanager.user_title");
		$grid->addColumnText("email", "usermanager.user_email")
			->setFilterText();
		$grid->addColumnText("phone", "usermanager.user_phone")
			->setDefaultHide();
		$grid->addColumnDateTime("created", "usermanager.user_created")
			->setDefaultHide();
		$grid->addColumnDateTime("lastLogged", "usermanager.user_last_logged")
			->setFormat("d.m.Y H:i:s");
		$disabledColumn = $grid->addColumnStatus("disabled", "usermanager.user_disabled_caption")
			->setCaret(false)
			->addOption(1, 'usermanager.user_disabled')
			->setIcon("lock")
			->setClass("btn-warning")
			->endOption()
			->addOption(0, 'usermanager.user_enabled')
			->setIcon("unlock-alt")
			->setClass("btn-success")
			->endOption();

		if ($this->permittedLockUnlock) {
			$disabledColumn->onChange[] =
				function ($id, $new_value) {
					$user = $this->findUserById($id);
					$user->setDisabled($new_value);
					$this->saveUser($user);
					if ($this->presenter->isAjax()) {
						$this->redrawControl('flashes');
						$this["usersListGrid"]->redrawItem($id);
					}
				};
		}

		if ($this->linkEdit) {
			$grid->addAction("userEdit", "")
				->setTitle("usermanager.user_action_edit")
				->setIcon("pencil");
		}
		if ($this->linkChangePassword) {
			$grid->addAction("userChangePassword", "")
				->setTitle("Change password")
				->setIcon("user-secret");
		}
		if ($this->permittedDelete) {
			$grid->addAction("userDelete", "")
				->setConfirm(
					function ($item) {
						return "Are you sure to delete user " . $item->getFullName() . "?";
					}
				)
				->setTitle("Delete user")
				->setIcon("trash");
		}

		return $grid;

	}

	public function handleUserEdit($id)
	{
		$this->getPresenter()->redirect($this->linkEdit, ["id" => $id]);
	}

	public function handleUserChangePassword($id)
	{
		$this->getPresenter()->redirect($this->linkChangePassword, ["id" => $id]);
	}

	public function handleUserDelete($id)
	{
		$user = $this->findUserById($id);
		$this->deleteUser($user);
	}

}
