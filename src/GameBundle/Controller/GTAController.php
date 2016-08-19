<?php

namespace GameBundle\Controller;

use GameBundle\GameBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GTAController extends Controller
{

    /**
     * @Route("/", name="centrum.gta")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Grand Theft Auto - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum każdego gracza Grand Theft Auto! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'Grand Theft Auto - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.gta', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'http://gtasite.net/rss.html'
            ]
        ];

        return $this->render('gameCenter/hs.html.twig', [
            'color' => GameBundle::getColor(),
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
