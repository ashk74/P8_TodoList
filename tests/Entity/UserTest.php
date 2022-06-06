<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\DataFixtures\UserFixtures;
use App\Tests\Traits\CustomAssert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserTest extends KernelTestCase
{
	use CustomAssert;

	private User $user;
	private Task $task;
	protected AbstractDatabaseTool $databaseTool;

	public function setUp(): void
	{
		$this->task = $this->getTaskEntity();
		$this->user = $this->getUserEntity();
		$this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
	}

	/**
	 * Get a valid Task entity
	 *
	 * @return \App\Entity\Task
	 */
	public function getTaskEntity(): Task
	{
		return (new Task)
			->setTitle('Task title')
			->setContent('Task content')
			->setCreatedAt(new \DateTime());
	}

	/**
	 * Get a valide user entity
	 *
	 * @return \App\Entity\User
	 */
	public function getUserEntity(): User
	{
		return (new User())
			->setEmail('arthur.pendragon@email.com')
			->setUsername('Arthur.Pendragon')
			->setPassword('password')
			->setRoles(['ROLE_ADMIN'])
			->addTask($this->task);
	}

	public function testValidUser()
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

	/**
	 * Test invalid username length (min: 3 / max: 25)
	 *
	 * @return void
	 */
	public function testInvalidLengthUsername()
	{
		$this->assertPropertyConstraint($this->user->setUsername('Jo'), 1);
		$this->assertPropertyConstraint($this->user->setUsername('Jooooooooooooooooooooooooo'), 1);
	}

	public function testInvalidBlankPassword()
	{
		$this->assertPropertyConstraint($this->user->setPassword(''), 1);
	}

	/**
	 * Test invalid password length (min: 8 / max: 64)
	 *
	 * @return void
	 */
	public function testInvalidLengthPassword()
	{
		$this->assertPropertyConstraint($this->user->setPassword('wrong'), 1);
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

	/**
	 * Test unique email constraint with fixtures
	 *
	 * @return void
	 */
	public function testInvalidUsedEmail()
	{
		$this->databaseTool->loadFixtures([UserFixtures::class]);
		$this->assertPropertyConstraint($this->user->setEmail('admin@email.com'), 1);
	}

	/**
	 * Test unique username constraint with fixtures
	 *
	 * @return void
	 */
	public function testInvalidUsedUsername()
	{
		$this->databaseTool->loadFixtures([UserFixtures::class]);
		$this->assertPropertyConstraint($this->user->setUsername('Admin'), 1);
	}

	/**
	 * Test getters and setters with expected values
	 *
	 * @return void
	 */
	public function testResultOnExpectedPropertyValue()
	{
		$this->assertSame($this->user->getId(), null);
		$this->assertSame($this->user->getEmail(), 'arthur.pendragon@email.com');
		$this->assertSame($this->user->getUserIdentifier(), 'Arthur.Pendragon');
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

	/**
	 * Test getters and setters with unexpected values
	 *
	 * @return void
	 */
	public function testResultOnUnexpectedPropertyValue()
	{
		$this->assertNotSame($this->user->getId(), 1);
		$this->assertNotSame($this->user->getEmail(), 'wrong@email.com');
		$this->assertNotSame($this->user->getUserIdentifier(), 'JohnDoe');
		$this->assertNotSame($this->user->getUsername(), 'JohnDoe');
		$this->assertNotSame($this->user->getPassword(), 'wrong-password');
		$this->assertNotContains('ROLE_SUPER_ADMIN', $this->user->getRoles());
		$this->assertNull($this->user->getSalt());
		$this->assertNotSame($this->user->getTasks(), 'Wrong task');
	}

	/**
	 * Test getters and setters with empty User
	 *
	 * @return void
	 */
	public function testResultOnEmptyUser()
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
