<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 18/5/18
 * Time: 22:45
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Administrator;
use AppBundle\Entity\User;
use AppBundle\Normalizers\AdministratorNormalizer;
use AppBundle\Services\Facades\AdministratorFacade;
use AppBundle\Services\Facades\UserFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/administrators")
 */

class AdministratorController extends Controller
{

    private $administratorFacade;
    private $userFacade;
    private $responseFactory;
    private $utils;

    public function __construct(AdministratorFacade $administratorFacade,
                                UserFacade $userFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->administratorFacade = $administratorFacade;
        $this->userFacade = $userFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("/{id}", name="verAdministrador")
     * @Method("GET")
     */
    public function getAction(Request $request, $id)
    {
        $administrator = $this->administratorFacade->find($id);
        if ($administrator == null) return $this->responseFactory->unsuccessfulJsonResponse("El administrador no existe");


        return $this->responseFactory->successfulJsonResponse(
            (new AdministratorNormalizer())->normalize($administrator)
        );

    }
}