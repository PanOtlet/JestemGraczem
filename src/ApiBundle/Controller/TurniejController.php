<?php

namespace ApiBundle\Controller;

use ApiBundle\Controller\DefaultController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;

class TurniejController extends Controller
{
    /**
     * @Route("/bracket", name="api.bracket")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     * @return Response
     */
    public function setBracketAction(Request $request)
    {
        if (!$request->isXmlHttpRequest() || !$request->get('id') || !$request->get('data')) {
            return \ApiBundle\Controller\DefaultController::badRequest();
        }

        $em = $this->getDoctrine()->getManager();

        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy([
            'id' => $request->get('id'),
            'owner' => $this->getUser()->getId()
        ]);

        if ($turniej == NULL) {
            return DefaultController::badRequest();
        }

        $bracket = $request->get('data');

        $turniej->setBracket($bracket['results']);
        $em->persist($turniej);
        $em->flush();

        $encoders = [
            new XmlEncoder(),
            new JsonEncoder()
        ];

        $normalizers = [
            new ObjectNormalizer()
        ];

        $serializer = new Serializer($normalizers, $encoders);

        $stream = [
            'teams' => $turniej->getTeams(),
            'results' => $turniej->getBracket()
        ];

        return new Response(
            $serializer->serialize($stream, 'json'),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }

}
