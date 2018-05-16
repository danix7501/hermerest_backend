<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 0:20
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use AppBundle\Services\Facades\CourseFacade;
use AppBundle\Services\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

class CoursesController
{

    /**
     * @Route("/courses", name="courses")
     * @Method("GET")
     */
    public function coursesAction(Request $request)
    {
    echo 'cursos';
    die();
    }

}