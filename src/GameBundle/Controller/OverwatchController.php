<?php

namespace GameBundle\Controller;

use GameBundle\GameBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class OverwatchController extends Controller
{

    /**
     * @Route("/", name="centrum.overwatch")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Overwatch - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum każdego gracza Overwatch! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'Overwatch - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.overwatch', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'https://overwatch.pl/feed',
                'name' => 'Overwatch.pl'
            ],
            [
                'url' => 'http://overwatchgame.pl/feed/',
                'name' => 'OverwatchGame.pl'
            ]
        ];

        return $this->render('gameCenter/overwatch.html.twig', [
            'color' => GameBundle::getColor(),
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
