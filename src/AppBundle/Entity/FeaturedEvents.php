<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FeaturedEvents
 *
 * @ORM\Table(name="featured_events")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\FeaturedEventsRepository")
 */
class FeaturedEvents
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
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="html", type="text")
     */
    private $html;

    /**
     * @var string
     *
     * @ORM\Column(name="javascript", type="text", nullable=true)
     */
    private $javascript;

    /**
     * @var string
     *
     * @ORM\Column(name="css", type="text", nullable=true)
     */
    private $css;

    /**
     * @var string
     *
     * @ORM\Column(name="buttonUrl", type="string", length=255, nullable=true)
     */
    private $buttonUrl;

    /**
     * @var string
     *
     * @ORM\Column(name="buttonText", type="string", length=255, nullable=true)
     */
    private $buttonText;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="datetime")
     */
    private $date;


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
     * @return FeaturedEvents
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
     * Set html
     *
     * @param string $html
     *
     * @return FeaturedEvents
     */
    public function setHtml($html)
    {
        $this->html = $html;

        return $this;
    }

    /**
     * Get html
     *
     * @return string
     */
    public function getHtml()
    {
        return $this->html;
    }

    /**
     * Set javascript
     *
     * @param string $javascript
     *
     * @return FeaturedEvents
     */
    public function setJavascript($javascript)
    {
        $this->javascript = $javascript;

        return $this;
    }

    /**
     * Get javascript
     *
     * @return string
     */
    public function getJavascript()
    {
        return $this->javascript;
    }

    /**
     * Set css
     *
     * @param string $css
     *
     * @return FeaturedEvents
     */
    public function setCss($css)
    {
        $this->css = $css;

        return $this;
    }

    /**
     * Get css
     *
     * @return string
     */
    public function getCss()
    {
        return $this->css;
    }

    /**
     * Set buttonUrl
     *
     * @param string $buttonUrl
     *
     * @return FeaturedEvents
     */
    public function setButtonUrl($buttonUrl)
    {
        $this->buttonUrl = $buttonUrl;

        return $this;
    }

    /**
     * Get buttonUrl
     *
     * @return string
     */
    public function getButtonUrl()
    {
        return $this->buttonUrl;
    }

    /**
     * Set buttonText
     *
     * @param string $buttonText
     *
     * @return FeaturedEvents
     */
    public function setButtonText($buttonText)
    {
        $this->buttonText = $buttonText;

        return $this;
    }

    /**
     * Get buttonText
     *
     * @return string
     */
    public function getButtonText()
    {
        return $this->buttonText;
    }

    /**
     * Set show time
     *
     * @param \DateTime $date
     *
     * @return FeaturedEvents
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get show time
     *
     * @return \DateTime
     */
    public function getDate()
    {
        return $this->date;
    }


}

