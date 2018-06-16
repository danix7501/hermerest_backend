<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 10/6/18
 * Time: 20:22
 */

namespace AppBundle\Controller;

use AppBundle\Entity\Schedule;
use AppBundle\Normalizers\ScheduleNormalizer;
use AppBundle\Normalizers\StudentNormalizer;
use AppBundle\Services\Facades\ScheduleFacade;
use AppBundle\Services\Facades\TeacherFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use DateTime;
use DateTimeZone;
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
     * @Route("/{id}/schedules", name="listarHorariosDeTutoriaProfesor")
     * @Method("GET")
     */
    public function getAction(Request $request, $id)
    {
        $teacher = $this->teacherFacade->find($id);
        $schedules = [];

        if ($request->query->get('schedule')) {
            foreach ($teacher->getSchedules() as $schedule) {
                $scheduleAux = $schedule->getSchedule()->format('Y-m-d');
                if ($scheduleAux == $request->query->get('schedule')){
                    array_push($schedules, $schedule);
                }
            }
            return $this->responseFactory->successfulJsonResponse(
                ['schedules' => $this->utils->serializeArray(
                    $schedules, new ScheduleNormalizer()
                )
                ]);
        } else {
            return $this->responseFactory->successfulJsonResponse(
                ['schedules' => $this->utils->serializeArray(
                    $teacher->getSchedules(), new ScheduleNormalizer()
                )
                ]);
        }
    }

    /**
     * @Route("/{id}/students", name="listarAlumnosDelProfesor")
     * @Method("GET")
     */
    public function getStudentsAction(Request $request, $id)
    {
        $teacher = $this->teacherFacade->find($id);
        if ($teacher == null) return $this->responseFactory->unsuccessfulJsonResponse('El profesor no existe');

        return $this->responseFactory->successfulJsonResponse(
            [ 'students' =>
                $this->utils->serializeArray(
                    $teacher->getStudents(), new StudentNormalizer()
                )
            ]
        );
    }

}