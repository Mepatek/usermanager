<?php

namespace Mepatek\UserManager\UI\Users;


use Mepatek\Components\Ublaboo\DataSources\RepositorySource;
use Mepatek\UserManager\Repository\UserRepository;
use Nette\Application\UI\Control;
use Ublaboo\DataGrid\DataGrid;

class UsersListControl extends Control
{

	/**
	 * @var UserRepository
	 */
	private $userRepository;

	/**
	 * UsersListControl constructor.
	 *
	 * @param UserRepository $userRepository
	 */
	public function __construct(UserRepository $userRepository)
	{
		$this->userRepository = $userRepository;
	}

	public function render()
	{
		$template = $this->template;

//		if (isset($this->parent->translator)) {
//			$template->setTranslator($this->parent->translator);
//		}


		$template->render(__DIR__ . '/UsersListControl.latte');
	}


	/**
	 *
	 */
	public function createComponentUsersListGrid($name)
	{
		$grid = new DataGrid($this, $name);
		$data = new RepositorySource( $this->userRepository, "id");
		$grid->setDataSource($data);

		$grid->addColumnText("fullName", "Full name");

		return $grid;

	}
}
