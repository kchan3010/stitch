<?php

namespace StitchLabs\DemoBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Organisation
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="StitchLabs\DemoBundle\Entity\OrganisationRepository")
 */
class Organisation
{
    /**
     * @var integer
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
     * @ORM\OneToMany(targetEntity="User", mappedBy="organisation")
     *
     * @var Doctrine\Common\Collections\Collection $users
     */
    private $users;


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
     * @return Organisation
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
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add users
     *
     * @param \StitchLabs\DemoBundle\Entity\User $users
     * @return Organisation
     */
    public function addUser(\StitchLabs\DemoBundle\Entity\User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param \StitchLabs\DemoBundle\Entity\User $users
     */
    public function removeUser(\StitchLabs\DemoBundle\Entity\User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
