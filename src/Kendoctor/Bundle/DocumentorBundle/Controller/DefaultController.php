<?php

namespace Kendoctor\Bundle\DocumentorBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;


class DefaultController extends Controller {

    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction(Request $request) {
    
        
        return array(
           
        );
    }
    
    /**
     * @Route("/aboutus", name="aboutus")
     * @Template()
     */
    public function aboutUsAction()
    {
        return array(
           
        );
    }

    public function createLocaleForm() {
        return $this->createFormBuilder()
                        ->add('locale', 'choice', array('choices' => array('en' => 'English', 'zh' => 'Chinese')))
                        ->getForm();
    }

}
