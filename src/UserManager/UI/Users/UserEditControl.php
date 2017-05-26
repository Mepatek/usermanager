<?php

namespace Mepatek\UserManager\UI\Users;


use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\UI\FormFactory;
use Nette\Application\UI\Control;

class UserEditControl extends Control
{

	/** @var EntityManager */
	private $em;
	/** @var FormFactory */
	private $formFactory;
	/** @var string */
	private $linkList;
	/** @var string */
	private $linkChangePassword;

	/** @var boolean */
	private $permittedDelete = true;
	/** @var boolean */
	private $permittedLockUnlock = true;

	/**
	 * UserEditControl constructor.
	 *
	 * @param EntityManager $em
	 * @param FormFactory   $formFactory
	 * @param string        $linkEdit
	 * @param string        $linkChangePassword
	 */
	public function __construct(EntityManager $em, FormFactory $formFactory, $linkList, $linkChangePassword)
	{
		$this->em = $em;
		$this->formFactory = $formFactory;
		$this->linkList = $linkList;
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
	 * @return
	 */
	public function createComponentUserEditForm($name)
	{
		$grid = new DataGrid($this, $name);
		$qb = $this->em->getRepository(User::class)
			->createQueryBuilder("user")
			->select("user")
			->where("user.deleted = 0");
		$data = new DoctrineDataSource($qb, "id");
		$grid->setDataSource($data);

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
		$grid->addColumnStatus("disabled", "Disabled");

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
		$grid->addAction("userDisable", "")
			->setTitle("Disable user")
			->setIcon("lock");
		$grid->addAction("userEnable", "")
			->setTitle("Enable user")
			->setIcon("unlock-alt");
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

		$grid->allowRowsAction(
			'userDisable', function ($item) {
			return !$item->isDisabled();
		}
		);
		$grid->allowRowsAction(
			'userEnable', function ($item) {
			return $item->isDisabled();
		}
		);

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

	public function handleUserDisable($id)
	{
		$user = $this->findUserById($id);
		$user->setDisabled(true);
		$this->saveUser($user);
	}

	public function handleUserEnable($id)
	{
		$user = $this->findUserById($id);
		$user->setDisabled(false);
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
