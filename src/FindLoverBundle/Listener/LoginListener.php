<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 11/20/17
 * Time: 12:12 PM
 */

namespace FindLoverBundle\Listener;


use Doctrine\ORM\EntityManager;
use FindLoverBundle\Entity\Lover;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;

class LoginListener implements AuthenticationSuccessHandlerInterface {
	private $entityManager;

	public function __construct(EntityManager $em) {
		$this->setEntityManager($em);
	}

	public function onAuthenticationSuccess( Request $request, TokenInterface $token ) {
		/** @var $lover Lover */
		$lover = $token->getUser();
		$lover->setLastOnline(null);

		$em = $this->getEntityManager();
		$em->persist($lover);
		$em->flush();

		return new RedirectResponse('/');
	}

	/**
	 * Get entityManager
	 *
	 * @return EntityManager
	 */
	public function getEntityManager() {
		return $this->entityManager;
	}

	/**
	 * Set entityManger
	 *
	 * @param $entityManager EntityManager
	 */
	public function setEntityManager($entityManager) {
		$this->entityManager = $entityManager;
	}


}