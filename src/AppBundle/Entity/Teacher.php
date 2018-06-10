<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
use Doctrine\Common\Collections\ArrayCollection;


/**
 * Teacher
 * @ORM\Entity
 * @ORM\Table(name="teacher")
 */
class Teacher
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="User")
     * @JoinColumn(name="id_user", referencedColumnName="id")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Centre")
     * @ORM\JoinColumn(name="centre", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    private $centre;

    /**
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @ORM\ManyToMany(targetEntity="Progenitor", inversedBy="teachers")
     * @ORM\OrderBy({"name" = "ASC"})
     * @ORM\JoinTable(name="teacher_parent",
     *      joinColumns={@ORM\JoinColumn(name="teacher", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="parent", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $parents;


    public function __construct($name = null)
    {
        $this->parents = new ArrayCollection();
        $this->name = $name;
    }

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
     * Set idUser
     *
     * @param string $user
     *
     * @return Teacher
     */
    public function setUser($user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get idUser
     *
     * @return string
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set centre
     *
     * @param integer $centre
     *
     * @return Teacher
     */
    public function setCentre($centre)
    {
        $this->centre = $centre;

        return $this;
    }

    /**
     * Get centre
     *
     * @return int
     */
    public function getCentre()
    {
        return $this->centre;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Teacher
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
}

