<?php

namespace FindLoverApiBundle\Controller;

use FindLoverBundle\Entity\Chat;
use FindLoverBundle\Entity\Friendship;
use FindLoverBundle\Entity\Invitation;
use FindLoverBundle\Entity\Lover;
use FindLoverBundle\Repository\ChatRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

        $participants = array(
            'participants' => array(
                "{$receiver->getId()}, {$this->getUser()->getId()}",
                "{$this->getUser()->getId()}, {$receiver->getId()}"
            ),
        );

        $invitation = $this->getDoctrine()->getRepository(Invitation::class)->findOneBy($participants);

    	if(null !== $receiver) {
    	    if($invitation == null)
            {
                $invitation = new Invitation();
                $invitation->setDateSent(new \DateTime());
                $invitation->setParticipants("{$this->getUser()->getId()}, {$receiver->getId()}");

                $em = $this->getDoctrine()->getManager();
                $em->persist($invitation);
                $em->flush();
            }
    		return new JsonResponse(1, JsonResponse::HTTP_OK);
	    }
        return new JsonResponse(0, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/api/get-invitations", name="get_lover_invitations")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getLoverInvitationsAction()
    {
        $lovers = [];
        $invitations = $this->getDoctrine()->getRepository(Invitation::class)->findInvitationsReceived($this->getUser()->getId());
        foreach ($invitations as $invitation){
            /**@var $invitation Invitation*/
            $senderId = $invitation->getparticipantsIds()[0];
            $lovers[] = [
                'lover'    => $this->getDoctrine()->getRepository(Lover::class)->find($senderId),
                'dateSent' => $invitation->getDateSent()
            ];
        }
        $serializer = $this->get('jms_serializer');

        return new JsonResponse( $serializer->serialize($lovers,'json'), JsonResponse::HTTP_OK);
    }

    /**
     * @param $request Request
     * @Route("/api/confirm-invitation", name="confirm_invitation")
     * @Method("POST")
     * @return JsonResponse
     */
    public function confirmInvitationAction(Request $request)
    {
        $senderId = $request->request->get('senderId');
        $sender = $this->getDoctrine()->getRepository(Lover::class)->find($senderId);

        if(null !== $sender) {
            $participants = array(
                'participants' => array(
                    "$senderId, {$this->getUser()->getId()}",
                    "{$this->getUser()->getId()}, $senderId"
                ),
            );
            $invitation = $this->getDoctrine()->getRepository(Invitation::class)->findOneBy($participants);
            $friendship = $this->getDoctrine()->getRepository('FindLoverBundle:Friendship')->findOneBy($participants);

            if($friendship === null){
                $friendship = new Friendship();
                $friendship->setParticipants("$senderId, {$this->getUser()->getId()}");
                $friendship->setDateAccomplished(new \DateTime());

                $chat = $this->getDoctrine()->getRepository(Chat::class)
                    ->findOneBy(
                        array(
                            'participants' => array(
                                "$senderId, {$this->getUser()->getId()}",
                                "{$this->getUser()->getId()}"
                            )
                        )
                    );

                if($chat == null) {
                    $chat = new Chat();
                    $chatPath = "{$this->get('kernel')->getRootDir()}/../src/FindLoverBundle/Resources/chats/chat-$senderId-{$this->getUser()->getId()}.txt";
                    $chat->setParticipants($friendship->getParticipants());
                    $chat->setChatFilePath($chatPath);
                    fclose(fopen($chatPath, 'w'));
                }

                $sender->addFriend($this->getUser()->getId());
                $this->getUser()->addFriend($senderId);

                $em = $this->getDoctrine()->getManager();

                $em->persist($friendship);
                $em->persist($chat);
                $em->persist($sender);
                $em->persist($this->getUser());
            }

            $em->remove($invitation);
            $em->flush();

            return new JsonResponse(1, JsonResponse::HTTP_OK);
        }
        return new JsonResponse(0, JsonResponse::HTTP_BAD_REQUEST);
    }

    /**
     * @Route("/api/get-recently-available-lovers", name="get_recently_available_lovers")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getRecentlyAvailableLoversAction()
    {
        $lovers = $this->getDoctrine()->getRepository(Lover::class)->findRecentlyAvailable($this->getUser()->getFriendsIds());
        if(! empty($lovers)) {
            $serializer = $this->get('jms_serializer');
            return new JsonResponse($serializer->serialize($lovers, 'json'), JsonResponse::HTTP_OK);
        }
        return new JsonResponse(0, JsonResponse::HTTP_OK);
    }

    /**
     * @param $request Request
     * @Route("/api/remove-lover-friend", name="remove_lover_friend")
     * @Method("POST")
     * @return JsonResponse
     */
    public function removeLoverFriendAction(Request $request)
    {
        $targetId = $request->request->get('targetId');
        $target = $this->getDoctrine()->getRepository(Lover::class)->find($targetId);
        $currUser = $this->getUser();

        if(null !== $target) {
            $targetFriends = $target->getFriendsIds();
            unset($targetFriends[array_search($currUser->getId(), $targetFriends)]);
            $target->setFriends(implode(', ', $targetFriends));

            $currentUserFriends = $currUser->getFriendsIds();
            unset($currentUserFriends[array_search($targetId, $currentUserFriends)]);
            $currUser->setFriends(implode(', ', $currentUserFriends));

            $friendship = $this->getDoctrine()
                               ->getRepository(Friendship::class)
                               ->findOneBy(array('participants' => array("{$targetId}, {$currUser->getId()}", "{$currUser->getId()}, {$targetId}")));

            $em = $this->getDoctrine()->getManager();
            $em->persist($currUser);
            $em->persist($target);
            $em->remove($friendship);
            $em->flush();

            return new JsonResponse(1, JsonResponse::HTTP_OK);
        }
        return new JsonResponse(0, JsonResponse::HTTP_BAD_REQUEST);
    }
}
