<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GameCenterController extends Controller
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
                ],
                [
                    'title' => 'Fifa',
                    'url' => 'centrum.fifa',
                    'img' => 'fifa'
                ]
            ]
        ]);
    }

    /**
     * @Route("/lol", name="centrum.lol")
     */
    public function lolAction()
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

    /**
     * @Route("/csgo", name="centrum.csgo")
     */
    public function csgoAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('CounterStrike: Global Offensive - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum gracza CounterStrike: Global Offensive! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'CounterStrike: Global Offensive - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.csgo', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'http://blog.counter-strike.net/index.php/feed/',
                'name' => 'Oficjalny'
            ],
            [
                'url' => 'http://cybersport.pl/category/gry/counter-strike-global-offensive/feed/',
                'name' => 'Cybersport'
            ],
            [
                'url' => 'http://gocs.pl/feed/',
                'name' => 'GOCS'
            ]
        ];

        return $this->render('gameCenter/csgo.html.twig', [
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
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
                'url' => 'http://store.steampowered.com/feeds/news.xml',
                'name' => 'Steam'
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

    /**
     * @Route("/hots", name="centrum.hots")
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
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

    /**
     * @Route("/overwatch", name="centrum.overwatch")
     */
    public function overwatchAction()
    {
        return $this->render('gameCenter/overwatch.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/wow", name="centrum.wow")
     */
    public function wowAction()
    {
        return $this->render('gameCenter/wow.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/hs", name="centrum.hs")
     */
    public function hsAction()
    {
        return $this->render('gameCenter/hs.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/dota", name="centrum.dota")
     */
    public function dotaAction()
    {
        return $this->render('gameCenter/dota.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/pokemon", name="centrum.pokemon")
     */
    public function pokemonAction()
    {
        return $this->render('gameCenter/pokemon.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/gta", name="centrum.gta")
     */
    public function gtaAction()
    {
        return $this->render('gameCenter/gta.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/wot", name="centrum.wot")
     */
    public function wotAction()
    {
        return $this->render('gameCenter/wot.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fifa", name="centrum.fifa")
     */
    public function fifaAction()
    {
        return $this->render('gameCenter/fifa.html.twig', [
            // ...
        ]);
    }

}
