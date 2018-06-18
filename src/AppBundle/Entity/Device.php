<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 18/06/2018
 * Time: 0:22
 */

namespace AppBundle\Entity;


use Doctrine\ORM\Mapping as ORM;

/**
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class Device {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id; // private or protected is necesary for doctrine

    /**
     * @var string $user
     *
     * @ORM\Column(name="user", type="string", length=150)
     */
    //this could be a foreign key, but in this example we have used a string
    private $user;

    /**
     * @var string $token
     *
     * @ORM\Column(name="token", type="string", length=255)
     */
    private $token;

    /**
     * @var string $trademark
     *
     * @ORM\Column(name="trademark", type="string", length=150)
     */
    private $manufacturer;



    function getId() {
        return $this->id;
    }

    function getToken() {
        return $this->token;
    }

    function setToken($token) {
        $this->token = $token;
    }

    // Dont make setId, doctrine MUST do it for you

    function getUser() {
        return $this->user;
    }

    function setUser($user) {
        $this->user = $user;
    }

    function getManufacturer() {
        return $this->manufacturer;
    }

    function setManufacturer($manufacturer) {
        $this->manufacturer = $manufacturer;
    }

}