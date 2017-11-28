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
            $invitation = null;
            $participants = null;
            $friendship = null;
		    if($this->getUser()->getId() !== $lover->getId()) {
                $invitation = $this->getDoctrine()->getRepository(Invitation::class)
                                                 ->findOneBy(
                                                     array(
                                                          'participants' => array(
                                                              "{$lover->getId()}, {$this->getUser()->getId()}",
                                                              "{$this->getUser()->getId()}, {$lover->getId()}"
                                                        ),
                                                     )
                                                 );
                if($invitation !== null) {
                    $participants = $invitation->getparticipantsIds();
                }

                $friendship = $this->getDoctrine()->getRepository(Friendship::class)
                                                  ->findOneBy(
                                                      array(
                                                          'participants' => array(
                                                              "{$lover->getId()}, {$this->getUser()->getId()}",
                                                              "{$this->getUser()->getId()}, {$lover->getId()}"
                                                          )
                                                      )
                                                  );
            }
			return $this->render('@FindLover/user/profile.html.twig',
                array(
                    'lover'     => $lover,
                    'isInvited' => null !== $participants ? $participants[0] == $this->getUser()->getId() : null,
                    'isSender'  => null !== $participants ? $participants[0] == $lover->getId() : null,
                    'isAFriend' => null !== $friendship
                )
            );
		}
		return $this->redirectToRoute('home');
	}
}