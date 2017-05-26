<?php

namespace Mepatek\UserManager\UI;


use Kdyby\Doctrine\EntityManager;

use Mepatek\Components\UI\FormFactory;
use Mepatek\Components\UI\GridFactory;
use Mepatek\UserManager\UI\Users\UserEditControl;
use Mepatek\UserManager\UI\Users\UsersListControl;

class UsermanagerFormsFactory
{

	/** @var EntityManager */
	private $em;
	/** @var GridFactory */
	private $gridFactory;
	/** @var FormFactory */
	private $formFactory;
	/** @var string */
	private $linkUsersList;
	/** @var string */
	private $linkUserEdit;
	/** @var string */
	private $linkUserChangePassword;


	/**
	 * UsermanagerFormsFactory constructor.
	 *
	 * @param EntityManager $em
	 * @param GridFactory   $gridFactory
	 * @param FormFactory   $formFactory
	 */
	public function __construct(EntityManager $em, GridFactory $gridFactory, FormFactory $formFactory)
	{
		$this->em = $em;
		$this->gridFactory = $gridFactory;
		$this->formFactory = $formFactory;
	}

	public function createUsersList()
	{
		$userList = new UsersListControl(
			$this->em,
			$this->gridFactory,
			$this->linkUserEdit,
			$this->linkUserChangePassword
		);
		return $userList;
	}


	public function createUserEdit()
	{
		$userList = new UserEditControl(
			$this->em,
			$this->formFactory,
			$this->linkUsersList,
			$this->linkUserChangePassword
		);
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
	public function setLinkUsersList( $linkUsersList)
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
	public function setLinkUserEdit( $linkUserEdit)
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
	public function setLinkUserChangePassword( $linkUserChangePassword)
	{
		$this->linkUserChangePassword = $linkUserChangePassword;
	}

}
