<?php

namespace App\Tests\Controller;

use App\DataFixtures\TaskFixtures;
use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class TaskControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class, TaskFixtures::class]);
    }

    /**
     * Retrieve a user and connect him
     *
     * @param bool $isAdmin Define user role
     *
     * @return void
     */
    private function connectUser(bool $isAdmin = false): void
    {
        $user = (static::getContainer()->get(UserRepository::class))->findOneBy(['username' => 'John.Doe']);

        if ($isAdmin) $user->setRoles(['ROLE_ADMIN']);

        $this->client->loginUser($user);
    }

    public function diplayableTasksUri()
    {
        return [
            ['/tasks'],
            ['/tasks/create'],
            ['/tasks/1/edit']
        ];
    }

    public function otherTasksUri()
    {
        return [
            ['/tasks/1/toggle'],
            ['/tasks/1/delete']
        ];
    }

    /**
     * Try to access each task URI without being logged in
     *
     * @param string $uri
     *
     * @return void
     *
     * @dataProvider diplayableTasksUri
     * @dataProvider otherTasksUri
     *
     */
    public function testAccessToTaskPagesNotLogged($uri): void
    {
        $this->client->request('GET', $uri);

        $this->assertResponseRedirects('http://localhost/login', Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextSame('h1', 'Connexion');
    }

    /**
     * @dataProvider diplayableTasksUri
     */
    public function testAccessToTaskPagesAsUser($uri): void
    {
        $this->connectUser();
        $this->client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }

    /**
     * @dataProvider diplayableTasksUri
     */
    public function testAccessToTaskPagesAsAdmin($uri): void
    {
        $this->connectUser(true);
        $this->client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }

    public function testCreateTaskLogged(): void
    {
        $this->connectUser();
        $this->client->request('GET', '/tasks/create');

        $this->client->submitForm('Ajouter', [
            'task[title]' => 'Hello',
            'task[content]' => 'Contenu',
        ]);

        $this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testEditTaskLogged(): void
    {
        $this->connectUser();
        $this->client->request('GET', '/tasks/1/edit');

        $this->client->submitForm('Modifier', [
            'task[title]' => 'Hello world',
            'task[content]' => 'Contenu modifié',
        ]);

        $this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testDeleteTaskAsAdmin(): void
    {
        $this->connectUser(true);
        $this->client->request('GET', '/tasks/1/delete');

        $this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testToggleTaskLogged(): void
    {
        $this->connectUser();
        $this->client->request('GET', '/tasks/1/toggle');

        $this->assertResponseRedirects('/tasks', Response::HTTP_FOUND);
        $this->client->followRedirect();

        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
