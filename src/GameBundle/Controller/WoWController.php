<?php

namespace GameBundle\Controller;

use GameBundle\GameBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class WoWController extends Controller
{

    /**
     * @Route("/", name="centrum.wow")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('World of Warcraft - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum każdego gracza World of Warcraft! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'World of Warcraft - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.wow', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'http://eu.battle.net/wow/pl/feed/news',
                'name' => 'Battle.net'
            ],
            [
                'url' => 'https://wowcenter.pl/rss',
                'name' => 'WoWCenter.pl'
            ]
        ];

        return $this->render('gameCenter/wow.html.twig', [
            'color' => GameBundle::getColor(),
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
