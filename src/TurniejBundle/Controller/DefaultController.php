<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class DefaultController extends Controller
{

    protected $color = "magenta";

    /**
     * @Route("/", name="tournament")
     */
    public function indexAction()
    {
        $seo = $this->container->get('sonata.seo.page');
        $seo->setTitle('Turnieje :: JestemGraczem.pl')
            ->addMeta('name', 'description', 'Więcej informacji wkrótce!')
            ->addMeta('property', 'og:title', 'Turnieje')
            ->addMeta('property', 'og:description', 'Więcej informacji wkrótce!')
            ->addMeta('property', 'og:url', $this->get('router')->generate('tournament', [], UrlGeneratorInterface::ABSOLUTE_URL));
        return $this->render('tournament/index.html.twig',[
            'color' => $this->color,
        ]);
    }
}
