<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
    /**
     * @Route("/fee/check", name="payment.check")
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successFeeAction()
    {
        return $this->render('Payment/fee.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fee/{id}", name="payment.fee")
     * @param $id
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @throws \Exception
     */
    public function feeAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->findOneBy(['id' => $id]);

        if ($turniej == NULL) {
            throw $this->createNotFoundException('Turniej nie istnieje!');
        }

        $fee = $em->getRepository('TurniejBundle:EntryTournament')->findOneBy([
            'tournamentId' => $turniej->getId(),
            'playerId' => $this->getUser()->getId()
        ]);

        if ($fee == NULL) {
            switch ($turniej->getPlayerType()) {
                case 0:
                    return $this->redirectToRoute('tournament.join', ['id' => $id]);
                case 1:
                    return $this->redirectToRoute('tournament.joins', ['id' => $id]);
                default:
                    throw new \Exception('Błąd w typie graczy na turnieju!');
            }
        }

        if ($fee->getStatus() != 1) {
            return $this->redirectToRoute('tournament.id', ['id' => $id]);
        }

        return $this->redirectToRoute('payment.prepare', [
            'fee' => $fee,
            'cost' => $turniej->getCostPerTeam(),
            'description' => 'Opłata wpisowa dla turnieju: ' . $turniej->getName() . '.'
        ], 307);
    }

    /**
     * @Route("/payment/prepare", name="payment.prepare")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function prepareAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
//        $fee = $em->getRepository('TurniejBundle:EntryTournament')->findOneBy([
//            'tournamentId' => $request->get('fee')->tournamentId(),
//            'playerId' => $this->getUser()->getId()
//        ]);

        $gatewayName = 'paypal';

        $storage = $this->get('payum')->getStorage('TurniejBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('PLN');
        $payment->setTotalAmount($request->get('cost'));
        $payment->setDescription($request->get('description'));
        $payment->setClientId($this->getUser()->getId());
        $payment->setClientEmail($this->getUser()->getEmail());

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'payment.check'
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @Route("/payment/status", name="payment.status")
     * @param Request $request
     * @return JsonResponse
     */
    public function doneAction(Request $request)
    {
        $token = $this->get('payum')->getHttpRequestVerifier()->verify($request);

        $gateway = $this->get('payum')->getGateway($token->getGatewayName());

        // you can invalidate the token. The url could not be requested any more.
//         $this->get('payum')->getHttpRequestVerifier()->invalidate($token);

        // Once you have token you can get the model from the storage directly.
        //$identity = $token->getDetails();
        //$payment = $payum->getStorage($identity->getClass())->find($identity);

        $gateway->execute($status = new GetHumanStatus($token));
        $payment = $status->getFirstModel();

        return new JsonResponse([
            'status' => $status->getValue(),
            'payment' => [
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ],
        ]);
    }

}
