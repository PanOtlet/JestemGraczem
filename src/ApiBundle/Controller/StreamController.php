<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use AppBundle\Entity\User;

class StreamController extends Controller
{
    /**
     * @Route("/stream", name="api.stream")
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:User');

        $response = $em->createQueryBuilder('p')
            ->select('p.username', 'p.twitch', 'p.beampro', 'p.partner', 'p.description')
            ->where('p.twitch IS NOT NULL')
            ->orWhere('p.beampro IS NOT NULL')
            ->andWhere('p.partner = 1')
            ->getQuery()
            ->getResult();

        if (!$response) {
            return new Response(
                "[]",
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );
        }

        $encoders = [
            new XmlEncoder(),
            new JsonEncoder()
        ];

        $normalizers = [
            new ObjectNormalizer()
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response(
            $serializer->serialize($response, 'json'),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
    /**
     * @Route("/stream/{name}", name="api.stream.name")
     */
    public function nameAction($name = NULL)
    {
        $em = $this->getDoctrine()->getRepository('AppBundle:User');

        $stream = $em->createQueryBuilder('p')
            ->select('p.username', 'p.twitch', 'p.partner', 'p.description')
            ->where('p.twitch = :name')
            ->setParameter('name', $name)
            ->getQuery()
            ->getResult();

        if ($stream == NULL) {
            return new Response(
                "{name:NULL,status:-1,message:'ERROR 404 - Streamów nie znaleziono!'}",
                Response::HTTP_NOT_FOUND,
                ['content-type' => 'application/json']
            );
        }

        $encoders = [
            new XmlEncoder(),
            new JsonEncoder()
        ];

        $normalizers = [
            new ObjectNormalizer()
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response(
            $serializer->serialize($stream, 'json'),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
    }
}
