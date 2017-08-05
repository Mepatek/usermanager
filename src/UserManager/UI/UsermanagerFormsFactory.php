<?php

namespace Mepatek\UserManager\UI;


use Kdyby\Doctrine\EntityManager;

use Mepatek\Components\International\LanguageHelper;
use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\UI\Users\UserChangePasswordControl;
use Mepatek\UserManager\UI\Users\UserEditControl;
use Mepatek\UserManager\UI\Users\UsersListControl;
use Nette\SmartObject;

class UsermanagerFormsFactory
{

	use SmartObject;

	/** @var EntityManager */
	private $em;
	/** @var GridFactory */
	private $gridFactory;
	/** @var FormFactory */
	private $formFactory;
	/** @var LanguageHelper */
	private $languageHelper;


	/** @var string */
	private $linkUsersList;
	/** @var string */
	private $linkUserEdit;
	/** @var string */
	private $linkUserChangePassword;

	/** @var array */
	public $onBeforeUserSave = [];
	/** @var array  */
	public $onAfterUserSave = [];
	/** @var array  */
	public $onBeforeUserDelete = [];
	/** @var array  */
	public $onAfterUserDelete = [];
	/** @var array  */
	public $onBeforeUserChangePassword = [];
	/** @var array  */
	public $onAfterUserChangePassword = [];


	/**
	 * UsermanagerFormsFactory constructor.
	 *
	 * @param EntityManager $em
	 * @param GridFactory   $gridFactory
	 * @param FormFactory   $formFactory
	 * @param LanguageHelper $languageHelper
	 */
	public function __construct(
		EntityManager $em,
		GridFactory $gridFactory,
		FormFactory $formFactory,
		LanguageHelper $languageHelper
	)
	{
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
		$this->languageHelper = $languageHelper;
	}

	/**
	 * @return UsersListControl
	 */
	public function createUsersList()
	{
		$usersListControl = new UsersListControl(
			$this->em,
			$this->gridFactory,
			$this->linkUserEdit,
			$this->linkUserChangePassword
		);
		$usersListControl->onBeforeUserSave = $this->onBeforeUserSave;
		$usersListControl->onAfterUserSave = $this->onAfterUserSave;
		$usersListControl->onBeforeUserDelete = $this->onBeforeUserDelete;
		$usersListControl->onAfterUserDelete = $this->onAfterUserDelete;
		return $usersListControl;
	}


	/**
	 * @return UserEditControl
	 */
	public function createUserEdit()
	{
		$userEditControl = new UserEditControl(
			$this->em,
			$this->gridFactory,
			$this->formFactory,
			$this->languageHelper,
			$this->linkUsersList,
			$this->linkUserChangePassword
		);
		$userEditControl->onBeforeUserSave = $this->onBeforeUserSave;
		$userEditControl->onAfterUserSave = $this->onAfterUserSave;
		$userEditControl->onBeforeUserDelete = $this->onBeforeUserDelete;
		$userEditControl->onAfterUserDelete = $this->onAfterUserDelete;
		return $userEditControl;
	}


	public function createUserChangePassword()
	{
		$userEditControl = new UserChangePasswordControl(
			$this->em,
			$this->formFactory,
			$this->languageHelper,
			$this->linkUsersList,
			$this->linkUserChangePassword
		);
		$userEditControl->onBeforeUserChangePassword = $this->onBeforeUserChangePassword;
		$userEditControl->onAfterUserChangePassword = $this->onAfterUserChangePassword;
		return $userEditControl;
	}

	/**
	 * @return string
	 */
	public function getLinkUsersList()
	{
		return $this->linkUsersList;
	}

	/**
	 * @param string $linkUsersList
	 */
	public function setLinkUsersList($linkUsersList)
	{
		$this->linkUsersList = $linkUsersList;
	}

	/**
	 * @return string
	 */
	public function getLinkUserEdit()
	{
		return $this->linkUserEdit;
	}

	/**
	 * @param string $linkUserEdit
	 */
	public function setLinkUserEdit($linkUserEdit)
	{
		$this->linkUserEdit = $linkUserEdit;
	}

	/**
	 * @return string
	 */
	public function getLinkUserChangePassword()
	{
		return $this->linkUserChangePassword;
	}

	/**
	 * @param string $linkUserChangePassword
	 */
	public function setLinkUserChangePassword($linkUserChangePassword)
	{
		$this->linkUserChangePassword = $linkUserChangePassword;
	}

}
