<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use AppBundle\Model\phpTools;

class StatusController extends Controller
{
    /**
     * @Route("/", name="status")
     */
    public function indexAction()
    {
        return $this->render('Status/index.html.twig', [

        ]);
    }
    /**
     * @Route("/wow", name="status.wow")
     */
    public function wowStatusAction()
    {
        $tools = new phpTools();
        $url = "https://eu.api.battle.net/wow/realm/status?locale=pl_PL&apikey=2u6schdhm434ng9uptswp8zjcf84267e";

        $data = $tools->get_remote_data($url);

        return $this->render('Status/wow.html.twig', [
            'data' => json_decode($data)
        ]);
    }

}
