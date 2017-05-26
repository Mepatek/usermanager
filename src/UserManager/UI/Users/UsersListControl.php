<?php

namespace Mepatek\UserManager\UI\Users;


use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\UI\GridFactory;
use Nette\Application\UI\Control;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\DataSource\DoctrineDataSource;

class UsersListControl extends Control
{

	/** @var EntityManager */
	private $em;
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

		$grid->addColumnText("userName", "User name")
			->setDefaultHide();
		$grid->addColumnText("fullName", "Full name");
		$grid->addColumnText("title", "Title");
		$grid->addColumnText("email", "E-mail");
		$grid->addColumnText("phone", "Phone")
			->setDefaultHide();
		$grid->addColumnDateTime("created", "Created")
			->setDefaultHide();
		$grid->addColumnDateTime("lastLogged", "Last logged");
		$grid->addColumnStatus("disabled", "Status")
			->setCaret(FALSE)
			->addOption(1, 'Locked')
			->setIcon("lock")
			->setClass("btn-warning")
			->endOption()
			->addOption(0, 'OK')
			->setIcon("unlock-alt")
			->setClass("btn-success")
			->endOption()
			->onChange[] =
			function ($id, $new_value) {
				$user = $this->findUserById($id);
				$user->setDisabled($new_value);
				$this->saveUser($user);
			};

		if ($this->linkEdit) {
			$grid->addAction("userEdit", "")
				->setTitle("Edit user")
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
		$user->setDeleted(true);
		$this->saveUser($user);
	}

	/**
	 * @param $id
	 *
	 * @return User
	 */
	protected function findUserById($id)
	{
		return $this->em->find(User::class, $id);
	}


	protected function saveUser(User $user)
	{
		$this->em->persist($user);
		$this->em->flush();
	}
}
