<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 20:24
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Centre;
use AppBundle\Normalizers\AuthorizationNormalizer;
use AppBundle\Normalizers\CircularNormalizer;
use AppBundle\Normalizers\CourseNormalizer;
use AppBundle\Normalizers\PollNormalizer;
use AppBundle\Normalizers\ProgenitorNormalizer;
use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Normalizers\TeacherNormalizer;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/centres")
 */

class CentresController extends Controller
{

    private $centreFacade;
    private $progenitorFacade;
    private $responseFactory;
    private $utils;

    public function __construct(CentreFacade $centreFacade,
                                ProgenitorFacade $progenitorFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->centreFacade = $centreFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("/{id}", name="listarCentrosContienePadre")
     * @Method("GET")
     */
    public function getCentresAction($id)
    {
        $parentCentres = $this->progenitorFacade->find($id)->getCentres();
        $centres = $this->centreFacade->findAll();
        $centresContent = array();
        foreach ($centres as $centre)
        {
            array_push($centresContent,
                [
                    'id'=>$centre->getId(),
                    'name' => $centre->getName(),
                    'isSet' => $parentCentres->contains($centre)
                ]);
        }
        return $this->responseFactory->successfulJsonResponse($centresContent);
    }

    /**
     * @Route("/{id}/courses", name="listarCursosDelCentro")
     * @Method("GET")
     */
    public function getCoursesAction(Request $request, $id)
    {
        $centro= $this->centreFacade->find($id);
        if ($centro == null) return $this->responseFactory->unsuccessfulJsonResponse("El centro no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['courses' =>
                $this->utils->serializeArray(
                    $centro->getCourses(), new CourseNormalizer()
                )
            ]
        );
    }

    /**
    * @Route("/{id}/parents", name="listarPadresDelCentro")
    * @Method("GET")
    */
    public function getParentsAction(Request $request, $id)
    {
        $centro= $this->centreFacade->find($id);
        if ($centro == null) return $this->responseFactory->unsuccessfulJsonResponse("El centro no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['parents' =>
                $this->utils->serializeArray(
                    $centro->getParents(), new ProgenitorNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{id}/teachers", name="listarProfesoresDelCentro")
     * @Method("GET")
     */
    public function getTeachersAction(Request $request, $id)
    {
        $centro= $this->centreFacade->find($id);
        if ($centro == null) return $this->responseFactory->unsuccessfulJsonResponse("El centro no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['teachers' =>
                $this->utils->serializeArray(
                    $centro->getTeachers(), new TeacherNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{id}/students", name="listarAlumnosDelCentro")
     * @Method("GET")
     */
    public function getStudentsAction(Request $request, $id)
    {
        $centre = $this->centreFacade->find($id);
        if ($centre == null) return $this->responseFactory->unsuccessfulJsonResponse("El centro no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['students' =>
                $this->utils->serializeArray(
                    $centre->getStudents(), new StudentNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{id}/circulars", name="listarCircularesDelCentro")
     * @Method("GET")
     */
    public function getCircularsAction(Request $request, $id)
    {
        $centre = $this->centreFacade->find($id);

        return $this->responseFactory->successfulJsonResponse(
            ['circulars' =>
                $this->utils->serializeArray(
                    $centre->getMessagesOfType('Circular'), new CircularNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{id}/polls", name="listarEncuestasDelCentro")
     * @Method("GET")
     */
    public function getPollsAction(Request $request, $id)
    {
        $centre = $this->centreFacade->find($id);

        return $this->responseFactory->successfulJsonResponse(
            ['polls' =>
                $this->utils->serializeArray(
                    $centre->getMessagesOfType('Poll'), new PollNormalizer()
                )

            ]
        );
    }

    /**
     * @Route("/{id}/authorizations", name="listarAutorizacionesDelCentro")
     * @Method("GET")
     */
    public function getAuthorizationsAction(Request $request, $id)
    {
        $centre = $this->centreFacade->find($id);
        return $this->responseFactory->successfulJsonResponse(
            ['authorizations' =>
                $this->utils->serializeArray(
                    $centre->getMessagesOfType('Authorization'), new AuthorizationNormalizer()
                )
            ]
        );
    }

}