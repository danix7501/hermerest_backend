<?php
/**
 * Created by PhpStorm.
 * User: alour
 * Date: 13/04/2017
 * Time: 21:08
 */

namespace AppBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="circular")
 */
class Circular extends Message
{
    public function __construct($subject = null, $message = null, ?DateTime $sendingDate = null, ?Centre $centre = null)
    {
        parent::__construct($subject, $message, $sendingDate, $centre);
    }
}
