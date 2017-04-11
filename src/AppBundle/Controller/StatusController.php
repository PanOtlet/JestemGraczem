<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use AppBundle\Model\phpTools;

class StatusController extends Controller
{
    /**
     * @Route("/", name="status", options={"sitemap" = true})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Statusy serwerów gier :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Statusy serwerów gier multiplayer i popularnych platform!')
            ->addMeta('property', 'og:title', 'Statusy serwerów gier :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('status', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('Status/index.html.twig', [
            'color' => $this->color
        ]);
    }

    /**
     * @Route("/wow", name="status.wow", options={"sitemap" = true})
     * @return \Symfony\Component\HttpFoundation\Response
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
            'color' => $this->color,
            'data' => json_decode($data)
        ]);
    }

    /**
     * @Route("/lol", name="status.lol", options={"sitemap" = true})
     * @return \Symfony\Component\HttpFoundation\Response
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
            'color' => $this->color,
            'eune' => json_decode($tools->getRemoteData($eune)),
            'euw' => json_decode($tools->getRemoteData($euw))
        ]);
    }

    /**
     * @Route("/steam", name="status.steam", options={"sitemap" = true})
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function steamStatusAction()
    {

        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Steam Status :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Status serwerów Steam!')
            ->addMeta('property', 'og:title', 'Steam Status :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('status.steam', [], UrlGeneratorInterface::ABSOLUTE_URL));

        return $this->render('Status/steam.html.twig', [
            'color' => $this->color
        ]);
    }
}
