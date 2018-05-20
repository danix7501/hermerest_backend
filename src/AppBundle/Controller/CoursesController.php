<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 0:20
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Services\Facades\CourseFacade;
use AppBundle\Services\Utils;
use AppBundle\Services\ResponseFactory;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/courses")
 */
class CoursesController
{
    private $courseFacade;
    private $responseFactory;
    private $utils;

    public function __construct(CourseFacade $courseFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->courseFacade = $courseFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }
    /**
     * @Route("/{id}/students", name="listarAlumnosdelCurso")
     * @Method("GET")
     */
    public function getStudentsAction(Request $request, $id)
    {
        $course= $this->courseFacade->find($id);
        if ($course == null) return $this->responseFactory->unsuccessfulJsonResponse("El curso no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['students' =>
                $this->utils->serializeArray(
                    $course->getStudents(), new StudentNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{id}", name="eliminarCurso")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $course = $this->courseFacade->find($id);
        if ($course == null) return $this->responseFactory->unsuccessfulJsonResponse("El curso no existe");

        $this->courseFacade->remove($course);
        return $this->responseFactory->successfulJsonResponse(
            []
        );
    }
}