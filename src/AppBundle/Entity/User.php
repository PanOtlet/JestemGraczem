<?php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 * @ORM\HasLifecycleCallbacks()
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
     * @ORM\Column(type="text", nullable=true)
     *
     * @Assert\Length(
     *     min=3,
     *     minMessage="The name is too short.",
     *     groups={"Registration", "Profile"}
     * )
     */
    protected $description = null;

    /**
     * @Assert\File(maxSize="8M")
     * @Assert\Image(mimeTypesMessage="Zły plik!")
     */
    protected $profilePictureFile;

    private $tempProfilePicturePath;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    protected $profilePicturePath;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $partner = 0;

    /**
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $premium = 0;

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

    public function getDescription()
    {
        return $this->description;
    }

    public function getPartner()
    {
        return $this->partner;
    }

    public function getPremium()
    {
        return $this->premium;
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

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function setPartner($partner)
    {
        $this->partner = $partner;
    }

    public function setPremium($premium)
    {
        $this->premium = $premium;
    }

    /**
     * @param UploadedFile $file
     * @return object
     */
    public function setProfilePictureFile(UploadedFile $file = null)
    {
        $this->profilePictureFile = $file;
        if (isset($this->profilePicturePath)) {
            $this->tempProfilePicturePath = $this->profilePicturePath;
            $this->profilePicturePath = null;
        } else {
            $this->profilePicturePath = 'web';
        }
        return $this;
    }

    /**
     * @return UploadedFile
     */
    public function getProfilePictureFile()
    {
        return $this->profilePictureFile;
    }

    /**
     * Set profilePicturePath
     *
     * @param string $profilePicturePath
     * @return User
     */
    public function setProfilePicturePath($profilePicturePath)
    {
        $this->profilePicturePath = $profilePicturePath;

        return $this;
    }

    /**
     * Get profilePicturePath
     *
     * @return string
     */
    public function getProfilePicturePath()
    {
        return $this->profilePicturePath;
    }

    /**
     * Get the absolute path of the profilePicturePath
     */
    public function getProfilePictureAbsolutePath()
    {
        return null === $this->profilePicturePath
            ? null
            : $this->getUploadRootDir() . '/' . $this->profilePicturePath;
    }

    /**
     * Get root directory for file uploads
     *
     * @return string
     */
    protected function getUploadRootDir($type = 'profilePicture')
    {
        return __DIR__ . '/../../../web/' . $this->getUploadDir($type);
    }

    /**
     * @return string
     */
    protected function getUploadDir($type = 'profilePicture')
    {
        return 'assets/user/profilepics';
    }

    /**
     * @return string
     */
    public function getWebProfilePicturePath()
    {

        return '/' . $this->getUploadDir() . '/' . $this->getProfilePicturePath();
    }

    /**
     * @ORM\PrePersist()
     * @ORM\PreUpdate()
     */
    public function preUploadProfilePicture()
    {
        if (null !== $this->getProfilePictureFile()) {
            $filename = $this->generateRandomProfilePictureFilename();
            $this->setProfilePicturePath($filename . '.' . $this->getProfilePictureFile()->guessExtension());
        }
    }

    /**
     * @return string
     */
    public function generateRandomProfilePictureFilename()
    {
        $count = 0;
        do {
            $random = rand(0, 30);
            $randomString = bin2hex($random);
            $count++;
        } while (file_exists($this->getUploadRootDir() . '/' . $randomString . '.' . $this->getProfilePictureFile()->guessExtension()) && $count < 50);

        return $randomString;
    }

    /**
     * @ORM\PostPersist()
     * @ORM\PostUpdate()
     *
     * @return mixed
     */
    public function uploadProfilePicture()
    {
        if ($this->getProfilePictureFile() === null) {
            return;
        }

        $this->getProfilePictureFile()->move($this->getUploadRootDir(), $this->getProfilePicturePath());

        if (isset($this->tempProfilePicturePath) && file_exists($this->getUploadRootDir() . '/' . $this->tempProfilePicturePath)) {
            unlink($this->getUploadRootDir() . '/' . $this->tempProfilePicturePath);
            $this->tempProfilePicturePath = null;
        }
        $this->profilePictureFile = null;
    }

    /**
     * @ORM\PostRemove()
     */
    public function removeProfilePictureFile()
    {
        if ($file = $this->getProfilePictureAbsolutePath() && file_exists($this->getProfilePictureAbsolutePath())) {
            unlink($file);
        }
    }
}