<?php

namespace ApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class StreamController extends Controller
{

    /**
     * @Route("/stream/p/{id}", name="api.stream.id")
     */
    public function streamAction($id)
    {
        $stream = $this->getDoctrine()->getRepository('AppBundle:Video')->findOneBy(['id' => $id]);

        $encoders = [
            new XmlEncoder(),
            new JsonEncoder()
        ];

        $normalizers = [
            new ObjectNormalizer()
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($stream, 'json'));
    }

    /**
     * @Route("/stream/{page}", name="api.stream")
     */
    public function indexAction($page = 0)
    {

        $em = $this->getDoctrine()->getRepository('AppBundle:Stream');
        $stream = $em->createQueryBuilder('p')
            ->where('p.status = 1')
            ->setFirstResult($page * 10)
            ->orderBy('p.id', 'DESC')
            ->getQuery()
            ->setMaxResults(10)
            ->getResult();
        
        $encoders = [
            new XmlEncoder(),
            new JsonEncoder()
        ];

        $normalizers = [
            new ObjectNormalizer()
        ];

        $serializer = new Serializer($normalizers, $encoders);

        return new Response($serializer->serialize($stream, 'json'));
    }
}
