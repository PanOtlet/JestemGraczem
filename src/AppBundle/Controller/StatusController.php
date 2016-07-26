<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
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

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Statusy serwerów gier :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Statusy serwerów gier multiplayer i popularnych platform!')
            ->addMeta('property', 'og:title', 'Statusy serwerów gier :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('status', [], UrlGeneratorInterface::ABSOLUTE_URL));

        if (!$tools->isJson($steam)) {
            return $this->render('Status/index.html.twig', []);
        }

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

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('WoW Realms Server Status :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Statusy serwerów do gry World of Warcraft Europe!')
            ->addMeta('property', 'og:title', 'WoW Realms Server Status :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('status.wow', [], UrlGeneratorInterface::ABSOLUTE_URL));

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

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Statusy serwerów League of Legends :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Statusy serwerów League of Legends!')
            ->addMeta('property', 'og:title', 'Statusy serwerów League of Legends :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('status.lol', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('Status/lol.html.twig', [
            'eune' => json_decode($tools->getRemoteData($eune)),
            'euw' => json_decode($tools->getRemoteData($euw))
        ]);
    }

}
