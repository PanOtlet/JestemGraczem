<?php

namespace TurniejBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class PaymentController extends Controller
{
    /**
     * @Route("/jackpot/add", name="payment.jackpot.add")
     */
    public function addJackpotAction()
    {
        return $this->render('Payment/add_jackpot.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/jackpot/success", name="payment.jackpot.success")
     */
    public function successJackpotAction()
    {
        return $this->render('Payment/success_jackpot.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/jackpot/fail", name="payment.jackpot.fail")
     */
    public function failJackpotAction()
    {
        return $this->render('Payment/fail_jackpot.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fee/add", name="payment.fee.add")
     */
    public function addFeeAction()
    {
        return $this->render('Payment/add_fee.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fee/success", name="payment.fee.success")
     */
    public function successFeeAction()
    {
        return $this->render('Payment/success_fee.html.twig', [
            // ...
        ]);
    }

    /**
     * @Route("/fee/fail", name="payment.fee.fail")
     */
    public function failFeeAction()
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
        $gatewayName = 'offline';

        $storage = $this->get('payum')->getStorage('Acme\PaymentBundle\Entity\Payment');

        $payment = $storage->create();
        $payment->setNumber(uniqid());
        $payment->setCurrencyCode('PLN');
        $payment->setTotalAmount(123);
        $payment->setDescription('A description');
        $payment->setClientId($this->getUser()->getId());
        $payment->setClientEmail($this->getUser()->getEmail());

        $storage->update($payment);

        $captureToken = $this->get('payum')->getTokenFactory()->createCaptureToken(
            $gatewayName,
            $payment,
            'done' // the route to redirect after capture
        );

        return $this->redirect($captureToken->getTargetUrl());
    }

}
