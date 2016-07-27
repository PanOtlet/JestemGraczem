<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class GameCenterController extends Controller
{
    /**
     * @Route("/", name="center")
     */
    public function indexAction()
    {
        return $this->render('gameCenter/index.html.twig', [
            'games' => [
                [
                    'title' => 'League of Legends',
                    'url' => 'center.lol',
                    'img' => 'lol'
                ],
            ]
        ]);
    }

    /**
     * @Route("/lol", name="center.lol")
     */
    public function lolAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('League of Legends - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('name', 'description', "Centrum gracza League of Legends! Najświeższe i najciekawsze newsy tylko u nas!")
            ->addMeta('property', 'og:title', 'League of Legends - Centrum Gracza :: JestemGraczem.pl')
            ->addMeta('property', 'og:url', $this->get('router')->generate('center.lol', [], UrlGeneratorInterface::ABSOLUTE_URL));

        $json_output = file_get_contents("https://eune.api.pvp.net/api/lol/eune/v1.2/champion?freeToPlay=true&api_key=dc0b664f-8284-4732-b680-63e418a0ecc7");
        $json = json_decode($json_output, true);

        $json_output = file_get_contents("https://eune.api.pvp.net/api/lol/static-data/eune/v1.2/champion?locale=pl_PL&dataById=true&api_key=dc0b664f-8284-4732-b680-63e418a0ecc7");
        $champions = json_decode($json_output, true);

        foreach ($json['champions'] as $hero) {
            $rotacja[] = $champions['data'][$hero['id']]['name'];
        }

        return $this->render('gameCenter/lol.html.twig', [
            'rotacja' => $rotacja
        ]);
    }

    /**
     * @Route("/csgo", name="center.csgo")
     */
    public function csgoAction()
    {
        return $this->render('gameCenter/csgo.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/hots", name="center.hots")
     */
    public function hotsAction()
    {
        return $this->render('gameCenter/hots.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/wow", name="center.wow")
     */
    public function wowAction()
    {
        return $this->render('gameCenter/wow.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/dota", name="center.dota")
     */
    public function dotaAction()
    {
        return $this->render('gameCenter/dota.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/overwatch", name="center.overwatch")
     */
    public function overwatchAction()
    {
        return $this->render('gameCenter/overwatch.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/gta", name="center.gta")
     */
    public function gtaAction()
    {
        return $this->render('gameCenter/gta.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fifa", name="center.fifa")
     */
    public function fifaAction()
    {
        return $this->render('gameCenter/fifa.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/hs", name="center.hs")
     */
    public function hsAction()
    {
        return $this->render('gameCenter/hs.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/pokemon", name="center.pokemon")
     */
    public function pokemonAction()
    {
        return $this->render('gameCenter/pokemon.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/wot", name="center.wot")
     */
    public function wotAction()
    {
        return $this->render('gameCenter/wot.html.twig', [
            // ...
        ]);
    }

}
