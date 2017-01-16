<?php

namespace GameBundle\Controller;

use GameBundle\GameBundle;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DotaController extends Controller
{

    /**
     * @Route("/", name="centrum.dota")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('DotA2 - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum każdego gracza DotA2! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'DotA2 - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('centrum.dota', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $feeds = [
            [
                'url' => 'https://dota2.pl/feed/',
                'name' => 'DotA2.pl'
            ],
            [
                'url' => 'http://dota2tv.pl/feed/',
                'name' => 'DotA2TV.pl'
            ]
        ];

        return $this->render($this->getParameter('theme') . '/gameCenter/dota.html.twig', [
            'color' => GameBundle::getColor(),
            'feeds' => $feeds,
            'json' => json_encode($feeds,true)
        ]);
    }

}
