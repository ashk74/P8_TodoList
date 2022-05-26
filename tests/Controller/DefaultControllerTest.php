<?php

namespace App\Tests\Functional\Controller;

use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class DefaultControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
    }

    public function testHomepageNotLogged(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('http://localhost/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertSelectorTextContains('.navbar-brand', 'To Do List app');
    }

    public function testHomepageLogged(): void
    {
        $user = ($this->getContainer()->get(UserRepository::class))->findOneBy(['username' => 'John.Doe']);
        $this->databaseTool->loadAliceFixture(['/./.' . dirname(__DIR__) . '/Fixtures/UserFixtures.yaml']);
        $this->client->loginUser($user);
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
    }
}
