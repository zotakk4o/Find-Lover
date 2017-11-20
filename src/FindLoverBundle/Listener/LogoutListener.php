<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 11/20/17
 * Time: 11:54 AM
 */

namespace FindLoverBundle\Listener;


use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Logout\LogoutHandlerInterface;

class LogoutListener implements LogoutHandlerInterface {

	private $entityManager;

	public function __construct(EntityManager $em) {
		$this->setEntityManager($em);
	}

	public function logout( Request $request, Response $response, TokenInterface $token ) {

		$lover = $token->getUser();

		$lover->setLastOnline(new \DateTime('now'));

		$em = $this->getEntityManager();
		$em->persist($lover);
		$em->flush();

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
	 * Set entityManager
	 *
	 * @param $em EntityManager
	 */
	public function setEntityManager($em) {
		$this->entityManager = $em;
	}
}