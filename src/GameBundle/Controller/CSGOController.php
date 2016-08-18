<?php

namespace GameBundle\Controller;

use GameBundle\GameBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CSGOController extends Controller
{

    /**
     * @Route("/", name="centrum.csgo")
     */
    public function indexAction()
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
            'color' => GameBundle::getColor(),
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
