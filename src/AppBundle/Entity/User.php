<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     minMessage="The name is too short.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $steam = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     minMessage="The name is too short.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $battlenet = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     minMessage="The name is too short.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $lol = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     minMessage="The name is too short.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $twitch = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     minMessage="The name is too short.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $youtube = null;

    /**
     * @ORM\Column(type="string", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     minMessage="The name is too short.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $localization = null;

    /**
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $partner = 0;

    public function __construct()
    {
        parent::__construct();
    }

    public function getBattlenet()
    {
        return $this->battlenet;
    }

    public function getSteam()
    {
        return $this->steam;
    }

    public function getLol()
    {
        return $this->lol;
    }

    public function getTwitch()
    {
        return $this->twitch;
    }

    public function getYouTube()
    {
        return $this->youtube;
    }

    public function getLocalization()
    {
        return $this->localization;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function setSteam($steam)
    {
        $this->steam = $steam;
    }

    public function setBattlenet($battlenet)
    {
        $this->battlenet = $battlenet;
    }

    public function setLol($lol)
    {
        $this->lol = $lol;
    }

    public function setTwitch($twitch)
    {
        $this->twitch = $twitch;
    }

    public function setYouTube($youtube)
    {
        $this->youtube = $youtube;
    }

    public function setLocalization($localization)
    {
        $this->localization = $localization;
    }

    public function setPartner($partner)
    {
        $this->partner = $partner;
    }
}