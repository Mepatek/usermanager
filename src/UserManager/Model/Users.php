<?php
/**
 * Created by PhpStorm.
 * User: pepa
 * Date: 06.08.2017
 * Time: 15:54
 */

namespace Mepatek\UserManager\Model;


use Doctrine\ORM\EntityManager;

class Users
{

	/** @var EntityManager */
	private $em;

	public function __construct(EntityManager $em)
	{
		$this->em = $em;
	}

}
