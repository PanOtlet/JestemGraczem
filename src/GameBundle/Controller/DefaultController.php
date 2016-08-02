<?php

namespace GameBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="centrum")
     */
    public function indexAction()
    {
        return $this->render('gameCenter/index.html.twig', [
            'games' => [
                [
                    'title' => 'Promocje na gry',
                    'url' => 'centrum.cebula',
                    'img' => 'cebula'
                ],
                [
                    'title' => 'League of Legends',
                    'url' => 'centrum.lol',
                    'img' => 'lol'
                ],
                [
                    'title' => 'Counter-Strike: Global Offensive',
                    'url' => 'centrum.csgo',
                    'img' => 'csgo'
                ],
                [
                    'title' => 'Heroes of the Storm',
                    'url' => 'centrum.hots',
                    'img' => 'hots'
                ],
                [
                    'title' => 'World of Warcraft',
                    'url' => 'centrum.wow',
                    'img' => 'wow'
                ],
                [
                    'title' => 'Overwatch',
                    'url' => 'centrum.overwatch',
                    'img' => 'overwatch'
                ],
                [
                    'title' => 'Hearthstone',
                    'url' => 'centrum.hs',
                    'img' => 'hs'
                ],
                [
                    'title' => 'World of Tanks',
                    'url' => 'centrum.wot',
                    'img' => 'wot'
                ],
                [
                    'title' => 'Grand Theft Auto',
                    'url' => 'centrum.gta',
                    'img' => 'gta'
                ],
                [
                    'title' => 'DotA2',
                    'url' => 'centrum.dota',
                    'img' => 'dota'
                ],
                [
                    'title' => 'Pokemon',
                    'url' => 'centrum.pokemon',
                    'img' => 'pokemon'
                ]
            ]
        ]);
    }

    /**
     * @Route("/cebula", name="centrum.cebula")
     */
    public function cebulaAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Promocje na gry - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum każdego gracza chcącego grać po najniższych cenach! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'Promocje na gry - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.cebula', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'http://lowcygier.pl/feed/',
                'name' => 'LowcyGier.pl'
            ],
            [
                'url' => 'https://salenauts.com/pl/news/feed/',
                'name' => 'Salenauts'
            ]
        ];

        return $this->render('gameCenter/cebula.html.twig', [
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
