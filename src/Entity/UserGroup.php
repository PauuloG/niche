<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserGroupRepository")
 * @JMS\ExclusionPolicy("all")
 */
class UserGroup
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=100)
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $handle;

    /**
     * @var string
     * @ORM\Column(type="string", unique=true, length=30, nullable=false)
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $slackId;

    /**
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="User", mappedBy="userGroups")
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $users;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    private $description;

    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): UserGroup
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return ArrayCollection
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @param User $user
     * @return UserGroup
     */
    public function addUser(User $user): UserGroup
    {
        if (!$this->users->contains($user)) {
            $this->users[] = $user;
        }

        return $this;
    }

    /**
     * @param User $user
     * @return UserGroup
     */
    public function removeUser(User $user): UserGroup
    {
        if ($this->users->contains($user)) {
            $this->users->removeElement($user);
        }

        return $this;
    }

    /**
     * @return string
     */
    public function getHandle(): string
    {
        return $this->handle;
    }

    /**
     * @param mixed $handle
     * @return UserGroup
     */
    public function setHandle($handle): UserGroup
    {
        $this->handle = $handle;
        return $this;
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
     * @return UserGroup
     */
    public function setSlackId(string $slackId): UserGroup
    {
        $this->slackId = $slackId;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return UserGroup
     */
    public function setDescription(string $description): UserGroup
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return int
     *
     * @JMS\Expose
     * @JMS\VirtualProperty
     * @JMS\SerializedName("usersCount")
     * @JMS\Groups({"usergroup_list", "usergroup_detail"})
     */
    public function getUsersCount(): int
    {
        return $this->getUsers()->count();
    }
}
