<?php

namespace Test;

use Mepatek\UserManager\Authenticator;
use Mepatek\UserManager\Repository\RoleRepository;
use Mepatek\UserManager\Repository\UserActivityRepository;
use Mepatek\UserManager\Repository\UserRepository;
use Nette,
	Tester,
	Tester\Assert;

require __DIR__ . '/bootstrap.php';

class AuthenticatorTest extends Tester\TestCase
{
	private $database;

	/** @var UserRepository */
	protected $userRepository;
	/** @var UserActivityRepository */
	protected $userActivityRepository;
	/** @var RoleRepository */
	protected $roleRepository;
	/** @var Authenticator */
	protected $authenticator;

	function setUp()
	{
		$connection = new \Nette\Database\Connection(
			"sqlite:" . __DIR__ . "/data/UserManager.db",
			null, null
		);
		$structure = new \Nette\Database\Structure($connection, new \Nette\Caching\Storages\FileStorage(TEMP_DIR));
		$conventions = new \Nette\Database\Conventions\DiscoveredConventions($structure);
		$this->database = new \Nette\Database\Context($connection, $structure, $conventions);

		$mapper = new \Mepatek\UserManager\Mapper\UserNetteDatabaseMapper($this->database);
		$this->userRepository = new \Mepatek\UserManager\Repository\UserRepository($mapper);
		$mapper = new \Mepatek\UserManager\Mapper\UserActivityNetteDatabaseMapper($this->database);
		$this->userActivityRepository = new \Mepatek\UserManager\Repository\UserActivityRepository($mapper);
		$mapper = new \Mepatek\UserManager\Mapper\RoleNetteDatabaseMapper($this->database);
		$this->roleRepository = new \Mepatek\UserManager\Repository\RoleRepository($mapper);

		$this->authenticator = new Authenticator(
			$this->userRepository, $this->roleRepository, $this->userActivityRepository
		);
	}


	function testRandomPassword()
	{

		for ($length = 6; $length <= 16; $length++) {
			for ($i = 1; $i <= 4; $i++) {
				$newPassword = $this->authenticator->generateRandomPassword($length, $i);
				Assert::same($length, strlen($newPassword));
				Assert::same(0, $this->authenticator->isPasswordSafe($newPassword, $length, $i));
			}
		}
	}
}


$test = new AuthenticatorTest();
$test->run();
