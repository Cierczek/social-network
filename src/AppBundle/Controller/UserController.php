<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use BackendBundle\Entity\User;
use AppBundle\Form\RegisterType;
use AppBundle\Form\UserType;

/**
 * Description of UserController
 *
 * @author rcierczek
 */
class UserController extends Controller {

    private $session;

    public function __construct() {
        $this->session = new Session();
    }

    public function loginAction(Request $request) {

        if (is_object($this->getUser())) {
            return $this->redirect("home");
        }

        $authenticationUtils = $this->get('security.authentication_utils');
        $errors = $authenticationUtils->getLastAuthenticationError();
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('AppBundle:User:login.html.twig', [
                    "last_username" => $lastUsername,
                    "errors" => $errors
        ]);
    }

    public function registerAction(Request $request) {

        if (is_object($this->getUser())) {
            return $this->redirect("home");
        }
        
        $user = new User();
        $form = $this->createForm(RegisterType::class, $user);
        
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            if ($form->isValid()) {

                $em = $this->getDoctrine()->getManager();

                $query = $em->createQuery("SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick")
                        ->setParameter("email", $form->get("email")->getData())
                        ->setParameter("nick", $form->get("nick")->getData());
                $user_isset = $query->getResult();
                
                if (count($user_isset) == 0) {
                    $factory = $this->get("security.encoder_factory");
                    $encoder = $factory->getEncoder($user);

                    $password = $encoder->encodePassword($form->get("password")->getData(), $user->getSalt());

                    $user->setPassword($password);
                    $user->setRole("ROLE_USER");
                    $user->setImage("null");

                    $em->persist($user);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Te has registrado correctamente";
                        $this->session->getFlashBag()->add("success", $status);

                        return $this->redirect("login");
                    } else {
                        $status = "No te has registrado correctamente";
                    }
                } else {
                    $status = "El usuario ya existe";
                }
            } else {
                $status = "No te has registrado correctamente";
            }

            $this->session->getFlashBag()->add("danger", $status);
        }
        return $this->render('AppBundle:User:register.html.twig', [
                "form" => $form->createView()
        ]);
    }

    public function nickTestAction(Request $request) {
        $nick = $request->get("nick");

        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:User");

        $user_isset = $user_repo->findOneBy((["nick" => $nick]));

        if (count($user_isset) >= 1 && is_object($user_isset)) {
            $result = "used";
        } else {
            $result = "unused";
        }

        return new Response($result);
    }

    public function emailTestAction(Request $request) {
        $email = $request->get("email");

        $em = $this->getDoctrine()->getManager();
        $user_repo = $em->getRepository("BackendBundle:User");

        $email_isset = $user_repo->findOneBy((["email" => $email]));

        if (count($email_isset) >= 1 && is_object($email_isset)) {
            $result = "used";
        } else {
            $result = "unused";
        }

        return new Response($result);
    }

    public function editUserAction(Request $request) {

        $user = $this->getUser();
        $user_image = $user->getImage();
        $form = $this->createForm(UserType::class, $user);

        $form->handleRequest($request);
   
        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();

                $query = $em->createQuery("SELECT u FROM BackendBundle:User u WHERE u.email = :email OR u.nick = :nick")
                        ->setParameter("email", $form->get("email")->getData())
                        ->setParameter("nick", $form->get("nick")->getData());
                $user_isset = $query->getResult();
                              
                if (count($user_isset) == 1) {

                    $file = $form["image"]->getData();

                    if ($file != null && !empty($file)) {
                        $ext = $file->guessExtension();
                        if ($ext == "jpg" || $ext == "jpeg" || $ext == "png") {
                            $file_name = $user->getId().'.'.time().'.'.$ext;
                            $destination = "uploads/users/".$user->getEmail().$user->getId();
                            if (!file_exists($destination)) {
                                mkdir($destination, 0777, true);
                            }
                            $file->move($destination,$file_name);
                            $user->setImage($file_name);
                        }
                    } else {
                        $user->setImage($user_image);
                    }

                    $em->persist($user);
                    $flush = $em->flush();

                    if ($flush == null) {
                        $status = "Has actualizado tus datos correctamente";
                        $this->session->getFlashBag()->add("success", $status);
                        return $this->redirect("my-data");
                    } else {
                        $status = "No se han podido modificar los datos correctamente";
                    }
                } else {
                    $status = "No puedes utlizar este usuario porque ya existe";
                }
            } else {
                $status = "No se han modificado los datos correctamente";
            }

            $this->session->getFlashBag()->add("danger", $status);
            return $this->redirect("my-data");
        }
        return $this->render('AppBundle:User:edit_user.html.twig', [
                    "form" => $form->createView()
        ]);
    }
    
    
    public function usersAction(Request $request){
        
       $em = $this->getDoctrine()->getManager();
    
       $users = $em->getRepository('BackendBundle:User')->findAll(); 
       
       $paginator = $this->get("knp_paginator");
       $pagination = $paginator->paginate($users, $request->query->getInt("page", 1), 5);
        
        return $this->render('AppBundle:User:users.html.twig', [
                    "pagination" => $pagination
        ]);
    }
    
    public function searchAction(Request $request){
        
       $em = $this->getDoctrine()->getManager();
       
       $search = $request->query->get("search", null);
       
       if($search == null){
           return $this->redirect($this->generateUrl("home_publications"));
       }
       
       $dql = "Select u From BackendBundle:User u Where u.name Like :search "
               . "OR u.surname Like :search OR u.nick Like :search";
       
       $query = $em->createQuery($dql)->setParameter("search", "%$search%");
      
       $paginator = $this->get("knp_paginator");
       $pagination = $paginator->paginate($query, $request->query->getInt("page", 1), 5);
        
        return $this->render('AppBundle:User:users.html.twig', [
                    "pagination" => $pagination
        ]);
    }

}