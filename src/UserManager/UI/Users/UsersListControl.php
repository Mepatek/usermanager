<?php

namespace Mepatek\UserManager\UI\Users;


use App\Mepatek\UserManager\Entity\User;
use Kdyby\Doctrine\EntityManager;
use Nette\Application\UI\Control;
use Ublaboo\DataGrid\DataGrid;
use Ublaboo\DataGrid\DataSource\DoctrineDataSource;

class UsersListControl extends Control
{

	/**
	 * @var EntityManager
	 */
	private $em;

	/**
	 * UsersListControl constructor.
	 *
	 * @param EntityManager $em
	 */
	public function __construct(EntityManager $em)
	{
		$this->em = $em;
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
		$qb = $this->em->createQueryBuilder("users")
			->select(User::class)
		;
		$data = new DoctrineDataSource( $qb, "id");
		$grid->setDataSource($data);

		$grid->addColumnText("fullName", "Full name");

		return $grid;

	}
}
