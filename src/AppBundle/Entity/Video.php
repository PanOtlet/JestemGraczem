<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Video
 *
 * @ORM\Table(name="video")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\VideoRepository")
 */
class Video
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
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="videoid", type="string", length=20)
     */
    private $videoid;

    /**
     * @var int
     *
     * @ORM\Column(name="user", type="integer")
     */
    private $user;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_add", type="datetime")
     */
    private $dateAdd;

    /**
     * @var int
     *
     * @ORM\Column(name="promoted", type="boolean")
     */
    private $promoted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="accept", type="boolean")
     */
    private $accept;


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
     * Set title
     *
     * @param string $title
     *
     * @return Video
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set videoid
     *
     * @param string $videoid
     *
     * @return Video
     */
    public function setVideoid($videoid)
    {
        $this->videoid = $videoid;

        return $this;
    }

    /**
     * Get videoid
     *
     * @return string
     */
    public function getVideoid()
    {
        return $this->videoid;
    }

    /**
     * Set user
     *
     * @param integer $user
     *
     * @return Video
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return int
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set dateAdd
     *
     * @param \DateTime $dateAdd
     *
     * @return Video
     */
    public function setDateAdd($dateAdd)
    {
        $this->dateAdd = $dateAdd;

        return $this;
    }

    /**
     * Get dateAdd
     *
     * @return \DateTime
     */
    public function getDateAdd()
    {
        return $this->dateAdd;
    }

    /**
     * Set promoted
     *
     * @param boolean $promoted
     *
     * @return Video
     */
    public function setPromoted($promoted)
    {
        $this->promoted = $promoted;

        return $this;
    }

    /**
     * Get promoted
     *
     * @return boolean
     */
    public function getPromoted()
    {
        return $this->promoted;
    }

    /**
     * @param boolean $accept
     */
    public function setAccept($accept)
    {
        $this->accept = $accept;
    }

    /**
     * Get accept
     *
     * @return boolean
     */
    public function getAccept()
    {
        return $this->accept;
    }
}

