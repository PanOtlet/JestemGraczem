<?php

namespace GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class LoLController extends Controller
{

    /**
     * @Route("/", name="centrum.lol")
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('League of Legends - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum gracza League of Legends! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'League of Legends - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.lol', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'http://eune.leagueoflegends.com/pl/rss.xml',
                'name' => 'Oficjalny'
            ],
            [
                'url' => 'http://cybersport.pl/category/gry/league-of-legends/feed/',
                'name' => 'Cybersport'
            ],
            [
                'url' => 'http://mmo24.pl/gry/lol/feed/',
                'name' => 'MMO24'
            ]
        ];

        return $this->render('gameCenter/lol.html.twig', [
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}