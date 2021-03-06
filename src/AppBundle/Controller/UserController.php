<?php

namespace AppBundle\Controller;


use AppBundle\Entity\AccountToken;
use AppBundle\Entity\Administrator;
use AppBundle\Entity\Progenitor;
use AppBundle\Entity\Teacher;
use AppBundle\Entity\User;
use AppBundle\Normalizers\CourseNormalizer;
use AppBundle\Services\Facades\AdministratorFacade;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\TeacherFacade;
use AppBundle\Services\Facades\UserFacade;
use AppBundle\Services\JwtAuth;
use AppBundle\Services\Mailer;
use AppBundle\Services\ResponseFactory;
use AppBundle\Services\Utils;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;


class UserController extends Controller
{
    private $jwtAuth;
    private $userFacade;
    private $progenitorFacade;
    private $teacherFacade;
    private $administratorFacade;
    private $centreFacade;
    private $responseFactory;
    private $utils;
    private $mailer;


    public function __construct(JwtAuth $jwtAuth,
                                ResponseFactory $responseFactory,
                                Utils $utils,
                                Mailer $mailer,
                                UserFacade $userFacade,
                                ProgenitorFacade $progenitorFacade,
                                TeacherFacade $teacherFacade,
                                CentreFacade $centreFacade,
                                AdministratorFacade $administratorFacade)
    {
        $this->jwtAuth = $jwtAuth;
        $this->responseFactory = $responseFactory;
        $this->utils = $utils;
        $this->mailer = $mailer;
        $this->userFacade = $userFacade;
        $this->progenitorFacade = $progenitorFacade;
        $this->teacherFacade = $teacherFacade;
        $this->centreFacade = $centreFacade;
        $this->administratorFacade = $administratorFacade;

    }

    /**
     * @Route("/login", name="login")
     * @Method("POST")
     */
    public function loginAction(Request $request)
    {

        if ($request->get('code') != null) {
            $user = $this->userFacade->findByUsername($request->request->get('telephone'));
            if ($request->request->get('code') == '123456') {
                if ($user == null) return $this->responseFactory->unsuccessfulJsonResponse('No hay ningún padre registrado con este telephono');
                return $this->responseFactory->successfulJsonResponse(
                    $this->jwtAuth->encodeUser($user)
                );
            } else {
                return $this->responseFactory->unsuccessfulJsonResponse('El código es incorrecto');
            }

        } else {
            $user = $this->userFacade->findByUsernameAndPlainPassword(
                $request->get('username'),
                $request->get('password')
            );

            if ($user == null) return $this->responseFactory->unsuccessfulJsonResponse('No se ha encontrado un usuario con las credenciales introducidas');

            return $this->responseFactory->successfulJsonResponse(
                $this->jwtAuth->encodeUser($user)
            );
        }
    }

    /**
     * @Route("/register", name="register")
     * @Method("POST")
     */
    public function registerAction(Request $request)
    {
        if ($request->get('telephone') != null) {
            $parent = $this->progenitorFacade->findByTelephone($request->request->get('telephone'));
            if ($parent != null){
                return $this->responseFactory->unsuccessfulJsonResponse('No se puede registrar porque el teléfono ya esta dado de alta en el sistema');
            } else {

              $userParent = new User();
              $userParent->setUsername($request->request->get('telephone'));
              $userParent->setPassword('toporomock');
              $userParent->setRol('progenitor');
              $this->userFacade->create($userParent);

              $user = $this->userFacade->findByUsername($request->request->get('telephone'));

              $registerParent = new Progenitor();
              $registerParent->setName($request->request->get('name'));
              $registerParent->setTelephone($request->request->get('telephone'));
              $registerParent->setUser($user);
              $this->progenitorFacade->create($registerParent);

                return $this->responseFactory->successfulJsonResponse(
                    $this->jwtAuth->encodeUserParent($user)
                );
            }
        } else {

            $findUser = $this->userFacade->findByUsername($request->request->get('username'));
            $randomPassword = $this->utils->generateRandomPassword();
            if ($findUser == null) {
                $userOther = new User();
                $userOther->setUsername($request->request->get('username'));
                $userOther->setPassword(hash('sha256',$randomPassword));
                $userOther->setRol($request->request->get('rol'));
                $this->userFacade->create($userOther);

                $findUser = $this->userFacade->findByUsername($request->request->get('username'));
                $centre = $this->centreFacade->find($request->request->get('centre'));

                switch ($request->request->get('rol')){
                    case 'teacher':
                        $teacher = new Teacher();
                        $teacher->setName($request->request->get('name'));
                        $teacher->setCentre($centre);
                        $teacher->setUser($findUser);
                        $this->teacherFacade->create($teacher);
                        $this->notifyNewAccount($teacher->getUser(), $randomPassword);
                        return $this->responseFactory->successfulJsonResponse(
                            ['teacher' =>
                                [
                                    'id' => $teacher->getId(),
                                    'name' => $teacher->getName(),
                                    'username' => $teacher->getUser()->getUsername(),
                                    'course' => null
                                ]
                        ]);
                    case 'administrator':
                        $teacher = new Administrator();
                        $teacher->setName($request->request->get('name'));
                        $teacher->setCentre($centre);
                        $teacher->setUser($findUser);
                        $this->teacherFacade->create($teacher);
                        return $this->responseFactory->successfulJsonResponse('Administrador creado correctamente');
                }

            } else {
                return $this->responseFactory->unsuccessfulJsonResponse('No se puede registrar porque el nombre de usuario ya esta dado de alta en el sistema');
            }
        }
    }

    /**
     * @Route("/changePassword/{id}", name="changePassword")
     * @Method("PUT")
     */
    public function changePasswordAction(Request $request, $id)
    {
        $user = $this->userFacade->find($id);
        if ($user == null) return $this->responseFactory->unsuccessfulJsonResponse('El usuario no existe');

        $user->setPassword(hash('sha256', $request->request->get('newPassword')));
        $this->userFacade->edit();
        return $this->responseFactory->successfulJsonResponse('La contraseña ha sido cambiada correctamente');
    }

    private function notifyNewAccount(User $user, $password)
    {
        $this->mailer->sendMail(
            'HERMEREST: Cuenta creada',
            Mailer::NOTIFY_ACCOUNT_CREATED,
            [
                'link' => 'http://localhost:4200/login',
                'password' => $password
            ],
            $user->getUsername()
        );
    }
}