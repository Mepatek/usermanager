<?php

namespace Mepatek\UserManager\UI\Users;


use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\International\LanguageHelper;
use Mepatek\Components\UI\FormFactory;
use Nette\Application\UI\Form;
use Nette\Http\FileUpload;

class UserEditControl extends UserControl
{

	/** @var FormFactory */
	private $formFactory;
	/** @var LanguageHelper */
	private $languageHelper;
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
	 * @param EntityManager  $em
	 * @param FormFactory    $formFactory
	 * @param LanguageHelper $languageHelper
	 * @param string         $linkList
	 * @param string         $linkChangePassword
	 */
	public function __construct(
		EntityManager $em,
		FormFactory $formFactory,
		LanguageHelper $languageHelper,
		$linkList,
		$linkChangePassword
	)
	{
		$this->em = $em;
		$this->formFactory = $formFactory;
		$this->languageHelper = $languageHelper;
		$this->linkList = $linkList;
		$this->linkChangePassword = $linkChangePassword;
		parent::__construct();
	}

	public function render()
	{
		$template = $this->getTemplate();

		if (isset($this->parent->translator)) {
			$template->setTranslator($this->parent->translator);
		}

		$id = $this->getPresenter()->getParameter("id");
		$template->user = $this->findUserById($id);

		$template->render(__DIR__ . '/' . substr(__CLASS__, strrpos(__CLASS__, '\\') + 1) . '.latte');


	}


	/**
	 * @param $name
	 *
	 * @return \Mepatek\Components\FormBootstrap
	 */
	public function createComponentUserEditForm($name)
	{
		$id = $this->getPresenter()->getParameter("id");
		$user = $this->findUserById($id);

		$form = $this->formFactory->createBootstrap();

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

					break;
				case "delete":
					// delete
					$user = $this->findUserById($id);
					$this->deleteUser($user);
					break;
			}

			if ($id) {
				$this->presenter->redirect("this", ["id" => $id]);
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


	/*
	 *
	 */
	public function handleRemoveThumbnail($id)
	{
		$user = $this->findUserById($id);
		$user->setThumbnail(null);
		$this->saveUser($user, false);
	}

}

