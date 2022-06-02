<?php

namespace App\Tests\Entity;

use App\Entity\Task;
use App\Entity\User;
use App\Tests\Traits\CustomAssert;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class TaskTest extends KernelTestCase
{
	use CustomAssert;

	private Task $task;
	private User $user;

	public function setUp(): void
	{
		$this->task = $this->getTaskEntity();
		$this->user = $this->getUserEntity();
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
			->setEmail('john.wick@email.com')
			->setUsername('John.Wick')
			->setPassword('password')
			->setRoles(['ROLE_ADMIN'])
			->addTask($this->task);
	}

	public function testValidTask()
	{
		$this->assertPropertyConstraint($this->task, 0);
	}

	public function testValidBlankAuthor()
	{
		$this->assertPropertyConstraint($this->task->setAuthor(null), 0);
	}

	public function testInvalidBlankTitle()
	{
		$this->assertPropertyConstraint($this->task->setTitle(''), 2);
	}

	public function testInvalidBlankContent()
	{
		$this->assertPropertyConstraint($this->task->setContent(''), 1);
	}

	/**
	 * Test invalid title length (min: 3 / max: 64)
	 *
	 * @return void
	 */
	public function testInvalidLengthTitle()
	{
		$this->assertPropertyConstraint($this->task->setTitle('Hi'), 1);
		$this->assertPropertyConstraint($this->task->setTitle(
			'Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean.'
		), 1);
	}

	/**
	 * Test getters and setters with expected values
	 *
	 * @return void
	 */
	public function testResultOnExpectedPropertyValue()
	{
		$this->assertSame($this->task->getId(), null);
		$this->assertSame($this->task->getTitle(), 'Task title');
		$this->assertSame($this->task->getContent(), 'Task content');
		$this->assertSame($this->task->getAuthorUsername(), 'John.Wick');
		$this->assertNotEmpty($this->task->getAuthor());
		$this->assertFalse($this->task->isDone());
		$this->assertNotEmpty($this->task->getCreatedAt());
	}

	/**
	 * Test getters and setters with unexpected values
	 *
	 * @return void
	 */
	public function testResultOnUnexpectedPropertyValue()
	{
		$this->assertNotSame($this->task->getId(), 1);
		$this->assertNotSame($this->task->getTitle(), 'Wrong title');
		$this->assertNotSame($this->task->getContent(), 'Wrong content');
		$this->assertNotSame($this->task->getAuthorUsername(), 'John Wick');
		$this->assertNotSame($this->task->getAuthor(), 'John Wick');
		$this->assertNotSame($this->task->isDone(), true);
		$this->assertNotSame($this->task->isDone(), $this->task->toggle(!$this->task->isDone()));
		$this->assertNotSame($this->task->getCreatedAt(), new \DateTime());
	}

	/**
	 * Test getters and setters with empty Task
	 *
	 * @return void
	 */
	public function testResultOnEmptyTask()
	{
		$task = new Task();

		$this->assertEmpty($task->getId());
		$this->assertEmpty($task->getTitle());
		$this->assertNull($task->getContent());
		$this->assertSame($task->getAuthorUsername(), 'Anonyme');
		$this->assertNull($task->getAuthor());
		$this->assertFalse($this->task->isDone());
		$this->assertNotEmpty($this->task->getCreatedAt());
	}
}
