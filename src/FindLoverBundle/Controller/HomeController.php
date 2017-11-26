<?php

namespace FindLoverBundle\Controller;

use FindLoverBundle\Entity\Friendship;
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
	    $authUtils = $this->get('security.authentication_utils');

	    // last username entered by the user
	    $authUtils->getLastUsername() === null ? $lastEmail = '' : $lastEmail = $authUtils->getLastUsername();

	    $this->forward('FindLoverBundle:Home:register', array('lastEmail' => $lastEmail));

	    return $this->redirectToRoute('register', array('lastEmail' => $lastEmail));
    }

	/**
	 * @param Request $request
	 * @param $lastEmail string
	 * @Route("/", name="home")
	 * @Route("/last-email/{lastEmail}", name="register")
	 * @return Response
	 */
    public function registerAction(Request $request, string $lastEmail = '') {
    	$lover = new Lover();

    	$registerForm = $this->createForm(RegisterForm::class, $lover);
    	$registerForm->handleRequest($request);

    	if($registerForm->isSubmitted() && $registerForm->isValid()) {
		    $em = $this->getDoctrine()->getManager();

		    $lover->setProfilePicture('/bundles/findlover/images/default_profile_pic.jpg');
		    $lover->setDateRegistered(new \DateTime());
		    $lover->addRole($em->getRepository('FindLoverBundle:Role')->findOneBy(array('name' => 'ROLE_LOVER')));
		    $lover->setPassword($this->get('security.password_encoder')->encodePassword($lover, $lover->getPassword()));

    		$em->persist($lover);
    		$em->flush();
	    }

	    $available = $this->getDoctrine()->getRepository(Friendship::class)->findRecentlyAvailable($this->getUser()->getId());


	    return $this->render('@FindLover/home/index.html.twig', array(
		    'form' => $registerForm->createView(),
		    'last_email' => $lastEmail
	    ));
    }
}
