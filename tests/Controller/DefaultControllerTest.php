<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Tests\Traits\ConnectUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class DefaultControllerTest extends WebTestCase
{
    use ConnectUser;

    private KernelBrowser|null $client = null;
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class]);
    }

    public function testHomepageNotLogged(): void
    {
        $this->client->request('GET', '/');

        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    public function testHomepageAsUser(): void
    {
        $this->assertHomepageLogged();
    }

    public function testHomepageAsAdmin(): void
    {
        $this->assertHomepageLogged(true);
        $this->assertSelectorTextContains('.btn.btn-primary', 'Créer un utilisateur');
    }

    private function assertHomepageLogged(bool $isAdmin = false)
    {
        $this->connectUser($isAdmin);
        $this->client->request('GET', '/');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Bienvenue sur Todo List');
    }
}
