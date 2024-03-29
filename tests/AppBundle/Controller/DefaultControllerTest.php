<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class DefaultControllerTest extends WebTestCase
{

    public function testHomePage(){

        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(500,$client->getResponse()->getStatusCode());
    }

    public function testUser(){

        $client = static::createClient();
        $client->request('GET', '/u/otlet');

        $this->assertEquals(404,$client->getResponse()->getStatusCode());
    }

}
