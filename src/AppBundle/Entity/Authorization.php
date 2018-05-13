<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 13/04/2017
 * Time: 21:08
 */

namespace AppBundle\Entity;

use AppBundle\Entity\AuthorizationReply;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="authorization")
 */
class Authorization extends Message
{

    /**
     * @ORM\Column(type="datetimetz", name="limitDate", nullable=false)
     */
    private $limitDate;

    /**
     * @ORM\OneToMany(targetEntity="AuthorizationReply", mappedBy="authorization")
     */
    private $replies;

    public function __construct($subject = null, $message = null, ?DateTime $sendingDate = null, ?Centre $centre = null, ?DateTime $limitDate = null)
    {
        $this->replies = new ArrayCollection();
        $this->limitDate = $limitDate;
        parent::__construct($subject, $message, $sendingDate, $centre);
    }

    /**
     * Set limitDate
     *
     * @param \DateTime $limitDate
     *
     * @return Authorization
     */
    public function setLimitDate($limitDate)
    {
        $this->limitDate = $limitDate;

        return $this;
    }

    /**
     * Get limitDate
     *
     * @return \DateTime
     */
    public function getLimitDate()
    {
        return $this->limitDate;
    }

    /**
     * Add reply
     *
     * @param AuthorizationReply $reply
     *
     * @return Authorization
     */
    public function addReply(AuthorizationReply $reply)
    {
        $this->replies[] = $reply;

        return $this;
    }

    /**
     * Remove reply
     *
     * @param AuthorizationReply $reply
     */
    public function removeReply(AuthorizationReply $reply)
    {
        $this->replies->removeElement($reply);
    }

    /**
     * Get replies
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getReplies()
    {
        return $this->replies;
    }
}
