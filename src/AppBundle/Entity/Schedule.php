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
     * @JoinColumn(name="id_teacher", referencedColumnName="id")
     */
    private $teacher;

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

    public function __construct($schedule = null, $timeFrom = null, $timeTo = null, Teacher $teacher = null)
    {
        $this->teacher = $teacher;
        $this->schedule = $schedule;
        $this->timeFrom = $timeFrom;
        $this->timeTo = $timeTo;
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
}

