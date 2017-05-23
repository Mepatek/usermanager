<?php

namespace Mepatek\UserManager\UI;


use Kdyby\Doctrine\EntityManager;

use Mepatek\UserManager\UI\Users\UsersListControl;

class UsermanagerFormsFactory
{

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * UsermanagerFormsFactory constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

	public function createUsersList()
	{
		$userList = new UsersListControl($this->em);
		return $userList;
	}

}
