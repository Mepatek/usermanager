<?php

namespace Mepatek\UserManager\UI;


use Mepatek\UserManager\Repository\UserRepository;
use Mepatek\UserManager\UI\Users\UsersListControl;

class UsermanagerFormsFactory
{

	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * UsermanagerFormsFactory constructor.
	 *
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function createUsersList()
	{
		$userList = new UsersListControl($this->userRepository);
		return $userList;
	}

}
