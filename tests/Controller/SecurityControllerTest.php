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

    public function credentialsInformations(): array
    {
        return [
            ['Bad.Credentials', 'Wrong.Password', '/login'],
            ['User1', 'password', '/'],
            ['Admin', 'password', '/']
        ];
    }

    public function testLoginIsUp(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    /**
     * @dataProvider credentialsInformations
     */
    public function testLoginCredentials($username, $password, $uri): void
    {
        $this->client->request('GET', '/login');

        $this->client->submitForm('Se connecter', [
            'username' => $username,
            'password' => $password
        ]);

        $this->assertResponseRedirects($uri, Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();

        if ($uri === '/') {
            $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
        } else {
            $this->assertSelectorExists('.alert.alert-danger');
        }

        if ($username === 'Admin') {
            $this->assertSelectorTextContains('.btn.btn-primary', 'Créer un utilisateur');
        }
    }
}
