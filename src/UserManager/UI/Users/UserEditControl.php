<?php

namespace Mepatek\UserManager\UI\Users;


use Mepatek\UserManager\UI\Components\Slim\Slim;
use App\Mepatek\UserManager\Entity\Role;
use App\Mepatek\UserManager\Entity\User;
use App\Mepatek\UserManager\Entity\UserActivity;
use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\International\LanguageHelper;
use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\Entity\Helper\UserLink;
use Nette\Application\UI\Form;

class UserEditControl extends UserControl
{

	/** @var GridFactory */
	private $gridFactory;
	/** @var FormFactory */
	private $formFactory;
	/** @var LanguageHelper */
	private $languageHelper;
	/** @var string */
	private $linkList;
	/** @var string */
	private $linkChangePassword;
	/** @var UserLink[] */
	private $userLinks = [];

	/** @var boolean */
	private $permittedDelete = true;
	/** @var boolean */
	private $permittedLockUnlock = true;

	/** @var User */
	private $user;

	/** @var array */
	public $onBeforeRenderEdit;

	/**
	 * UserEditControl constructor.
	 *
	 * @param EntityManager  $em
	 * @param GridFactory    $gridFactory
	 * @param FormFactory    $formFactory
	 * @param LanguageHelper $languageHelper
	 * @param string         $linkList
	 * @param string         $linkChangePassword
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		FormFactory $formFactory,
		LanguageHelper $languageHelper,
		$linkList,
		$linkChangePassword
	) {
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
		$this->languageHelper = $languageHelper;
		$this->linkList = $linkList;
		$this->linkChangePassword = $linkChangePassword;
		parent::__construct();
	}

	public function handleUploadThumbnail()
	{
		$slim = Slim::getImages("upload");
		if (count($slim) > 0) {
			$image = $slim[0];
			$id = $image["meta"]->userId;
			$user = $this->readUser($id);
			$user->setThumbnail($image['output']['data']);
			$this->saveUser($user, false);
			exit();
		}

	}

	public function render()
	{
		$template = $this->getTemplate();

		if (isset($this->parent->translator)) {
			$template->setTranslator($this->parent->translator);
		}

		$user = $this->readUser();
		$template->user = $user;

		$this->onBeforeRenderEdit($this, $user);

		$template->userLinks = [];
		$template->userLinks = $this->userLinks;

		$template->render(__DIR__ . '/' . substr(__CLASS__, strrpos(__CLASS__, '\\') + 1) . '.latte');


	}


	/**
	 * @param int|null $id
	 *
	 * @return User
	 */
	private function readUser($id = null)
	{
		if (!$this->user) {
			$id = $id ? $id : $this->getPresenter()->getParameter("id");
			$this->user = $this->findUserById($id);
		}
		return $this->user;
	}

	/**
	 * @param $name
	 *
	 * @return \Mepatek\Components\FormBootstrap
	 */
	public function createComponentUserEditForm($name)
	{
		$user = $this->readUser();

		$form = $this->formFactory->createBootstrap("vertical");

		$form->addHidden("id");

		$form->addText("userName", "usermanager.user_name")
			->setRequired(true);
		$form->addText("fullName", "usermanager.user_full_name")
			->setRequired(true);
		$form->addText("title", "usermanager.user_title");
		$form->addText("email", "usermanager.user_email")
			->setRequired(true)
			->addRule(Form::EMAIL);
		$form->addText("phone", "usermanager.user_phone");
		$form->addSelect("language", "usermanager.user_language")
			->setPrompt("usermanager.user_select_language")
			->setItems(
				$this->languageHelper->getSelectItems(
					$user ? $user->getLanguage() : null
				)
			);
		if ($this->permittedLockUnlock) {
			$form->addSelect("disabled", "usermanager.user_disabled_caption")
				->setItems(
					[
						0 => "usermanager.user_enabled",
						1 => "usermanager.user_disabled",
					]
				);
		}
		// usermanager.user_created
		// usermanager.user_last_logged

		$form->addSubmit("send", "usermanager.user_save");
		$form->addSubmit("delete", "usermanager.user_delete");
		if ($user) {
//			if (!$user->isDeleted()) {
//				$form->addSubmit("send", "usermanager.user_save");
//				if ($user->isDisabled()) {
//					$form->addSubmit("enable", "usermanager.user_enable");
//				} else {
//					$form->addSubmit("disable", "usermanager.user_disable");
//				}
//				$form->addSubmit("delete", "usermanager.user_delete");
//			}

			$form->setDefaults(
				[
					"id"       => $user->getId(),
					"userName" => $user->getUserName(),
					"fullName" => $user->getFullName(),
					"title"    => $user->getTitle(),
					"email"    => $user->getEmail(),
					"phone"    => $user->getPhone(),
					"language" => $user->getLanguage(),
					"disabled" => $user->isDisabled() ? 1 : 0,
				]
			);

		} else {
//			$form->addSubmit("send", "usermanager.user_save");
		}

		$form->onSuccess[] = function (Form $form, $values) {
			$id = $values->id;

			switch ($form->isSubmitted()->getName()) {
				// save
				case "send":
					if ($id) {
						$user = $this->findUserById($id);
					} else {
						$user = new User();
					}
					$user->setUserName($values->userName);
					$user->setFullName($values->fullName);
					$user->setTitle($values->title);
					$user->setEmail($values->email);
					$user->setPhone($values->phone);
					$user->setLanguage($values->language);
					$this->saveUser($user);

					$this->presenter->redirect("this", ["id" => $user->getId()]);

					break;
				case "delete":
					// delete
					$user = $this->findUserById($id);
					$this->deleteUser($user);
					break;
			}

		};

		return $form;

	}

