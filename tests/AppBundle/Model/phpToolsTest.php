<?php

namespace Tests\AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use AppBundle\Model\phpTools;

class phpToolsTest extends WebTestCase
{

    public function testGetRemoteData(){
        $tools = new phpTools();

        $tools->getRemoteData('http://google.com');
    }

}
