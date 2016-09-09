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
     * @Route("/jackpot/{id}", name="payment.jackpot")
     */
    public function addJackpotAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $turniej = $em->getRepository('TurniejBundle:Turnieje')->find($id);

        if ($turniej == NULL) {
            throw $this->createNotFoundException('Turniej nie istnieje!');
        }

        $storage = $this->get('payum')->getStorage('TurniejBundle\Entity\Payment');

        /** @var \TurniejBundle\Entity\Payment $details */
        $details = $storage->create();

        $details->setNumber(uniqid());
        $details->setCurrencyCode('PLN');
        $details->setTotalAmount($turniej->getCostPerTeam());
        $details->setDescription('A description');
        $details->setClientId($this->getUser()->getId());
        $details->setClientEmail($this->getUser()->getEmail());
//        $details['PAYMENTREQUEST_0_CURRENCYCODE'] = 'PLN';
//        $details['PAYMENTREQUEST_0_AMT'] = $turniej->getCostPerTeam();
        $storage->update($details);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            'paypal',
            $details,
            'payment.jackpot.status'
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @Route("/jackpot/status", name="payment.jackpot.status")
     */
    public function statusJackpotAction()
    {
        return $this->render('Payment/success_jackpot.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fee/{id}", name="payment.fee")
     */
    public function addFeeAction($id)
    {
        return $this->render('Payment/add_fee.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/jackpot/{id}/success", name="payment.jackpot.success")
     */
    public function successJackpotAction($id)
    {
        return $this->render('Payment/success_jackpot.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/jackpot/{id}/fail", name="payment.jackpot.fail")
     */
    public function failJackpotAction($id)
    {
        return $this->render('Payment/fail_jackpot.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fee/{id}/success", name="payment.fee.success")
     */
    public function successFeeAction($id)
    {
        return $this->render('Payment/success_fee.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fee/{id}/fail", name="payment.fee.fail")
     */
    public function failFeeAction($id)
    {
        return $this->render('Payment/fail_fee.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/payment/prepare", name="payment.prepare")
     */
    public function prepareAction(Request $request)
    {
        $gatewayName = 'paypal';

        $storage = $this->get('payum')->getStorage('TurniejBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('PLN');
        $payment->setTotalAmount(1.23);
        $payment->setDescription('A description');
        $payment->setClientId($this->getUser()->getId());
        $payment->setClientEmail($this->getUser()->getEmail());

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'payment.status'
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

    /**
     * @Route("/payment/status", name="payment.status")
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
