<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 13/04/2017
 * Time: 21:08
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Attachment;
use AppBundle\Entity\Centre;
use AppBundle\Entity\Student;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="message")
 * @ORM\InheritanceType("JOINED")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({"authorization" = "Authorization", "circular" = "Circular", "poll" = "Poll"})
 */
abstract class Message
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="subject", nullable=false)
     */
    private $subject;

    /**
     * @ORM\Column(type="text", length=65535, name="message", nullable=false)
     */
    private $message;

    /**
     * @ORM\Column(type="datetimetz", name="sendingDate", nullable=false)
     */
    private $sendingDate;

    /**
     * @ORM\ManyToOne(targetEntity="Centre", inversedBy="messages")
     * @ORM\JoinColumn(name="centre", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    private $centre;

    /**
     * @ORM\ManyToMany(targetEntity="Student", inversedBy="messages")
     * @ORM\OrderBy({"surname" = "ASC", "name" = "ASC"})
     * @ORM\JoinTable(name="message_student",
     *      joinColumns={@ORM\JoinColumn(name="message", referencedColumnName="id", onDelete="cascade")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="student", referencedColumnName="id", onDelete="cascade")}
     *      )
     */
    private $students;

    /**
     * @ORM\OneToMany(targetEntity="Attachment", mappedBy="message")
     * @ORM\OrderBy({"name" = "ASC"})
     */
    private $attachments;

    public function __construct($subject, $message, ?DateTime $sendingDate, ?Centre $centre)
    {
        $this->students = new ArrayCollection();
        $this->attachments = new ArrayCollection();
        $this->subject = $subject;
        $this->message = $message;
        $this->sendingDate = $sendingDate;
        $this->centre = $centre;
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
     * Set subject
     *
     * @param string $subject
     *
     * @return Message
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;

        return $this;
    }

    /**
     * Get subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * Set message
     *
     * @param string $message
     *
     * @return Message
     */
    public function setMessage($message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * Set sendingDate
     *
     * @param \DateTime $sendingDate
     *
     * @return Message
     */
    public function setSendingDate($sendingDate)
    {
        $this->sendingDate = $sendingDate;

        return $this;
    }

    /**
     * Get sendingDate
     *
     * @return \DateTime
     */
    public function getSendingDate()
    {
        return $this->sendingDate;
    }

    /**
     * Set centre
     *
     * @param Centre $centre
     *
     * @return Message
     */
    public function setCentre(Centre $centre)
    {
        $this->centre = $centre;

        return $this;
    }

    /**
     * Get centre
     *
     * @return Centre
     */
    public function getCentre()
    {
        return $this->centre;
    }

    /**
     * Add student
     *
     * @param Student $student
     *
     * @return Message
     */
    public function addStudent(Student $student)
    {
        $this->students[] = $student;

        return $this;
    }

    /**
     * Remove student
     *
     * @param Student $student
     */
    public function removeStudent(Student $student)
    {
        $this->students->removeElement($student);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * Add attachment
     *
     * @param Attachment $attachment
     *
     * @return Message
     */
    public function addAttachment(Attachment $attachment)
    {
        $this->attachments[] = $attachment;

        return $this;
    }

    /**
     * Remove attachment
     *
     * @param Attachment $attachment
     */
    public function removeAttachment(Attachment $attachment)
    {
        $this->attachments->removeElement($attachment);
    }

    /**
     * Get attachments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttachments()
    {
        return $this->attachments;
    }
}
