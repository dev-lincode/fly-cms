<?php

namespace Lincode\Fly\Bundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UserProfileRoute
 *
 * @ORM\Table(name="fly_user_profile_permission")
 * @ORM\Entity(repositoryClass="Lincode\Fly\Bundle\Repository\UserProfilePermissionRepository")
 */
class UserProfilePermission
{
    const MENU     = 0;
    const SPECIFIC = 1;
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="UserProfile", inversedBy="permissions")
     * @ORM\JoinColumn(name="profileId", referencedColumnName="id")
     */
    private $profile;

    /**
     * @var string
     *
     * @ORM\Column(name="route", type="string", length=255)
     */
    private $route;

    /**
     * @var string
     *
     * @ORM\Column(name="params", type="string", length=255, nullable=true)
     */
    private $params;

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
     * Set profile
     *
     * @param integer $profile
     * @return UserProfileRoute
     */
    public function setProfile($profile)
    {
        $this->profile = $profile;

        return $this;
    }

    /**
     * Get profile
     *
     * @return integer
     */
    public function getProfile()
    {
        return $this->profile;
    }

    /**
     * Set route
     *
     * @param string $route
     * @return UserProfileRoute
     */
    public function setRoute($route)
    {
        $this->route = $route;

        return $this;
    }

    /**
     * Get route
     *
     * @return string
     */
    public function getRoute()
    {
        return $this->route;
    }

    /**
     * Set params
     *
     * @param string $params
     * @return UserProfileRoute
     */
    public function setParams($params)
    {
        $this->params = $params;

        return $this;
    }

    /**
     * Get params
     *
     * @return string
     */
    public function getParams()
    {
        return $this->params;
    }
}
