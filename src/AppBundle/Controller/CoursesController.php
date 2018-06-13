<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 0:20
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Course;
use AppBundle\Entity\Student;
use AppBundle\Entity\Centre;
use AppBundle\Entity\Progenitor;
use AppBundle\Normalizers\CourseNormalizer;
use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Services\Facades\CourseFacade;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\Facades\TeacherFacade;
use AppBundle\Services\Utils;
use AppBundle\Services\ResponseFactory;
use League\Csv\Reader;
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
    private $studentFacade;
    private $progenitorFacade;
    private $teacherFacade;
    private $responseFactory;
    private $utils;

    public function __construct(CourseFacade $courseFacade,
                                CentreFacade $centreFacade,
                                StudentFacade $studentFacade,
                                ProgenitorFacade $progenitorFacade,
                                TeacherFacade $teacherFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->courseFacade = $courseFacade;
        $this->centreFacade = $centreFacade;
        $this->studentFacade = $studentFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->teacherFacade = $teacherFacade;
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
        return $this->responseFactory->successfulJsonResponse((new CourseNormalizer())->normalize($course));
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

    /**
     * @Route("/importCourse", name="importarCurso")
     * @Method("POST")
     */
    public function importCourse(Request $request) {

        $centre = $this->centreFacade->find($_POST['centre']);

        $tempFile = $_FILES['file']['tmp_name'];
        $fileName = $_FILES['file']['name'];
        $filePath = $_SERVER['DOCUMENT_ROOT'] .'/hermerest_backend/src/AppBundle/Uploads/'. $fileName;
        move_uploaded_file($tempFile, $filePath);

        $reader = Reader::createFromPath('%kernel.root_dir%/../../src/AppBundle/Uploads/'.$fileName);
        $results = $reader->fetchAssoc();

        foreach ($results as $row) {
            $teacher = $this->teacherFacade->findByNameOfTeacher($row['Profesor']);
            $course = $this->courseFacade->findByNameOfCourse($row['NombreCurso']);
            if ($course == null) {
                $course = new Course();
                $course->setName($row['NombreCurso']);
                $course->setCentre($centre);
                $course->setTeacher($teacher);
                $this->courseFacade->create($course);
            }

            $student = $this->studentFacade->findByNameAndSurnameOfStudent($row['NombreAlumno'], $row['ApellidosAlumno']);
            if ($student == null) {
                $student = new Student();
                $student->setName($row['NombreAlumno']);
                $student->setSurname($row['ApellidosAlumno']);
                $student->setCourse($course);
                $student->setCentre($centre);
                $this->studentFacade->create($student);
                $this->relateParentsWithStudents($centre, $student, $row['TelefonoPadres'], $row['NombrePadres']);
            } else {
                $this->relateParentsWithStudents($centre, $student, $row['TelefonoPadres'], $row['NombrePadres']);
            }

        }
//        // TODO: eliminar el fichero guardado en UPLOADS una vez importados los datos
        return $this->responseFactory->successfulJsonResponse([]);

    }

    private function relateParentsWithStudents($centre, $student, $telephoneParents, $nameParents)
    {
        $progenitor = $this->progenitorFacade->findByTelephone($telephoneParents);
        if ($progenitor == null) {
            $progenitor = new Progenitor();
            $progenitor->setName($nameParents);
            $progenitor->setTelephone($telephoneParents);
            $progenitor->addCentre($centre);
            $progenitor->addChild($student);
            $this->progenitorFacade->create($progenitor);

            $student->addParent($progenitor);
            $this->studentFacade->edit();

            $centre->addParent($progenitor);
            $this->centreFacade->edit();
        }
    }
}