<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 19/05/2018
 * Time: 14:13
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Student;
use AppBundle\Normalizers\ProgenitorNormalizer;
use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\CourseFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/students")
 */
class StudentsController extends Controller
{
    private $studentFacade;
    private $progenitorFacade;
    private $courseFacade;
    private $centreFacade;
    private $responseFactory;
    private $utils;

    public function __construct(StudentFacade $studentFacade,
                                ProgenitorFacade $progenitorFacade,
                                CourseFacade $courseFacade,
                                CentreFacade $centreFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->studentFacade = $studentFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->courseFacade = $courseFacade;
        $this->centreFacade = $centreFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }
    /**
     * @Route("/{id}", name="verAlumno")
     * @Method("GET")
     */
    public function getStudentAction(Request $request, $id)
    {
        $student = $this->studentFacade->find($id);
        if ($student == null) return $this->responseFactory->unsuccessfulJsonResponse("El alumno no existe");

        return $this->responseFactory->successfulJsonResponse(
            (new StudentNormalizer())->normalize($student)
        );
    }

    /**
     * @Route("/{id}/parents", name="listarPadresDeAlumno")
     * @Method("GET")
     */
    public function getParentsAction(Request $request, $id)
    {
        $student = $this->studentFacade->find($id);
        if ($student == null) return $this->responseFactory->unsuccessfulJsonResponse("El alumno no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['parents' =>
                $this->utils->serializeArray(
                    $student->getParents(), new ProgenitorNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("", name="crearAlumno")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {
        $course = $this->courseFacade->find($request->get('course'));
        $centre = $this->centreFacade->find($request->get('centre'));

        $student = new Student();
        $student->setName($request->request->get('name'));
        $student->setSurname($request->request->get('surname'));
        $student->setCourse($course);
        $student->setCentre($centre);

        $this->studentFacade->create($student);
        return $this->responseFactory->successfulJsonResponse((new StudentNormalizer())->normalize($student));
    }

    /**
     * @Route("/{id}", name="editarAlumno")
     * @Method("PUT")
     */
    public function editAction(Request $request, $id)
    {
        $student = $this->studentFacade->find($id);
        if ($student == null) return $this->responseFactory->unsuccessfulJsonResponse("El alumno no existe");

        $student->setName($request->request->get('name'));
        $student->setSurname($request->request->get('surname'));

        $this->studentFacade->edit();
        return $this->responseFactory->successfulJsonResponse((new StudentNormalizer())->normalize($student));
    }


    /**
     * @Route("/{id}", name="eliminarAlumno")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $student = $this->studentFacade->find($id);
        if ($student == null) return $this->responseFactory->unsuccessfulJsonResponse("El alumno no existe");

        $this->studentFacade->remove($student);
        return $this->responseFactory->successfulJsonResponse(
            []
        );
    }
}