	/**
	 * @return \Mepatek\Components\Form
	 */
	public function createComponentUserThumbnailForm()
	{
		$form = $this->formFactory->create();

		$id = $this->getPresenter()->getParameter("id");

		$form->addHidden("id", $id);

		$form->addUpload("thumbnail", "usermanager.user_thumbnail");
		$form->addSubmit("upload", "usermanager.user_upload_thumbnail");

		$form->onSuccess[] = function (Form $form, $values) {
			$thumbnail = $values->thumbnail;
			if ($thumbnail->isOk()) {
				if (!$thumbnail->isImage()) {
					throw new \Exception("Not image!");
				}
				$image = $thumbnail->toImage();
				$user = $this->findUserById($values->id);
				$user->setThumbnail((string)$image);
				$this->saveUser($user, false);
//				$this->flashMessage("");
			}
		};
		return $form;
	}

	public function createComponentLoginHistory($name)
	{
		$qb = $this->em->getRepository(UserActivity::class)
			->createQueryBuilder("ua")
			->select("ua")
			->where("ua.user = :user")
//			->orderBy("ua.datetime", "DESC")
			->setParameter("user", $this->readUser());

		$grid = $this->gridFactory->create(
			$qb,
			"id",
			20,
			[],
			$this,
			$name
		);

		$grid->addColumnDateTime("datetime", "Datum a čas")
			->setFormat("d.m.Y H:i");
		$grid->addColumnText("ip", "IP");
		$grid->addColumnText("type", "Akce");
		$grid->setDefaultSort(["datetime"=>"DESC"]);

		return $grid;
	}

	public function createComponentUserRoleForm()
	{
		$user = $this->readUser();

		$form = $this->formFactory->createBootstrap();
		$roles = $this->em->getRepository(Role::class)
			->createQueryBuilder("role")
			->select("role")
			->getQuery()
			->getResult();

		$form->addHidden("id", $user->getId());

		foreach ($roles as $role) {
			$checkbox = bin2hex($role->getRole());
			$form->addCheckbox($checkbox, $role->getName() ?: $role->getRole())
				->setDefaultValue($user->isInRole($role->getRole()));
		}

		$form->addSubmit("send", "Ulož role");

		$form->onSuccess[] = function ($form, $values) {
			$id = $values->id;
			$user = $this->readUser($id);
			$user->setRoles([]);
			foreach ($values as $key => $value) {
				if ($value and $key!=="id") {
					$key = hex2bin($key);
					$role = $this->em->find(Role::class, $key);
					if ($role) {
						$user->addRole($role);
					}
				}
			}
			$this->saveUser($user, false);
		};
		return $form;
	}

	/*
	 *
	 */
	public function handleRemoveThumbnail($id)
	{
		$user = $this->findUserById($id);
		$user->setThumbnail(null);
		$this->saveUser($user, false);
	}

	/**
	 * @return UserLink[]
	 */
	public function getUserLinks()
	{
		return $this->userLinks;
	}

	/**
	 * @param UserLink[] $userLinks
	 */
	public function setUserLinks($userLinks)
	{
		$this->userLinks = $userLinks;
	}

	/**
	 * @param string $description
	 * @param string $link
	 * @param int|string $counter
	 */
	public function addUserLinks($description, $link=null, $counter=null)
	{
		$userLink = new UserLink();
		$userLink->description = $description;
		$userLink->link = $link;
		$userLink->counter = $counter;
		$this->userLinks[] = $userLink;
	}
}

