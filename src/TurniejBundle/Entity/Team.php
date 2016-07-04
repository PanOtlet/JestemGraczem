<?php

namespace TurniejBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

/**
 * Team
 *
 * @ORM\Table(name="team")
 * @ORM\Entity(repositoryClass="TurniejBundle\Repository\TeamRepository")
 * @ORM\HasLifecycleCallbacks()
 * @UniqueEntity("tag")
 */
class Team
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
     *
     */
    private $name;

    /**
     * @var int
     *
     * @ORM\Column(name="owner", type="integer")
     */
    private $owner;

    /**
     * @var string
     *
     * @ORM\Column(name="tag", type="string", length=5, unique=true)
     */
    private $tag;

    /**
     * @var string
     *
     * @Assert\File(maxSize="8M")
     * @Assert\Image(mimeTypesMessage="error.filetype")
     */
    private $logofile;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $logo;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=2000)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="shortdesc", type="string", length=255)
     */
    private $shortdesc;

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
     * @param int $owner
     */
    public function setOwner($owner)
    {
        $this->owner = $owner;
    }

    /**
     * @return int
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Team
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
     * Set tag
     *
     * @param string $tag
     *
     * @return Team
     */
    public function setTag($tag)
    {
        $this->tag = $tag;

        return $this;
    }

    /**
     * Get tag
     *
     * @return string
     */
    public function getTag()
    {
        return $this->tag;
    }

    /**
     * @param mixed $logo
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;
    }

    /**
     * @return mixed
     */
    public function getLogo()
    {
        return $this->logo;
    }

    /**
     * Set logo
     *
     * @param UploadedFile $logo
     *
     * @return Team
     */
    public function setLogoFile(UploadedFile $logo = null)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * Get logo
     *
     * @return UploadedFile
     */
    public function getLogoFile()
    {
        return $this->logo;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Team
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
     * Set shortdesc
     *
     * @param string $shortdesc
     *
     * @return Team
     */
    public function setShortdesc($shortdesc)
    {
        $this->shortdesc = $shortdesc;

        return $this;
    }

    /**
     * Get shortdesc
     *
     * @return string
     */
    public function getShortdesc()
    {
        return $this->shortdesc;
    }

    public function getAbsolutePath()
    {
        return null === $this->logo
            ? null
            : $this->getUploadRootDir() . '/' . $this->logo;
    }

    public function getWebPath()
    {
        return null === $this->logo
            ? null
            : $this->getUploadDir() . '/' . $this->logo;
    }

    protected function getUploadRootDir()
    {
        return __DIR__ . '/../../../web/' . $this->getUploadDir();
    }

    protected function getUploadDir()
    {
        return 'assets/team/logo';
    }

    public function uploadLogo()
    {
        if (null === $this->getLogo()) {
            return;
        }

        $filename = md5(uniqid()) . '.' . $this->getLogoFile()->getClientOriginalExtension();
        $this->getLogoFile()->move($this->getUploadRootDir(), $filename);

        $this->logo = $filename;
    }
}

