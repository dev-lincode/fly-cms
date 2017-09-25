<?php

namespace Lincode\Fly\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Lincode\Fly\Bundle\Service\Service;

/**
 * UserProfile
 *
 * @ORM\Table(name="fly_user_profile")
 * @ORM\Entity()
 */
class UserProfile
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="string", length=255, unique=true)
     * @ORM\Id
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var boolean
     *
     * @ORM\Column(name="administrator", type="boolean")
     */
    private $administrator = false;

    /**
     * @var ArrayCollection $permissions
     * @ORM\OneToMany(targetEntity="UserProfilePermission", mappedBy="profile",
     *     cascade={"all","merge","persist","refresh","remove"})
     */
    private $permissions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = Service::UUID();
        $this->routes = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return UserProfile
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
     * Gets the value of administrator.
     *
     * @return boolean
     */
    public function getAdministrator()
    {
        return $this->administrator;
    }

    /**
     * Sets the value of administrator.
     *
     * @param boolean $administrator the administrator
     *
     * @return self
     */
    public function setAdministrator($administrator)
    {
        $this->administrator = $administrator;

        return $this;
    }

    public function addPermissions($permission)
    {
        $permission->setProfile($this);
        $this->permissions[] = $permission;
        return $this;
    }

    /**
     * Remove Route
     *
     * @param UserProfileRoute $permission
     */
    public function removePermissions($permission)
    {
        $this->permissions->removeElement($permission);
    }

    /**
     * Gets the permissions
     * @return ArrayCollection $permissions
     */
    public function getPermissions()
    {
        return $this->permissions;
    }

    public function __toString()
    {
        return $this->name;
    }
}
