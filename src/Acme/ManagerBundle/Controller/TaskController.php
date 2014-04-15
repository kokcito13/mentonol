<?php

namespace Acme\ManagerBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

class TaskController extends Controller
{
    /**
     * @Route("/tasks/", name="manager_task")
     * @Method("GET")
     * @Template()
     */
    public function indexAction()
    {

        return array('name' => 123);
    }
}
