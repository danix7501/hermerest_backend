<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\JoinColumn;
use Doctrine\ORM\Mapping\ManyToOne;
/**
 * Schedule
 * @ORM\Entity
 * @ORM\Table(name="schedule")
 */
class Schedule
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
     * @ManyToOne(targetEntity="Teacher", inversedBy="schedules")
     * @JoinColumn(name="id_teacher", referencedColumnName="id", nullable=false)
     */
    private $teacher;

    /**
     * @ManyToOne(targetEntity="Student", inversedBy="schedules")
     * @JoinColumn(name="id_student", referencedColumnName="id")
     */
    private $student;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="schedule", type="date")
     */
    private $schedule;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_from", type="time")
     */
    private $timeFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time_to", type="time")
     */
    private $timeTo;

    /**
     * @var int
     *
     * @ORM\Column(name="status", type="integer")
     */
    private $status;


    public function __construct($schedule = null, $timeFrom = null, $timeTo = null, $status = null, Teacher $teacher = null, Student $student = null)
    {
        $this->teacher = $teacher;
        $this->student = $student;
        $this->schedule = $schedule;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
        $this->status = $status;

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
     * Set teacher
     *
     * @param Teacher $teacher
     *
     * @return Schedule
     */
    public function setTeacher(Teacher $teacher = null)
    {
        $this->teacher = $teacher;

        return $this;
    }

    /**
     * Get teacher
     *
     * @return Teacher
     */
    public function getTeacher()
    {
        return $this->teacher;
    }

    /**
     * Set student
     *
     * @param Student $student
     *
     * @return Schedule
     */
    public function setStudent(Student $student = null)
    {
        $this->student = $student;

        return $this;
    }

    /**
     * Get student
     *
     * @return Student
     */
    public function getStudent()
    {
        return $this->student;
    }

    /**
     * Set schedule
     *
     * @param \DateTime $schedule
     *
     * @return Schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;

        return $this;
    }

    /**
     * Get schedule
     *
     * @return \DateTime
     */
    public function getSchedule()
    {
        return $this->schedule;
    }

    /**
     * Set timeFrom
     *
     * @param \DateTime $timeFrom
     *
     * @return Schedule
     */
    public function setTimeFrom($timeFrom)
    {
        $this->timeFrom = $timeFrom;

        return $this;
    }

    /**
     * Get timeFrom
     *
     * @return \DateTime
     */
    public function getTimeFrom()
    {
        return $this->timeFrom;
    }

    /**
     * Set timeTo
     *
     * @param \DateTime $timeTo
     *
     * @return Schedule
     */
    public function setTimeTo($timeTo)
    {
        $this->timeTo = $timeTo;

        return $this;
    }

    /**
     * Get timeTo
     *
     * @return \DateTime
     */
    public function getTimeTo()
    {
        return $this->timeTo;
    }


    /**
     * Set status
     *
     * @param integer $status
     *
     * @return integer
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return integer
     */
    public function getStatus()
    {
        return $this->status;
    }
}

