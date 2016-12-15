<?php

namespace GameBundle\Controller;

use GameBundle\GameBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class HearthstoneController extends Controller
{

    /**
     * @Route("/", name="centrum.hs")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Hearthstone: Heroes of Warcraft - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum każdego gracza Hearthstone: Heroes of Warcraft! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'Hearthstone: Heroes of Warcraft - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.hs', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'http://eu.battle.net/hearthstone/pl/feed/news'
            ]
        ];

        return $this->render($this->getParameter('theme') . '/gameCenter/hs.html.twig', [
            'color' => GameBundle::getColor(),
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
