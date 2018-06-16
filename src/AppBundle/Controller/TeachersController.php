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
use AppBundle\Services\Facades\UserFacade;
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
    private $userFacade;
    private $teacherFacade;
    private $responseFactory;
    private $utils;

    public function __construct(ScheduleFacade $scheduleFacade,
                                TeacherFacade $teacherFacade,
                                UserFacade $userFacade,
                                ResponseFactory $responseFactory,
                                Utils $utils)
    {
        $this->scheduleFacade = $scheduleFacade;
        $this->teacherFacade = $teacherFacade;
        $this->responseFactory = $responseFactory;
        $this->userFacade = $userFacade;
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

        $course = $teacher->getCourse();
        if ($course == null) return $this->responseFactory->unsuccessfulJsonResponse('El curso no existe');

        return $this->responseFactory->successfulJsonResponse(
            [ 'students' =>
                $this->utils->serializeArray(
                    $course->getStudents(), new StudentNormalizer()
                )
            ]
        );
    }

    /**
     * @Route("/{id}", name="verProfesor")
     * @Method("GET")
     */
    public function getTeacherAction(Request $request, $id)
    {
        $teacher = $this->teacherFacade->find($id);
        if ($teacher == null) return $this->responseFactory->unsuccessfulJsonResponse('El profesor no existe');

        return $this->responseFactory->successfulJsonResponse(
            [ 'teacher' =>
                [
                    'id' => $teacher->getId(),
                    'name' => $teacher->getName(),
                    'username' => $teacher->getUser()->getUsername(),
                    'sub' => $teacher->getUser()->getId()
                ]
            ]);
    }

    /**
     * @Route("/{id}/changePassword", name="cambiarContraseñaProfesor")
     * @Method("PUT")
     */
    public function changePasswordAction(Request $request, $id)
    {
        $user = $this->userFacade->find($id);
        if ($user == null) return $this->responseFactory->unsuccessfulJsonResponse('El usuario no existe');
        if($user->getPassword() == hash('sha256', $request->request->get('oldPassword'))){
            if($request->request->get('newPassword') == $request->request->get('confirmNewPassword')){
                $user->setPassword(hash('sha256', $request->request->get('newPassword')));
                $this->userFacade->edit();
                return $this->responseFactory->successfulJsonResponse('La contraseña ha sido cambiada correctamente.');
            }else{
                return $this->responseFactory->unsuccessfulJsonResponse('La contraseña nueva y la confirmacion deben coincidir.');
            }
        }else{
            return $this->responseFactory->unsuccessfulJsonResponse('La contraseña antigua no es la correcta.');
        }
    }

}