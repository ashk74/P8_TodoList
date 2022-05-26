<?php

namespace App\Tests\Functional\Controller;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityControllerTest extends WebTestCase
{
    private KernelBrowser|null $client = null;
    private $urlGenerator;

    public function setUp(): void
    {
        $this->client = static::createClient();
        $this->urlGenerator = $this->client->getContainer()->get('router.default');
    }

    // Une bonne manière de commencer est de vous assurer que toutes les pages de votre site web ne retournent pas de code status 500 !

    public function testLoginIsUp(): void
    {
        $this->client->request('GET', '/login');

        $this->assertResponseIsSuccessful();
        $this->assertSelectorTextContains('.navbar-brand', 'To Do List app');
    }

    public function testLoginWithBadCredentials(): void
    {
        $crawler = $this->client->request(Request::METHOD_GET, $this->urlGenerator->generate('login'));

        $form = $crawler->selectButton('Se connecter')->form([
            '_username' => 'AshK74',
            '_password' => '$2y$13$mIyb/qU0QZIm2T5CIa9e9eS8HYUibDccxPE72tH.dj/4yxDwAnglC'
        ]);

        $this->client->submit($form);
        $this->assertResponseRedirects('/login');
        $this->client->followRedirect();
        $this->assertSelectorExists('.alert.alert-danger');
    }
}
