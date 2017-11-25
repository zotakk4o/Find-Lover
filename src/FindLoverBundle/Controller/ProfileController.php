<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 11/21/17
 * Time: 10:57 AM
 */

namespace FindLoverBundle\Controller;


use FindLoverBundle\Entity\Friendship;
use FindLoverBundle\Entity\Invitation;
use FindLoverBundle\Entity\Lover;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProfileController extends Controller {

	/**
	 * @Route("/profile/{id}", name="view_profile")
	 * @param $id int
	 * @return Response
	 */
	public function viewProfileAction($id) {
		$lover = $this->getDoctrine()->getRepository(Lover::class)->find($id);
		if( null !== $lover ) {
            $invitationSent = [];
            $invitationReceived = [];
            $friendship = null;
		    if( $this->getUser()->getId() !== $lover->getId() ) {
                $invitationSent = $this->getDoctrine()->getRepository(Invitation::class)
                                   ->findBy(
                                       array(
                                           'receiverId' => $lover->getId(),
                                           'senderId'   => $this->getUser()->getId()
                                       )
                                   );
                $invitationReceived = $this->getDoctrine()->getRepository(Invitation::class)
                                       ->findBy(
                                           array(
                                               'receiverId' => $this->getUser()->getId(),
                                               'senderId'   => $lover->getId()
                                           )
                                       );
                $friendship = $this->getDoctrine()->getRepository(Friendship::class)
                                                  ->findOneBy(
                                                      array(
                                                          'participants' => array(
                                                              "{$lover->getId()}, {$this->getUser()->getId()}",
                                                              "{$this->getUser()->getId()}, {$lover->getId()}"
                                                          ),
                                                      )
                                                  );
            }
			return $this->render('@FindLover/user/profile.html.twig',
                array(
                    'lover'     => $lover,
                    'isInvited' => ! empty($invitationSent),
                    'isSender'  => ! empty($invitationReceived),
                    'isAFriend' => null !== $friendship
                )
            );
		}
		return $this->redirectToRoute('home');
	}
}