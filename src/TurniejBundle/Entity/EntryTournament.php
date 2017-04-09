<?php

namespace TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * EntryTournament
 *
 * @ORM\Table(name="entry_tournament")
 * @ORM\Entity(repositoryClass="TurniejBundle\Repository\EntryTournamentRepository")
 */
class EntryTournament
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="tournamentId", type="integer")
     */
    private $tournamentId;

    /**
     * @var int
     *
     * @ORM\Column(name="playerId", type="integer")
     */
    private $playerId;

    /**
     * @var int
     *
     * 0 - Zaproszony
     * 1 - Zapisany, ale nie opłacony
     * 2 - Zapisany
     * 3 - Wyrzucony
     * 4 - Wypisany
     * @ORM\Column(name="status", type="integer")
     */
    private $status = 0;

    /**
     * @var string
     *
     * Payment id
     * @ORM\Column(name="paymentId", type="string", nullable=true)
     */
    private $paymentId;

    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set tournamentId
     *
     * @param integer $tournamentId
     *
     * @return EntryTournament
     */
    public function setTournamentId($tournamentId)
    {
        $this->tournamentId = $tournamentId;

        return $this;
    }

    /**
     * Get tournamentId
     *
     * @return int
     */
    public function getTournamentId()
    {
        return $this->tournamentId;
    }

    /**
     * Set playerId
     *
     * @param integer $playerId
     *
     * @return EntryTournament
     */
    public function setPlayerId($playerId)
    {
        $this->playerId = $playerId;

        return $this;
    }

    /**
     * Get playerId
     *
     * @return int
     */
    public function getPlayerId()
    {
        return $this->playerId;
    }

    /**
     * Set status
     *
     * @param integer $status
     *
     * @return EntryTournament
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param string $paymentId
     */
    public function setPaymentId($paymentId)
    {
        $this->paymentId = $paymentId;
    }

    /**
     * @return string
     */
    public function getPaymentId()
    {
        return $this->paymentId;
    }
}

