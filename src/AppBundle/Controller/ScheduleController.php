<?php
/**
 * Created by PhpStorm.
 * User: danielromerocalero
 * Date: 10/6/18
 * Time: 18:39
 */

namespace AppBundle\Controller;


use AppBundle\Entity\Appointment;
use AppBundle\Entity\Schedule;
use AppBundle\Services\Facades\AppointmentFacade;
use AppBundle\Services\Facades\ScheduleFacade;
use AppBundle\Services\Facades\TeacherFacade;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use DateInterval;
use DateTime;
use DateTimeZone;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


/**
 * @Route("/schedules")
 */
class ScheduleController extends Controller
{
    private $scheduleFacade;
    private $teacherFacade;
    private $appointmentFacade;
    private $responseFactory;
    private $utils;

    public function __construct(ScheduleFacade $scheduleFacade,
                                TeacherFacade $teacherFacade,
                                AppointmentFacade $appointmentFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->scheduleFacade = $scheduleFacade;
        $this->teacherFacade = $teacherFacade;
        $this->appointmentFacade = $appointmentFacade;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
    }

    /**
     * @Route("", name="crearHorarioDeTutoria")
     * @Method("POST")
     */
    public function createAction(Request $request)
    {

        $dateFrom = new DateTime($request->request->get('dateFrom'), new DateTimeZone('Atlantic/Canary'));
        $dateTo = new DateTime($request->request->get('dateTo'), new DateTimeZone('Atlantic/Canary'));
        $timeFrom = new DateTime($request->request->get('timeFrom'), new DateTimeZone('Atlantic/Canary'));
        $timeTo = new DateTime($request->request->get('timeTo'), new DateTimeZone('Atlantic/Canary'));
        $daysOfWek = $request->request->get('daysOfWek');
        $teacher = $this->teacherFacade->find($request->request->get('teacher'));

        while ($dateFrom <= $dateTo) {
            foreach ($daysOfWek as $dayOfWek){
                if ($dateFrom->format('w') == $dayOfWek){
                    $checkSchedule = $this->scheduleFacade->checkSchedule($teacher, $dateFrom, $timeFrom, $timeTo);
                    if ($checkSchedule == null) {
                        $schedule = new Schedule();
                        $schedule->setSchedule($dateFrom);
                        $schedule->setTimeFrom($timeFrom);
                        $schedule->setTimeTo($timeTo);
                        $schedule->setTeacher($teacher);
                        $this->scheduleFacade->create($schedule);
                    }
                }
            }
            $dateFrom->add(new DateInterval('P1D'));
        }

        return $this->responseFactory->successfulJsonResponse('Horario creado correctamente');
    }


    /**
     * @Route("/{id}", name="editarHorarioDeTutoria")
     * @Method("PUT")
     */
    public function editAction(Request $request, $id)
    {
        $schedule = $this->scheduleFacade->find($id);

        $schedule->setSchedule(new DateTime($request->request->get('schedule'), new DateTimeZone('Atlantic/Canary')));
        $schedule->setTimeFrom(new DateTime($request->request->get('timeFrom'), new DateTimeZone('Atlantic/Canary')));
        $schedule->setTimeTo(new DateTime($request->request->get('timeTo'), new DateTimeZone('Atlantic/Canary')));
        $this->scheduleFacade->edit();

        return $this->responseFactory->successfulJsonResponse('Horario de tutoría modificado con éxito');
    }

    /**
     * @Route("/{id}", name="eliminarHorarioDeTutoria")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $schedule = $this->scheduleFacade->find($id);
        $appointmentSchedule = $this->appointmentFacade->findBySchedule($schedule);
        if ($appointmentSchedule != null) return $this->responseFactory->unsuccessfulJsonResponse('Este horario tiene una cita asociada');
        $this->scheduleFacade->remove($schedule);
        return $this->responseFactory->successfulJsonResponse('Horario eliminado correctamente');
    }


}