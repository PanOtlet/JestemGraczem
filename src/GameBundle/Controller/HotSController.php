<?php

namespace GameBundle\Controller;

use GameBundle\GameBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HotSController extends Controller
{

    /**
     * @Route("/", name="centrum.hots")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function hotsAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Heroes of the Storm - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum każdego gracza Heroes of the Storm! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'Heroes of the Storm - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.hots', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'http://eu.battle.net/heroes/pl/feed/news',
                'name' => 'Battle.net'
            ],
            [
                'url' => 'https://hotscenter.pl/rss',
                'name' => 'HotSCenter.pl'
            ],
            [
                'url' => 'http://mmo24.pl/tag/hots-polska/feed/',
                'name' => 'MMO24.pl'
            ]
        ];

        return $this->render('gameCenter/hots.html.twig', [
            'color' => GameBundle::getColor(),
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
