<?php

namespace Mepatek\UserManager\UI\User;


use Mepatek\UserManager\UI\Components\Slim\Slim;
use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\International\LanguageHelper;
use Mepatek\Components\UI\FormFactory;
use Nette\Application\UI\Control;

class ProfileControl extends Control
{

	/** @var array */
	public $onBeforeUserSave;
	/** @var array */
	public $onAfterUserSave;

	/** @var EntityManager */
	private $em;
	/** @var FormFactory */
	private $formFactory;
	/** @var LanguageHelper */
	private $languageHelper;

	/** @var User */
	protected $user;

	/**
	 * ProfileControl constructor.
	 *
	 * @param EntityManager  $em
	 * @param FormFactory    $formFactory
	 * @param LanguageHelper $languageHelper
	 */
	public function __construct(
		EntityManager $em,
		FormFactory $formFactory,
		LanguageHelper $languageHelper
	) {
		$this->em = $em;
		$this->formFactory = $formFactory;
		$this->languageHelper = $languageHelper;
		parent::__construct();
	}

	/**
	 * render template
	 */
	public function render()
	{
		$template = $this->getTemplate();

		if (isset($this->parent->translator)) {
			$template->setTranslator($this->parent->translator);
		}

		$user = $this->readUser();
		$template->user = $user;
//
		$template->userLinks = [];
//		$template->userLinks = $this->userLinks;

		$template->render(__DIR__ . '/' . substr(__CLASS__, strrpos(__CLASS__, '\\') + 1) . '.latte');


	}


	/**
	 * Form profile
	 * @return \Mepatek\Components\FormBootstrap
	 */
	public function createComponentProfileForm()
	{
		$form = $this->formFactory->createBootstrap();

		$form->addText("userName", "userform.user_name");
		$form->addText("fullName", "userform.user_full_name");
		$form->addText("title", "userform.user_title");
		$form->addText("email", "userform.user_email");
		$form->addText("phone", "userform.user_phone");
		$form->addText("language", "userform.user_language");

//		userform.user_select_language: Vyberte jazyk
//		userform.user_language: Jazyk
//		userform.user_thumbnail: Fotografie
		$form->addSubmit("send", "userform.user_save");

		$this->readUser();
		$form->setDefaults(
			[
				"userName" => $this->user->getUserName(),
				"fullName" => $this->user->getFullName(),
				"title" => $this->user->getTitle(),
				"email" => $this->user->getEmail(),
				"phone" => $this->user->getPhone(),
				"language" => $this->user->getLanguage(),
			]
		);

		return $form;
	}

	public function handleUploadThumbnail()
	{
		$slim = Slim::getImages("upload");
		if (count($slim) > 0) {
			$image = $slim[0];
			$id = $image["meta"]->userId;
			$this->readUser($id);
			$this->user->setThumbnail($image['output']['data']);
			$this->saveUser();
			exit();
		}

	}

	/**
	 * @return \Mepatek\Components\Form
	 */
	public function createComponentUserThumbnailForm()
	{
		$form = $this->formFactory->create();

		$form->addUpload("thumbnail", "usermanager.user_thumbnail");
		$form->addSubmit("upload", "usermanager.user_upload_thumbnail");

		$form->onSuccess[] = function (Form $form, $values) {
			$thumbnail = $values->thumbnail;
			if ($thumbnail->isOk()) {
				if (!$thumbnail->isImage()) {
					throw new \Exception("Not image!");
				}
				$image = $thumbnail->toImage();
				$this->readUser();
				$this->user->setThumbnail((string)$image);
				$this->saveUser();
//				$this->flashMessage("");
			}
		};
		return $form;
	}


	/**
	 * @return User
	 */
	private function readUser()
	{
		if (!$this->user) {
			$id = $this->getPresenter()->getUser()->getId();
			$this->user = $this->findUserById($id);
		}
		return $this->user;
	}

	/**
	 * Find user by ID
	 *
	 * @param integer $id
	 *
	 * @return User
	 */
	protected function findUserById($id)
	{
		$user = null;
		if ($id) {
			$user = $this->em->find(User::class, $id);
		}
		return $user;
	}

	/**
	 * Save user
	 *
	 * @param bool $runEvents
	 */
	protected function saveUser($runEvents = true)
	{
		if ($runEvents) {
			$this->onBeforeUserSave($this->user);
		}
		$this->em->persist($this->user);
		$this->em->flush();
		if ($runEvents) {
			$this->onAfterUserSave($this->user);
		}
	}

}
