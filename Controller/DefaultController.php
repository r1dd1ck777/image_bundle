<?php

namespace Rid\Bundle\ImageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('RidImageBundle:Default:index.html.twig', array('name' => $name));
    }
}
