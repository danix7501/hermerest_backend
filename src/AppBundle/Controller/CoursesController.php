<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 0:20
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use AppBundle\Entity\Centre;
use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Services\Facades\CourseFacade;
use AppBundle\Services\Facades\CentreFacade;
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
    private $centreFacade;
    private $responseFactory;
    private $utils;

    public function __construct(CourseFacade $courseFacade,
                                CentreFacade $centreFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->courseFacade = $courseFacade;
        $this->centreFacade = $centreFacade;
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
     * @Route("", name="crearCurso")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $centre = $this->centreFacade->find($request->get('centre'));

        $course = new Course();
        $course->setName($request->request->get('name'));
        $course->setCentre($centre);

        $this->courseFacade->create($course);
        return $this->responseFactory->successfulJsonResponse([]);
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