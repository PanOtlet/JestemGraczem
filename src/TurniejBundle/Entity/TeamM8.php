<?php

namespace TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TeamM8
 *
 * @ORM\Table(name="team_m8")
 * @ORM\Entity(repositoryClass="TurniejBundle\Repository\TeamM8Repository")
 */
class TeamM8
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
     * @ORM\Column(name="divisionId", type="integer")
     */
    private $divisionId;

    /**
     * @var int
     *
     * @ORM\Column(name="playerId", type="integer")
     */
    private $playerId;

    /**
     * @var int
     *
     * @ORM\Column(name="role", type="string", length=10)
     */
    private $role;


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
     * Set divisionId
     *
     * @param integer $divisionId
     *
     * @return TeamM8
     */
    public function setDivisionId($divisionId)
    {
        $this->divisionId = $divisionId;

        return $this;
    }

    /**
     * Get divisionId
     *
     * @return int
     */
    public function getDivisionId()
    {
        return $this->divisionId;
    }

    /**
     * Set playerId
     *
     * @param integer $playerId
     *
     * @return TeamM8
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
     * Set role
     *
     * @param integer $role
     *
     * @return TeamM8
     */
    public function setRole($role)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return int
     */
    public function getRole()
    {
        return $this->role;
    }
}

