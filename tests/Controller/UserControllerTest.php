<?php

namespace App\Tests\Controller;

use App\DataFixtures\UserFixtures;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liip\TestFixturesBundle\Services\DatabaseToolCollection;
use Liip\TestFixturesBundle\Services\DatabaseTools\AbstractDatabaseTool;

class UserControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    protected AbstractDatabaseTool $databaseTool;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->databaseTool = static::getContainer()->get(DatabaseToolCollection::class)->get();
        $this->databaseTool->loadFixtures([UserFixtures::class]);
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
        $user = (static::getContainer()->get(UserRepository::class))->findOneByUsername('John.Doe');

        if ($isAdmin) $user->setRoles(['ROLE_ADMIN']);

        $this->client->loginUser($user);
    }

    public function usersUri()
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
    public function testDisplayUsersListNotLogged($uri): void
    {
        $this->client->request('GET', $uri);

        $this->assertResponseRedirects('http://localhost/login', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('h1', 'Connexion');
    }

    // TODO Check redirection errors to users logged without admin roles

    /**
     * @dataProvider usersUri
     */
    public function testDisplayUsersListAsAdmin($uri): void
    {
        $this->connectUser(true);
        $this->client->request('GET', $uri);

        $this->assertResponseIsSuccessful();
    }

    public function testCreateUserAsAdmin(): void
    {
        // Récupérer un utilisateur
        // Ajouter le ROLE_ADMIN
        // Se connecter
        $user = (static::getContainer()->get(UserRepository::class))->findOneByUsername('John.Doe');
        $user->setRoles(['ROLE_ADMIN']);
        $this->client->loginUser($user);

        // Faire une requête sur la page user_create
        $this->client->request('GET', '/users/create');

        // Code status = 200
        $this->assertResponseIsSuccessful();

        // Séléctionner le bouton du formulaire
        // Envoyer le formulaire
        $this->client->submitForm('Ajouter', [
            'user[username]' => 'John.Wick',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'johnwick@email.com',
            'user[isAdmin]' => 0,
        ]);

        // Vérifier que la redirection renvoie vers la liste des utilisateurs
        // Code status 302
        $this->assertResponseRedirects('/users', Response::HTTP_FOUND);

        // Suivre la redirection
        $this->client->followRedirect();

        // Code status = 200
        $this->assertResponseIsSuccessful();

        // Récupérer le message confirmant qu'un nouvel utilisateur à été ajouter
        $this->assertSelectorExists('.alert.alert-success');
    }

    public function testEditUserAsAdmin(): void
    {
        $this->client->request('GET', '/users/1/edit');

        $this->client->submitForm('Modifier', [
            'user[username]' => 'John.Wick',
            'user[password][first]' => 'password',
            'user[password][second]' => 'password',
            'user[email]' => 'johnwick@email.com',
            'user[isAdmin]' => 0,
        ]);

        $this->assertResponseRedirects('/users', Response::HTTP_FOUND);
        $this->client->followRedirect();
        $this->assertResponseIsSuccessful();
        $this->assertSelectorExists('.alert.alert-success');
    }
}
