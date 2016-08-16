<?php

namespace TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * turnieje
 *
 * @ORM\Table(name="turnieje")
 * @ORM\Entity(repositoryClass="TurniejBundle\Repository\TurniejeRepository")
 */
class Turnieje
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
     * @var string
     *
     * Nazwa drużyny
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * Opis turnieju
     * @ORM\Column(name="description", type="text")
     */
    private $description;

    /**
     * @var int
     *
     * Organizator
     * @ORM\Column(name="owner", type="integer")
     */
    private $owner;

    /**
     * @var int
     *
     * Gra, która będzie rozgrywana
     * 0. Inna
     * 1. CS:GO
     * 2. LoL
     * 3. HotS
     * 4. SC2
     * 5. HS
     * 6. Dota2
     * 7. WoT
     * @ORM\Column(name="dyscyplina", type="smallint")
     */
    private $dyscyplina;

    /**
     * @var bool
     *
     * Open/Invite
     * @ORM\Column(name="type", type="boolean")
     */
    private $type;

    /**
     * @var int
     *
     * Wpisowe, free, czy dotacje?
     *
     * Free 0
     * Wpisowe 1
     * Dotacje 2
     *
     * @ORM\Column(name="cost", type="smallint")
     */
    private $cost;

    /**
     * @var int
     *
     * Ilość drużyn
     * @ORM\Column(name="countTeam", type="smallint")
     */
    private $countTeam;

    /**
     * @var int
     *
     * Pula nagród
     * @ORM\Column(name="prizePool", type="integer")
     */
    private $prizePool;

    /**
     * @var int
     *
     * Koszt wpisowego na drużynę
     * @ORM\Column(name="costPerTeam", type="integer")
     */
    private $costPerTeam;

    /**
     * @var int
     *
     * Koszty organizacyjne
     * @ORM\Column(name="costOrg", type="smallint")
     */
    private $costOrg;

    /**
     * @var \DateTime
     *
     * Data rozpoczęcia zapisów
     * @ORM\Column(name="dataStart", type="date")
     */
    private $dataStart;

    /**
     * @var \DateTime
     *
     * Data rozpoczęcia turnieju
     * @ORM\Column(name="dataStop", type="date")
     */
    private $dataStop;

    /**
     * @var bool
     *
     * Czy turniej jest dla drużyn (1), czy dla graczy (0)
     * @ORM\Column(name="playerType", type="boolean")
     */
    private $playerType;

    /**
     * @var bool
     *
     * Zakończony?
     * @ORM\Column(name="end", type="boolean")
     */
    private $end;

    /**
     * @var bool
     *
     * Promowany?
     * @ORM\Column(name="promoted", type="boolean")
     */
    private $promoted;


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
     * Set name
     *
     * @param string $name
     *
     * @return turnieje
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return turnieje
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set owner
     *
     * @param integer $owner
     *
     * @return turnieje
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return int
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set dyscyplina
     *
     * @param integer $dyscyplina
     *
     * @return turnieje
     */
    public function setDyscyplina($dyscyplina)
    {
        $this->dyscyplina = $dyscyplina;

        return $this;
    }

    /**
     * Get dyscyplina
     *
     * @return int
     */
    public function getDyscyplina()
    {
        return $this->dyscyplina;
    }

    /**
     * Set type
     *
     * @param boolean $type
     *
     * @return turnieje
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type
     *
     * @return bool
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set cost
     *
     * @param integer $cost
     *
     * @return turnieje
     */
    public function setCost($cost)
    {
        $this->cost = $cost;

        return $this;
    }

    /**
     * Get cost
     *
     * @return int
     */
    public function getCost()
    {
        return $this->cost;
    }

    /**
     * Set countTeam
     *
     * @param integer $countTeam
     *
     * @return turnieje
     */
    public function setCountTeam($countTeam)
    {
        $this->countTeam = $countTeam;

        return $this;
    }

    /**
     * Get countTeam
     *
     * @return int
     */
    public function getCountTeam()
    {
        return $this->countTeam;
    }

    /**
     * @return int
     */
    public function getPrizePool()
    {
        return $this->prizePool;
    }

    /**
     * @param int $prizePool
     */
    public function setPrizePool($prizePool)
    {
        $this->prizePool = $prizePool;
    }

    /**
     * Set wpisowe
     *
     * @param integer $wpisowe
     *
     * @return turnieje
     */
    public function setWpisowe($wpisowe)
    {
        $this->wpisowe = $wpisowe;

        return $this;
    }

    /**
     * Get wpisowe
     *
     * @return int
     */
    public function getWpisowe()
    {
        return $this->wpisowe;
    }

    /**
     * Set costPerTeam
     *
     * @param integer $costPerTeam
     *
     * @return turnieje
     */
    public function setCostPerTeam($costPerTeam)
    {
        $this->costPerTeam = $costPerTeam;

        return $this;
    }

    /**
     * Get costPerTeam
     *
     * @return int
     */
    public function getCostPerTeam()
    {
        return $this->costPerTeam;
    }

    /**
     * Set costOrg
     *
     * @param integer $costOrg
     *
     * @return turnieje
     */
    public function setCostOrg($costOrg)
    {
        $this->costOrg = $costOrg;

        return $this;
    }

    /**
     * Get costOrg
     *
     * @return int
     */
    public function getCostOrg()
    {
        return $this->costOrg;
    }

    /**
     * Set dataStart
     *
     * @param \DateTime $dataStart
     *
     * @return turnieje
     */
    public function setDataStart($dataStart)
    {
        $this->dataStart = $dataStart;

        return $this;
    }

    /**
     * Get dataStart
     *
     * @return \DateTime
     */
    public function getDataStart()
    {
        return $this->dataStart;
    }

    /**
     * Set dataStop
     *
     * @param \DateTime $dataStop
     *
     * @return turnieje
     */
    public function setDataStop($dataStop)
    {
        $this->dataStop = $dataStop;

        return $this;
    }

    /**
     * Get dataStop
     *
     * @return \DateTime
     */
    public function getDataStop()
    {
        return $this->dataStop;
    }

    /**
     * Set playerType
     *
     * @param boolean $playerType
     *
     * @return turnieje
     */
    public function setPlayerType($playerType)
    {
        $this->playerType = $playerType;

        return $this;
    }

    /**
     * Get playerType
     *
     * @return bool
     */
    public function getPlayerType()
    {
        return $this->playerType;
    }

    /**
     * @param boolean $end
     */
    public function setEnd($end)
    {
        $this->end = $end;
    }

    /**
     * Get end time
     *
     * @return boolean
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * @param boolean $promoted
     */
    public function setPromoted($promoted)
    {
        $this->promoted = $promoted;
    }

    /**
     * Get promoted
     *
     * @return bool
     */
    public function getPromoted()
    {
        return $this->promoted;
    }
}

