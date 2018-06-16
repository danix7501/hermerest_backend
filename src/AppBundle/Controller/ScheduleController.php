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
use AppBundle\Services\Facades\StudentFacade;
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
    private $studentFacade;
    private $responseFactory;
    private $utils;

    public function __construct(ScheduleFacade $scheduleFacade,
                                TeacherFacade $teacherFacade,
                                StudentFacade $studentFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->scheduleFacade = $scheduleFacade;
        $this->teacherFacade = $teacherFacade;
        $this->studentFacade = $studentFacade;
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
        $segment = $request->request->get('segment');
        $teacher = $this->teacherFacade->find($request->request->get('teacher'));
        while ($dateFrom <= $dateTo) {
            $daysOfWek = explode(',', $daysOfWek);
            foreach ($daysOfWek as $dayOfWek){
                if ($dateFrom->format('w') == $dayOfWek){
                    $timeFromAux = clone  $timeFrom;
                    $timeToAux =  clone $timeFrom;
                    $timeToAux = $timeToAux->add(new DateInterval('PT' .$segment . 'M'));
                    while ($timeFromAux < $timeTo) {
                        $checkSchedule = $this->scheduleFacade->checkSchedule($teacher, $dateFrom, $timeFromAux, $timeToAux);
                        if ($checkSchedule == null) {
                            $schedule = new Schedule();
                            $schedule->setSchedule($dateFrom);
                            $schedule->setTimeFrom($timeFromAux);
                            $schedule->setTimeTo($timeToAux);
                            $schedule->setTeacher($teacher);
                            $schedule->setStatus(0);
                            $this->scheduleFacade->create($schedule);
                        }
                        $timeFromAux->add(new DateInterval('PT' .$segment . 'M'));
                        $timeToAux->add(new DateInterval('PT' .$segment . 'M'));
                    }
                }
            }
            $dateFrom->add(new DateInterval('P1D'));
        }

        return $this->responseFactory->successfulJsonResponse('Horario creado correctamente');
    }

    /**
     * @Route("/{idSchedule}/students/{idStudent}", name="asociarAlumnoAHorarioDeTutoria")
     * @Method("POST")
     */
    public function associatedStudentWithScheduleAction(Request $request, $idSchedule, $idStudent)
    {
        $schedule = $this->scheduleFacade->find($idSchedule);
        $student = $this->studentFacade->find($idStudent);

        $schedule->setStudent($student);
        $schedule->setStatus($request->request->get('status'));
        $this->scheduleFacade->edit();

        return $this->responseFactory->successfulJsonResponse('Alumno asociado a horario de tutoría correctamente');
    }

    /**
     * @Route("/{id}", name="editarHorarioDeTutoria")
     * @Method("PUT")
     */
    public function editAction(Request $request, $id)
    {
        $schedule = $this->scheduleFacade->find($id);

        if ($request->get('status')) {
            if ($schedule->getStudent() != null) {

                $schedule->setStatus($request->request->get('status'));
                $this->scheduleFacade->edit();

                switch ($request->request->get('status')) {
                    case 2: return $this->responseFactory->successfulJsonResponse('Horario de tutoría confirmado');
                        break;
                    case 3: return $this->responseFactory->successfulJsonResponse('Horario de tutoría cancelado');
                        break;
                }
            } else {
                return $this->responseFactory->successfulJsonResponse('No se puede cambiar el estado de un horario de tutoria que no esta asignado');
            }
        } else {

            $schedule->setSchedule(new DateTime($request->request->get('schedule'), new DateTimeZone('Atlantic/Canary')));
            $schedule->setTimeFrom(new DateTime($request->request->get('timeFrom'), new DateTimeZone('Atlantic/Canary')));
            $schedule->setTimeTo(new DateTime($request->request->get('timeTo'), new DateTimeZone('Atlantic/Canary')));
            $this->scheduleFacade->edit();

            return $this->responseFactory->successfulJsonResponse('Horario de tutoría modificado con éxito');
        }

    }

    /**
     * @Route("/{id}", name="eliminarHorarioDeTutoria")
     * @Method("DELETE")
     */
    public function deleteAction(Request $request, $id)
    {
        $schedule = $this->scheduleFacade->find($id);
        $this->scheduleFacade->remove($schedule);
        return $this->responseFactory->successfulJsonResponse('Horario de tutoría eliminado');
    }

    /**
     * @Route("/deleteRange", name="eliminarHorarioDeTutoriaPorRango")
     * @Method("POST")
     */
    public function deleteScheduleWithRangeAction(Request $request)
    {
        $teacher = $this->teacherFacade->find($request->request->get('teacher'));
        $dateFrom = new DateTime($request->request->get('dateFrom'), new DateTimeZone('Atlantic/Canary'));
        $dateTo = new DateTime($request->request->get('dateTo'), new DateTimeZone('Atlantic/Canary'));


        if ($teacher != null) {
            while ($dateFrom <= $dateTo) {

                $schedules = $this->scheduleFacade->findSchedulesByDateAnTeacher($dateFrom, $teacher);

                if ( $schedules != null) {
                    foreach ($schedules as $schedule) {
                        if (($schedule->getSchedule())->format('Y-m-d') == $dateFrom->format('Y-m-d') && $schedule->getStudent() == null && $schedule->getStatus() == 0) {
                            $this->scheduleFacade->remove($schedule);
                        }
                    }
                }
                $dateFrom->add(new DateInterval('P1D'));
            }
        }


        return $this->responseFactory->successfulJsonResponse('Horarios de tutorias eliminados desde ' .$dateFrom->format('Y-m-d'). ' hasta ' .$dateTo->format('Y-m-d'));

    }


}