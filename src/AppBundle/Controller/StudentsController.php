<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 19/05/2018
 * Time: 14:13
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Student;
use AppBundle\Entity\Progenitor;
use AppBundle\Entity\Centre;
use AppBundle\Normalizers\AttachmentNormalizer;
use AppBundle\Normalizers\ProgenitorNormalizer;
use AppBundle\Normalizers\MessageNormalizer;
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
     * @Route("", name="listarAlumnosSinCurso")
     * @Method("GET")
     */
    public function getStudentsWithoutCourseAction(Request $request)
    {
        $students = $this->studentFacade->findAll();
        $studentsWithoutCourse = [];

        foreach ($students as $student) {
            if ($student->getCourse() == null) {
                array_push($studentsWithoutCourse, $student);
            }
        }

        return $this->responseFactory->successfulJsonResponse(
            ['students' =>
                $this->utils->serializeArray(
                    $studentsWithoutCourse, new StudentNormalizer()
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

        if ($request->get('course')) {
            $student = new Student();
            $student->setName($request->request->get('name'));
            $student->setSurname($request->request->get('surname'));
            $student->setCourse($course);
            $student->setCentre($centre);
        } else {
            $student = new Student();
            $student->setName($request->request->get('name'));
            $student->setSurname($request->request->get('surname'));
            $student->setCourse(NULL);
            $student->setCentre($centre);
        }

        $this->studentFacade->create($student);
        return $this->responseFactory->successfulJsonResponse((new StudentNormalizer())->normalize($student));
    }

    /**
     * @Route("/register", name="registrarAlumno")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {
        $course = $this->courseFacade->find($request->get('course'));
        $centre = $this->centreFacade->find($request->get('centre'));
        $parents = $request->request->get('telephoneParents');

        $student = new Student();
        $student->setName($request->request->get('name'));
        $student->setSurname($request->request->get('surname'));
        $student->setCourse($course);
        $student->setCentre($centre);

        $this->studentFacade->create($student);

        if ($parents != null) {
            $parents = explode(',', $parents);
            foreach ($parents as $telephoneParent) {
                $parent = $this->progenitorFacade->findByTelephone($telephoneParent);
                $student->addParent($parent);
                $this->studentFacade->edit();
                $parent->addChild($student);
                $this->progenitorFacade->edit();
            }
        }

        return $this->responseFactory->successfulJsonResponse((new StudentNormalizer())->normalize($student));
    }

    /**
     * @Route("/{id}", name="editarAlumno")
     * @Method("PUT")
     */
    public function editAction(Request $request, $id)
    {
        $student = $this->studentFacade->find($id);
        $course = $this->courseFacade->find($request->get('course'));
        if ($student == null) return $this->responseFactory->unsuccessfulJsonResponse("El alumno no existe");

        $student->setName($request->request->get('name'));
        $student->setSurname($request->request->get('surname'));
        $student->setCourse($course);

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
        return $this->responseFactory->successfulJsonResponse([]);
    }

    /**
     * @Route("/{id}/course", name="desaociarCursoDelAlumno")
     * @Method("DELETE")
     */
    public function disassociateCourseAction(Request $request, $id)
    {
        $student = $this->studentFacade->find($id);
        if ($student == null) return $this->responseFactory->unsuccessfulJsonResponse('El alumno no existe');

        $student->setCourse(null);
        $this->studentFacade->edit();

        return $this->responseFactory->successfulJsonResponse([]);
    }

    /**
     * @Route("/{studentId}/parents/{parentTelephone}", name="asociarPadresAlumno")
     * @Method("POST")
     */
    public function associateParentsAction(Request $request, $studentId, $parentTelephone)
    {
        //TODO: return mensaje de asociar un padre que ya esta asociado a un alumno
        //TODO: controlar que un alumno no pueda tener mas de dos padres asociados
        $student = $this->studentFacade->find($studentId);
        $parent = $this->progenitorFacade->findByTelephone($parentTelephone);
        $centre = $student->getCentre();

        if ($parent == null) return $this->responseFactory->unsuccessfulJsonResponse('Este número no corresponde a ningún padre');

        if ($student != null && $parent != null) {

            $student->addParent($parent);
            $this->studentFacade->edit();
            $parent->addChild($student);
            $this->progenitorFacade->edit();
        }

        if ($parent->getCentres() == null) {
            $centre->addParent($parent);
            $this->progenitorFacade->edit();
            $parent->addCentre($centre);
            $this->centreFacade->edit();
        }

        return $this->responseFactory->successfulJsonResponse(
            ['parents' =>
                $this->utils->serializeArray(
                    $student->getParents(), new ProgenitorNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{id}/messages", name="listarMensajesDeAlumno")
     * @Method("GET")
     */
    public function getAuthorizationsOfStudentAction(Request $request, $id)
    {
        // TODO: listar solo los mensajes que no esten contestados y los que no se hayan pasado la fecha limite
        $student = $this->studentFacade->find($id);
        return $this->responseFactory->successfulJsonResponse(
            ['messages' =>
                $this->utils->serializeArray(
                    $student->getMessages(), new MessageNormalizer()
                )
            ]
        );
    }
}