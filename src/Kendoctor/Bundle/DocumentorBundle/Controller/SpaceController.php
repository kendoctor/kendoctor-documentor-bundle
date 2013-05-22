<?php

namespace Kendoctor\Bundle\DocumentorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class SpaceController extends Controller
{
    /**
     * @Route("/space")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }
    
    
    /**
     * @Route("/space-sidebar", name="space_sidebar")
     * @Template()
     */
    public function sidebarAction()
    {
        return array();
    }
}
