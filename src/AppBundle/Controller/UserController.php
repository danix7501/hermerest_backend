<?php

namespace AppBundle\Controller;


use AppBundle\Entity\Administrator;
use AppBundle\Entity\Progenitor;
use AppBundle\Entity\Teacher;
use AppBundle\Entity\User;
use AppBundle\Services\Facades\AdministratorFacade;
use AppBundle\Services\Facades\CentreFacade;
use AppBundle\Services\Facades\ProgenitorFacade;
use AppBundle\Services\Facades\TeacherFacade;
use AppBundle\Services\Facades\UserFacade;
use AppBundle\Services\JwtAuth;
use AppBundle\Services\ResponseFactory;
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

    public function __construct(JwtAuth $jwtAuth,
                                ResponseFactory $responseFactory,
                                UserFacade $userFacade,
                                ProgenitorFacade $progenitorFacade,
                                TeacherFacade $teacherFacade,
                                CentreFacade $centreFacade,
                                AdministratorFacade $administratorFacade)
    {
        $this->jwtAuth = $jwtAuth;
        $this->responseFactory = $responseFactory;
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
            $user = $this->userFacade->findByEmailAndPlainPassword(
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
                    $this->jwtAuth->encodeUser($user)
                );
            }
        } else {

            $findUser = $this->userFacade->findByUsername($request->request->get('username'));
            if ($findUser == null) {
                $userOther = new User();
                $userOther->setUsername($request->request->get('username'));
                $userOther->setPassword(hash('sha256', $request->request->get('password')));
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
                        return $this->responseFactory->successfulJsonResponse('Profesor creado correctamente');
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

}
