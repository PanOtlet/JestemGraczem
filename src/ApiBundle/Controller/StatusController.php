<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Model\phpTools;

class StatusController extends Controller
{
    /**
     * @Route("/steam", name="api.steam")
     */
    public function steamAction()
    {
        $tools = new phpTools();
        $status = $tools->getRemoteData('https://crowbar.steamdb.info/Barney');

        return new Response(
            $status,
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

}
