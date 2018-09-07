<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @JMS\ExclusionPolicy("all")
 */
class User
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false, length=100)
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $slackId;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, nullable=false, length=100)
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $slackName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, length=100)
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $slackRealName;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true, length=150)
     */
    private $email;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="UserGroup", inversedBy="users")
     * @ORM\JoinTable(name="users_usergroups")
     */
    private $userGroups;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $imageUrl;

    public function __construct()
    {
        $this->userGroups = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * @return string
     */
    public function getSlackId(): string
    {
        return $this->slackId;
    }

    /**
     * @param string $slackId
     * @return User
     */
    public function setSlackId(string $slackId): User
    {
        $this->slackId = $slackId;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlackName(): string
    {
        return $this->slackName;
    }

    /**
     * @param string $slackName
     * @return User
     */
    public function setSlackName(string $slackName): User
    {
        $this->slackName = $slackName;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlackRealName(): string
    {
        return $this->slackRealName;
    }

    /**
     * @param string $slackRealName
     * @return User
     */
    public function setSlackRealName(string $slackRealName): User
    {
        $this->slackRealName = $slackRealName;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     * @return User
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getSlackMentionableName()
    {
        return sprintf(
            '<@%s>',
            $this->getSlackId()
        );
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     * @return User
     */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUserGroups(): ArrayCollection
    {
        return $this->userGroups;
    }

    /**
     * @param UserGroup $userGroup
     * @return User
     */
    public function addUserGroup(UserGroup $userGroup): User
    {
        if (!$this->userGroups->contains($userGroup)) {
            $this->userGroups[] = $userGroup;
        }

        return $this;
    }

    /**
     * @param UserGroup $userGroup
     * @return User
     */
    public function removeUserGroup(UserGroup $userGroup): User
    {
        if ($this->userGroups->contains($userGroup)) {
            $this->userGroups->removeElement($userGroup);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @param String $imageUrl
     * @return User
     */
    public function setImageUrl(String $imageUrl): User
    {
        $this->imageUrl = $imageUrl;
        return $this;
    }
}
