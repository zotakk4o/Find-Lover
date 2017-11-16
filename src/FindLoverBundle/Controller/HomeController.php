<?php

namespace FindLoverBundle\Controller;

use FindLoverBundle\Entity\Lover;
use FindLoverBundle\Form\RegisterForm;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    /**
     * @Route("/login", name="login")
     * @return Response
     */
    public function indexAction()
    {
    	//--- Login part logic ---
	    $authUtils = $this->get('security.authentication_utils');

	    // last username entered by the user
	    $lastEmail = $authUtils->getLastUsername();
	    //--- Login part logic ---

	    echo "<pre>";
	    var_dump($authUtils->getLastAuthenticationError()->getMessage());
	    echo "</pre>";exit;

	    $this->forward('FindLoverBundle:Home:register', array('lastEmail' => $lastEmail));

	    return $this->redirectToRoute('register', array('lastEmail' => $lastEmail));
    }

	/**
	 * @param Request $request
	 * @Route("/")
	 * @Route("/last-email/{lastEmail}", name="register")
	 * @return Response
	 */
    public function registerAction(Request $request, string $lastEmail = '', string $error = '') {
    	$lover = new Lover();
    	if( $error !== '' ) var_dump($error);

    	$registerForm = $this->createForm(RegisterForm::class, $lover);

    	$registerForm->handleRequest($request);

    	if($registerForm->isValid() && $registerForm->isSubmitted()) {
    		$lover->setDateRegistered(new \DateTime());
		    $password = $this->get('security.password_encoder')->encodePassword($lover, $lover->getPassword());
		    $lover->setPassword($password);


		    $em = $this->getDoctrine()->getManager();
    		$em->persist($lover);
    		$em->flush();
	    }

	    return $this->render('@FindLover/home/index.html.twig', array(
		    'form' => $registerForm->createView(),
		    'last_email' => $lastEmail
	    ));
    }

}
