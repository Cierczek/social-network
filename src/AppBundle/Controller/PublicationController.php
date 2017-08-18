<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * Description of PublicationController
 *
 * @author rcierczek
 */
class PublicationController extends Controller {
    //put your code here
    public function indexAction(Request $request){
        
       return $this->render('AppBundle:Publication:home.html.twig');
    }
}
