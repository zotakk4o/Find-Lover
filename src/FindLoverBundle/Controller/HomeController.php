<?php

namespace FindLoverBundle\Controller;

use FindLoverBundle\Entity\Lover;
use FindLoverBundle\Form\RegisterForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class HomeController extends Controller
{
    /**
     * @Route("/", name="login_home")
     * @param $request Request
     * @param $authUtils AuthenticationUtils
     * @return Response
     */
    public function indexAction(Request $request)
    {
    	//--- Login part logic ---
	    $authUtils = $this->get('security.authentication_utils');

	    // get the login error if there is one
	    $error = $authUtils->getLastAuthenticationError();

	    // last username entered by the user
	    $lastUsername = $authUtils->getLastUsername();
	    //--- Login part logic ---

	    //--- Register part logic ---
    	$lover = new Lover();

    	$registerForm = $this->createForm(RegisterForm::class, $lover);

    	$registerForm->handleRequest($request);

    	if($registerForm->isSubmitted() && $registerForm->isValid()) {


	    }

	    //--- Register part logic ---

	    return $this->render('@FindLover/home/index.html.twig', array(
	    	'form'       => $registerForm->createView(),
		    'last_email' => $lastUsername,
		    'error'      => $error,
	    ));
    }

}
