<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Model\phpTools;
use Symfony\Component\HttpFoundation\Response;

class LoLController extends Controller
{
    private $api = 'dc0b664f-8284-4732-b680-63e418a0ecc7';

    /**
     * @Route("/free", name="api.lol.free")
     */
    public function freeAction()
    {
        $tools = new phpTools();
        $response = $tools->getRemoteData('https://eune.api.pvp.net/api/lol/eune/v1.2/champion?freeToPlay=true&api_key=' . $this->api);

        return new Response(
            $response,
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

    /**
     * @Route("/champions", name="api.lol.champions")
     */
    public function championsAction()
    {
        $tools = new phpTools();
        $response = $tools->getRemoteData('https://eune.api.pvp.net/api/lol/static-data/eune/v1.2/champion?locale=pl_PL&dataById=true&api_key=' . $this->api);

        return new Response(
            $response,
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
