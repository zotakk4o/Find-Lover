<?php

namespace FindLoverApiBundle\Controller;

use FindLoverBundle\Entity\Friendship;
use FindLoverBundle\Entity\Invitation;
use FindLoverBundle\Entity\Lover;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends Controller
{
	/**
	 * @param $request Request
	 * @Route("/api/send-invitation", name="invite_lover")
	 * @Method("POST")
	 * @return JsonResponse
	 */
    public function sendInvitationAction(Request $request)
    {
    	$receiver = $this->getDoctrine()->getRepository(Lover::class)->find($request->request->get('receiverId'));

    	if(null !== $receiver ) {
    		$invitation = new Invitation();
    		$invitation->setDateSent(new \DateTime());
    		$invitation->setReceiverId($receiver->getId());
    		$invitation->setSenderId($this->getUser()->getId());

    		$em = $this->getDoctrine()->getManager();
    		$em->persist($invitation);
    		$em->flush();

    		return new JsonResponse(1, Response::HTTP_OK);
	    }
        return new JsonResponse(0, Response::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/api/get-invitations", name="get_lover_invitations")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getLoverInvitations() {
        $lovers = [];
        $invitations = $this->getDoctrine()->getRepository(Invitation::class)
                                           ->findBy(array('receiverId' => $this->getUser()->getId()));
        foreach ($invitations as $invitation){
            /**@var $invitation Invitation*/
            $lovers[] = [
                'lover'    => $this->getDoctrine()->getRepository(Lover::class)->find($invitation->getSenderId()),
                'dateSent' => $invitation->getDateSent()
            ];
        }
        $serializer = $this->get('jms_serializer');

        return new JsonResponse( $serializer->serialize($lovers,'json'), Response::HTTP_OK);
    }

    /**
     * @param $request Request
     * @Route("/api/confirm-invitation", name="confirm_invitation")
     * @Method("POST")
     * @return JsonResponse
     */
    public function confirmInvitationAction(Request $request) {
        $senderId = $request->request->get('senderId');

        $invitation = $this->getDoctrine()->getRepository(Invitation::class)
                                          ->findOneBy(
                                              array(
                                                  'receiverId' => $this->getUser()->getId(),
                                                  'senderId'   => $senderId
                                              )
                                          );

        $friendship = new Friendship();
        $friendship->setParticipants("$senderId, {$this->getUser()->getId()}");
        $friendship->setDateAccomplished(new \DateTime());

        $em = $this->getDoctrine()->getManager();

        $em->remove($invitation);
        $em->persist($friendship);

        $em->flush();

        return new JsonResponse(1, Response::HTTP_OK);
    }
}
