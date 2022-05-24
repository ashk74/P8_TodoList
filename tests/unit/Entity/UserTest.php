<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserTest extends KernelTestCase
{
	private User $user;
	private Task $task;
	protected AbstractDatabaseTool $databaseTool;

	public function setUp(): void
	{
		$this->task = $this->getTaskEntity();
		$this->user = $this->getUserEntity();
		$this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
	}

	// Get a valid Task entity
	public function getTaskEntity(): Task
	{
		return (new Task)
			->setTitle('Task title')
			->setContent('Task content')
			->setCreatedAt(new \DateTime());
	}

	// Get a valide user entity
	public function getUserEntity(): User
	{
		return (new User())
			->setEmail('john.doe@email.com')
			->setUsername('JohnDoe')
			->setPassword('password')
			->setRoles(['ROLE_ADMIN'])
			->addTask($this->task);
	}

	// Check constraint validation and return error
	public function assertPropertyConstraint(User $user, int $expectedErrors)
	{
		self::bootKernel();
		$errors = $this->getContainer()->get(ValidatorInterface::class)->validate($user);
		$errorMessages = [];

		/** @var ConstraintViolation $error */
		foreach ($errors as $error) {
			$errorMessages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}

		$this->assertCount($expectedErrors, $errors, implode(', ', $errorMessages));
	}

	public function testValidEntity()
	{
		$this->assertPropertyConstraint($this->user, 0);
	}

	public function testInvalidBlankEmail()
	{
		$this->assertPropertyConstraint($this->user->setEmail(''), 1);
	}

	public function testInvalidEmail()
	{
		$this->assertPropertyConstraint($this->user->setEmail('johndoe.fr'), 1);
	}

	public function testInvalidBlankUsername()
	{
		$this->assertPropertyConstraint($this->user->setUsername(''), 1);
	}

	// Test invalid username length
	public function testInvalidLengthUsername()
	{
		// Smaller than 3
		$this->assertPropertyConstraint($this->user->setUsername('Jo'), 1);
		// Longer than 25
		$this->assertPropertyConstraint($this->user->setUsername('Jooooooooooooooooooooooooo'), 1);
	}

	public function testInvalidBlankPassword()
	{
		$this->assertPropertyConstraint($this->user->setPassword(''), 1);
	}

	// Test invalid password length
	public function testInvalidLengthPassword()
	{
		// Smaller than 8
		$this->assertPropertyConstraint($this->user->setPassword('wrong'), 1);
		// Longer than 64
		$this->assertPropertyConstraint($this->user->setPassword(
			'VMKoJewNDZE#9kitn^zhZfzv96HBirAYPYi5DE7csZqrk!eJ^xU4dk7JGzN&5j2iy'
		), 1);
	}

	public function testValidBlankRoles()
	{
		$this->assertPropertyConstraint($this->user->setRoles(['']), 0);
	}

	public function testInvalidAddEmptyTask()
	{
		$this->assertPropertyConstraint($this->user->addTask(new Task), 2);
	}

	public function testValidRemoveTask()
	{
		$this->assertNotEmpty($this->user->getTasks());
		$this->user->removeTask($this->task);
		$this->assertEmpty($this->user->getTasks());
	}

	// Test unique email with fixtures
	public function testInvalidUsedEmail()
	{
		$this->databaseTool->loadAliceFixture([dirname(__DIR__) . '/Fixtures/UserFixtures.yaml']);
		$this->assertPropertyConstraint($this->user->setEmail('johndoe@email.com'), 1);
	}

	// Test unique username with fixtures
	public function testInvalidUsedUsername()
	{
		$this->databaseTool->loadAliceFixture([dirname(__DIR__) . '/Fixtures/UserFixtures.yaml']);
		$this->assertPropertyConstraint($this->user->setUsername('John,Doe'), 1);
	}

	// Test getters and setters expected values
	public function testResultOnExpectedPropertyValue()
	{
		$this->assertSame($this->user->getId(), null);
		$this->assertSame($this->user->getEmail(), 'john.doe@email.com');
		$this->assertSame($this->user->getUserIdentifier(), 'JohnDoe');
		$this->assertSame($this->user->getUsername(), $this->user->getUserIdentifier());
		$this->assertSame($this->user->getPassword(), 'password');
		$this->assertContains('ROLE_USER', $this->user->getRoles());
		$this->assertContains('ROLE_ADMIN', $this->user->getRoles());
		$this->assertNull($this->user->getSalt());
		$this->assertNotEmpty($this->user->getTasks());
		// Remove Task and check value
		$this->user->removeTask($this->task);
		$this->assertEmpty($this->user->getTasks());
	}

	// Test getters and setters unexpected values
	public function testResultOnUnexpectedPropertyValue()
	{
		$this->assertNotSame($this->user->getId(), 1);
		$this->assertNotSame($this->user->getEmail(), 'wrong@email.com');
		$this->assertNotSame($this->user->getUserIdentifier(), 'John.Doe');
		$this->assertNotSame($this->user->getUsername(), 'John.Doe');
		$this->assertNotSame($this->user->getPassword(), 'wrong-password');
		$this->assertNotContains('ROLE_SUPER_ADMIN', $this->user->getRoles());
		$this->assertNull($this->user->getSalt());
		$this->assertNotSame($this->user->getTasks(), 'Wrong task');
	}

	// Test getters and setters empty values
	public function testResultOnEmptyPropertyValue()
	{
		$user = new User();

		$this->assertEmpty($user->getId());
		$this->assertEmpty($user->getEmail());
		$this->assertEmpty($user->getUserIdentifier());
		$this->assertEmpty($user->getUsername());
		$this->assertNull($user->getPassword());
		$this->assertContains('ROLE_USER', $user->getRoles());
		$this->assertEmpty($user->getTasks());
		$this->assertEmpty($user->getSalt());
	}
}
