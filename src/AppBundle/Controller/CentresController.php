<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 17/5/18
 * Time: 20:24
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Centre;
use AppBundle\Normalizers\CourseNormalizer;
use AppBundle\Services\Facades\CentreFacade;
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
    private $responseFactory;
    private $utils;

    public function __construct(CentreFacade $centreFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->centreFacade = $centreFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
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

}