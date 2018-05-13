<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 14/04/2017
 * Time: 0:06
 */

namespace AppBundle\Entity;

use AppBundle\Entity\Message;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="attachment")
 */
class Attachment
{
    /**
     * @ORM\Column(type="integer", name="id")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255, name="name", nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Message", inversedBy="attachments")
     * @ORM\JoinColumn(name="message", referencedColumnName="id", nullable=false, onDelete="cascade")
     */
    private $message;

    public function __construct($name = null, Message $message = null)
    {
        $this->name = $name;
        $this->message = $message;
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
     *
     * @return Attachment
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
     * Set message
     *
     * @param Message $message
     *
     * @return Attachment
     */
    public function setMessage(Message $message)
    {
        $this->message = $message;

        return $this;
    }

    /**
     * Get message
     *
     * @return Message
     */
    public function getMessage()
    {
        return $this->message;
    }
}
