<?php
namespace TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Payum\Core\Model\Token;

/**
 * @ORM\Table(name="paymentToken")
 * @ORM\Entity
 */
class PaymentToken extends Token
{
}