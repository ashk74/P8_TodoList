<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Tests\Traits\ConnectUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserControllerTest extends WebTestCase
{
    // Load ConnectUser Trait
    use ConnectUser;

    private KernelBrowser|null $client = null;
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class]);
    }

    /**
     * Create dataProvider to test all routes related to users
     *
     * @return array
     */
    public function usersUri(): array
    {
        return [
            ['/users'],
            ['/users/create'],
            ['/users/1/edit']
        ];
    }

    /**
     * @dataProvider usersUri
     */
    public function testRequestOnUsersRoutesNotLogged($uri): void
    {
        $this->client->request('GET', $uri);

        $this->assertResponseRedirects('/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    /**
     * @dataProvider usersUri
     */
    public function testRequestOnUsersRoutesAsAdmin($uri): void
    {
        $this->connectUser(true);
        $this->client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }

    public function testCreateUserAsAdmin(): void
    {
        $this->assertSuccessfullySubmitForm();
    }

    public function testEditUserAsAdmin(): void
    {
        $this->assertSuccessfullySubmitForm(true);
    }

    private function assertSuccessfullySubmitForm(bool $editMode = false)
    {
        $uri = $editMode ? '/users/2/edit' : '/users/create';
        $button = $editMode ? 'Modifier' : 'Ajouter';

        $this->connectUser(true);
        $this->client->request('GET', $uri);

        $this->client->submitForm($button, [
            'user[username]' => 'John.Doe',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'john.doe@email.com',
            'user[isAdmin]' => 0,
        ]);

        $this->assertResponseRedirects('/users', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
