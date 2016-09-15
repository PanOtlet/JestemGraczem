<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Payum\Core\Request\GetHumanStatus;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
    private $gatewayName = 'paypal';

    /**
     * @Route("/fee/check", name="payment.check")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function successFeeAction(Request $request)
    {
        if (!($token = $request->get('payum_token'))) {
            $token = NULL;
        }

        return $this->render('Payment/fee.html.twig', [
            'token' => $token
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
            'tournamentID' => $fee->getTournamentId(),
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

        $fee = $em->getRepository('TurniejBundle:EntryTournament')->findOneBy([
            'tournamentId' => $request->get('tournamentID'),
            'playerId' => $this->getUser()->getId()
        ]);

        $storage = $this->get('payum')->getStorage('TurniejBundle\Entity\Payment');

        $id = uniqid();

        $fee->setPaymentId($id);
        $em->persist($fee);
        $em->flush();

        $payment = $storage->create();
        $payment->setNumber($id);
        $payment->setCurrencyCode('PLN');
        $payment->setTotalAmount($request->get('cost') * 100);
        $payment->setDescription($request->get('description'));
        $payment->setClientId($this->getUser()->getId());
        $payment->setClientEmail($this->getUser()->getEmail());

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $this->gatewayName,
            $payment,
            'payment.status'
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

        switch ($status->getValue()) {
            case 'canceled':
            case 'suspended':
            case 'failed':
            case 'unknown':
                $message = 'payment.error';
                break;
            case 'authorized':
            case 'captured':
                $em = $this->getDoctrine()->getManager();
                $fee = $em->getRepository('TurniejBundle:EntryTournament')->findOneBy([
                    'paymentId' => $payment->getDetails()['INVNUM']
                ]);

                $fee->setStatus(2);
                $em->persist($fee);
                $em->flush();
                $this->get('payum')->getHttpRequestVerifier()->invalidate($token);
                $message = 'payment.success';
                break;
            default:
                $message = 'payment.waiting';
                break;
        }

//        return new JsonResponse([
//            'status' => $status->getValue(),
//            'payment' => [
//                'total_amount' => $payment->getTotalAmount(),
//                'currency_code' => $payment->getCurrencyCode(),
//                'details' => $payment->getDetails(),
//            ],
//        ]);

        return $this->render('Payment/fee.html.twig', [
            'message' => $message,
            'token' => $token,
            'status' => $status->getValue(),
            'payment' => [
                'total_amount' => $payment->getTotalAmount(),
                'currency_code' => $payment->getCurrencyCode(),
                'details' => $payment->getDetails(),
            ],
        ]);
    }

}
