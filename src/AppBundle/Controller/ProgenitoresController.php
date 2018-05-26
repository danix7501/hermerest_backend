<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 26/5/18
 * Time: 15:58
 */

namespace AppBundle\Controller;

use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\StudentFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/parents")
 */
class ProgenitoresController extends Controller
{
    private $progenitorFacade;
    private $responseFactory;
    private $utils;

    public function __construct(ProgenitorFacade $progenitorFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->progenitorFacade = $progenitorFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("/{id}/childrens", name="listarHijosDelPadre")
     * @Method("GET")
     */
    public function getStudentsAction(Request $request, $id)
    {
        $parent = $this->progenitorFacade->find($id);
        if ($parent == null) return $this->responseFactory->unsuccessfulJsonResponse("El padre no existe");

        return $this->responseFactory->successfulJsonResponse(
            ['childrens' =>
                $this->utils->serializeArray(
                    $parent->getChildren(), new StudentNormalizer()
                )
            ]
        );
    }
}