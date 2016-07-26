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
        $tools = new phpTools();
        $steam = "http://api.steampowered.com/ISteamWebAPIUtil/GetServerInfo/v0001/";

        $steam = $tools->getRemoteData($steam);

        return $this->render('Status/index.html.twig', [
            'steam' => json_decode($steam)
        ]);
    }

    /**
     * @Route("/wow", name="status.wow")
     */
    public function wowStatusAction()
    {
        $tools = new phpTools();
        $url = "https://eu.api.battle.net/wow/realm/status?locale=pl_PL&apikey=2u6schdhm434ng9uptswp8zjcf84267e";

        $data = $tools->getRemoteData($url);

        return $this->render('Status/wow.html.twig', [
            'data' => json_decode($data)
        ]);
    }

    /**
     * @Route("/lol", name="status.lol")
     */
    public function lolStatusAction()
    {
        $tools = new phpTools();
        $eune = "http://status.leagueoflegends.com/shards/eune";
        $euw = "http://status.leagueoflegends.com/shards/euw";

        return $this->render('Status/lol.html.twig', [
            'eune' => json_decode($tools->getRemoteData($eune)),
            'euw' => json_decode($tools->getRemoteData($euw))
        ]);
    }

}
