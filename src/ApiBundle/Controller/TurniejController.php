<?php

namespace ApiBundle\Controller;

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
     * @param Request $request
     * @return Response
     */
    public function setBracketAction(Request $request)
    {
        if (!$request->isXmlHttpRequest()){
            return \ApiBundle\Controller\DefaultController::badRequest();
        }

        $em = $this->getDoctrine()->getManager();

        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => $request->get('id')->getViewData()]);
//        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => 1]);

        if ($turniej == NULL || $turniej->getOwner() != $this->getUser()->getId()) {
            return \ApiBundle\Controller\DefaultController::badRequest();
        }

        $bracket = json_decode($request->get('Data')->getViewData(), true);
//        $json = '{"teams":[["Team 1","Team 2"],["Team 3","Team 4"]],"results":[[[[4,6],[5,7]],[[8,9],[4,3]]]]}';
//        $bracket = json_decode($json, true);

        $turniej->setBracket(json_encode($bracket['results']));
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
