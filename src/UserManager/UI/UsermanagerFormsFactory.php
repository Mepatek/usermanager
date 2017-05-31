<?php

namespace Mepatek\UserManager\UI;


use Kdyby\Doctrine\EntityManager;

use Mepatek\Components\International\LanguageHelper;
use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
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
		$userList = new UsersListControl(
			$this->em,
			$this->gridFactory,
			$this->linkUserEdit,
			$this->linkUserChangePassword
		);
		$userList->onBeforeUserSave = $this->onBeforeUserSave;
		$userList->onAfterUserSave = $this->onAfterUserSave;
		$userList->onBeforeUserDelete = $this->onBeforeUserDelete;
		$userList->onAfterUserDelete = $this->onAfterUserDelete;
		return $userList;
	}


	/**
	 * @return UserEditControl
	 */
	public function createUserEdit()
	{
		$userList = new UserEditControl(
			$this->em,
			$this->formFactory,
			$this->languageHelper,
			$this->linkUsersList,
			$this->linkUserChangePassword
		);
		$userList->onBeforeUserSave = $this->onBeforeUserSave;
		$userList->onAfterUserSave = $this->onAfterUserSave;
		$userList->onBeforeUserDelete = $this->onBeforeUserDelete;
		$userList->onAfterUserDelete = $this->onAfterUserDelete;
		return $userList;
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
