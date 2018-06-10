<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 10/6/18
 * Time: 20:22
 */

namespace AppBundle\Controller;


use AppBundle\Normalizers\ScheduleNormalizer;
use AppBundle\Services\Facades\ScheduleFacade;
use AppBundle\Services\Facades\TeacherFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * @Route("/teachers")
 */
class TeachersController extends Controller
{
    private $scheduleFacade;
    private $teacherFacade;
    private $responseFactory;
    private $utils;

    public function __construct(ScheduleFacade $scheduleFacade,
                                TeacherFacade $teacherFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->scheduleFacade = $scheduleFacade;
        $this->teacherFacade = $teacherFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("/{id}/schedules", name="verHorariosDeTutoriaProfesor")
     * @Method("GET")
     */
    public function createAction(Request $request, $id)
    {
        $teacher = $this->teacherFacade->find($id);

        return $this->responseFactory->successfulJsonResponse(
            ['schedules' => $this->utils->serializeArray(
                    $teacher->getSchedules(), new ScheduleNormalizer()
                )
        ]);
    }

}