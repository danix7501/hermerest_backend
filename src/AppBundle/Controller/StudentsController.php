<?php
/**
 * Created by PhpStorm.
 * User: Daniel Romero
 * Date: 19/05/2018
 * Time: 14:13
 */

namespace AppBundle\Controller;


use AppBundle\Normalizers\ProgenitorNormalizer;
use AppBundle\Normalizers\StudentNormalizer;
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
    private $responseFactory;
    private $utils;

    public function __construct(StudentFacade $studentFacade,
                                ProgenitorFacade $progenitorFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->studentFacade = $studentFacade;
        $this->progenitorFacade = $progenitorFacade;
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
}