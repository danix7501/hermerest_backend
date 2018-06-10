<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\OneToOne;
/**
 * Subject
 * @ORM\Entity
 * @ORM\Table(name="subject")
 */
class Subject
{
    /**
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @OneToOne(targetEntity="Teacher")
     * @JoinColumn(name="id_teacher", referencedColumnName="id")
     */
    private $teacher;

    /**
     * @OneToOne(targetEntity="Course")
     * @JoinColumn(name="id_course", referencedColumnName="id")
     */
    private $course;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;


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
     * Set idTeacher
     *
     * @param integer $teacher
     *
     * @return Subject
     */
    public function setTeacher($teacher)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get idTeacher
     *
     * @return int
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Set idCourse
     *
     * @param integer $course
     *
     * @return Subject
     */
    public function setCourse($course)
    {
        $this->course = $course;

        return $this;
    }

    /**
     * Get idCourse
     *
     * @return int
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return Subject
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

