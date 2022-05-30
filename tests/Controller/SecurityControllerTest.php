<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class]);
    }

    public function testLoginIsUp(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testLoginWithBadCredentials(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'John.Doe',
            '_password' => 'wrong'
        ]);

        $this->assertResponseRedirects('http://localhost/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-danger');
    }

    public function testLoginWithGoodCredentials(): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            '_username' => 'John.Doe',
            '_password' => 'password'
        ]);

        $this->assertResponseRedirects('http://localhost/', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }
}
