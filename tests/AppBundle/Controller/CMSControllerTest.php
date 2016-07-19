<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class CMSControllerTest extends WebTestCase
{

    public function testCMS(){

        $client = static::createClient();
        $client->request('GET', '/cms/regulamin');

        $this->assertEquals(200,$client->getResponse()->getStatusCode());
    }

}
