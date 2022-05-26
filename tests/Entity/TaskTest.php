<?php

namespace App\Tests\Unit\Entity;

use App\Entity\Task;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskTest extends KernelTestCase
{
	private Task $task;
	private User $user;
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
		return (new Task())
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
		return $user = (new User)
			->setEmail('john.doe@email.com')
			->setUsername('JohnDoe')
			->setPassword('password')
			->setRoles(['ROLE_ADMIN'])
			->addTask($this->task);
	}

	/**
	 * Check constraint validation errors
	 *
	 * @param \App\Entity\Task $task
	 * @param integer $expectedErrors
	 *
	 * @return void
	 */
	public function assertPropertyConstraint(Task $task, int $expectedErrors)
	{
		self::bootKernel();
		$errors = $this->getContainer()->get(ValidatorInterface::class)->validate($task);
		$errorMessages = [];

		/** @var ConstraintViolation $error */
		foreach ($errors as $error) {
			$errorMessages[] = $error->getPropertyPath() . ' => ' . $error->getMessage();
		}

		$this->assertCount($expectedErrors, $errors, implode(', ', $errorMessages));
	}

	public function testValidEntity()
	{
		$this->assertPropertyConstraint($this->task, 0);
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
		$this->assertSame($this->task->getAuthorUsername(), 'JohnDoe');
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
		$this->assertNotSame($this->task->getAuthorUsername(), 'John Doe');
		$this->assertNotSame($this->task->getAuthor(), 'John Doe');
		$this->assertNotSame($this->task->isDone(), true);
		$this->assertNotSame($this->task->isDone(), $this->task->toggle(!$this->task->isDone()));
		$this->assertNotSame($this->task->getCreatedAt(), new \DateTime());
	}

	/**
	 * Test getters and setters with empty values
	 *
	 * @return void
	 */
	public function testResultOnEmptyPropertyValue()
	{
		$task = new Task();

		$this->assertEmpty($task->getId());
		$this->assertEmpty($task->getTitle());
		$this->assertNull($task->getContent());
		$this->assertNotEmpty($task->getAuthorUsername());
		$this->assertNull($task->getAuthor());
		$this->assertFalse($this->task->isDone());
		$this->assertNotEmpty($this->task->getCreatedAt());
	}
}
