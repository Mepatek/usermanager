<?php

namespace Mepatek\UserManager\UI\Users;

use Kdyby\Doctrine\EntityManager;
use Mepatek\Components\UI\FormFactory;
use Nette\Forms\Form;

class UserChangePasswordControl extends UserControl
{
	/**
	 * Minimum password length
	 * @var integer
	 */
	public $passwordMinLength = 6;
	/**
	 * Minimum password level
	 * @var integer
	 */
	public $passwordMinLevel = 2;

	/** @var FormFactory */
	private $formFactory;
	/** @var string */
	private $linkList;
	/** @var string */
	private $linkEdit;

	/** @var boolean */
	private $permittedDelete = true;
	/** @var boolean */
	private $permittedLockUnlock = true;

	/**
	 * UserEditControl constructor.
	 *
	 * @param EntityManager $em
	 * @param FormFactory   $formFactory
	 * @param string        $linkList
	 * @param string        $linkEdit
	 */
	public function __construct(
		EntityManager $em,
		FormFactory $formFactory,
		$linkList,
		$linkEdit
	)
	{
		$this->em = $em;
		$this->formFactory = $formFactory;
		$this->linkList = $linkList;
		$this->linkEdit = $linkEdit;
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
	public function createComponentUserChangePasswordForm($name)
	{
		$id = $this->getPresenter()->getParameter("id");

		$form = $this->formFactory->createBootstrap();

		$form->addHidden("id", $id);

		$form->addPassword('newPassword', "usermanager.user_new_password")
			->addRule(Form::MIN_LENGTH, "usermanager.user_new_password_min_length", $this->passwordMinLength)
			->setRequired("usermanager.user_new_password_required");
		$form->addPassword('newPasswordConfirm', "usermanager.user_new_password_confirm")
			->setRequired("usermanager.user_new_password_confirm_required")
			->addRule(Form::EQUAL, "usermanager.user_new_password_not_same", $form['newPassword']);
		$form->addSubmit('send', "usermanager.user_change_password");

		$form->onSuccess[] = function (Form $form, $values) {
			$id = $values->id;

			$this->onBeforeUserChangePassword($id, $values->newPassword);

			if (($passwordSafe = $this->getPresenter()->getUser()->getAuthenticator()->isPasswordSafe(
					$values->newPassword,
					$this->passwordMinLength,
					$this->passwordMinLevel
				)) > 0
			) {
				if ($passwordSafe == 2 or $passwordSafe == 6) {
					$form->addError(sprintf("usermanager.user_new_password_min_length", $this->passwordMinLength));
				}
				if ($passwordSafe == 4 or $passwordSafe == 6) {
					$form->addError("usermanager.user_new_password_too_simple");
				}
			}

			if (!$this->getPresenter()->getUser()->getAuthenticator()->changePassword($id, $values->newPassword)) {
				$form->addError("usermanager.user_new_password_not_change");
				return false;
			}

			$this->onAfterUserChangePassword($id, $values->newPassword);

			if ($id) {
				$this->presenter->redirect("this", ["id" => $id]);
			}
		};

		return $form;

	}


}
