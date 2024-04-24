<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class RegisterUserTest extends WebTestCase
{
    public function testSomething(): void
    {
        $client = static::createClient();
        $client->request('GET', '/inscription');
        $client->submitForm('Valider', [
            'register[firstname]' => 'kallo',
            'register[lastname]' => 'kaba',
            'register[email]' => 'kaba@gmail.com',
            'register[plainPassword][first]' => '11111',
            'register[plainPassword][second]' => '11111',
        ]);

        $this->assertResponseRedirects('/login');

        $client->followRedirect();

        $this->assertSelectorTextContains('.alert', 'Votre compte a bien été créé');
    }
}
