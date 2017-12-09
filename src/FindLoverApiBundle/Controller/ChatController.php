<?php

namespace FindLoverApiBundle\Controller;

use FindLoverBundle\Entity\Chat;
use FindLoverBundle\Entity\Lover;
use FindLoverBundle\Helper\ChatHelper;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChatController extends Controller
{
    /**
     * @param Request $request
     * @Route("/api/get-chat-data", name="get_chat_data")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getChatData(Request $request)
    {
        $participants = $request->get('participants');
        $offset = $request->get('offset');

        if(preg_match('/^[0-9]+-[0-9]+$/', $participants)) {
            $participants = explode('-', $participants);
            $chat = $this->getDoctrine()->getRepository(Chat::class)
                                        ->findOneBy(array(
                                                'participants' => array(
                                                    "$participants[0], $participants[1]",
                                                    "$participants[1], $participants[0]"
                                                )
                                            )
                                        );
            if(null !== $chat) {

                $lines = explode(PHP_EOL, $chat->readFromLine($offset));
                foreach ($lines as $line) {
                    if($line) {
                        $data['chatMessages'][] = new ChatHelper($line);
                    }
                }
                $currIdIndex = array_search($this->getUser()->getId(), $participants);
                $currIdIndex === 1 ? $guestIdIndex = 0 : $guestIdIndex = 1;
                $data['currentLover'] = $this->getDoctrine()->getRepository(Lover::class)->find($participants[$currIdIndex]);
                $data['guestLover'] = $this->getDoctrine()->getRepository(Lover::class)->find($participants[$guestIdIndex]);

                return new JsonResponse($this->get('jms_serializer')->serialize($data, 'json'), JsonResponse::HTTP_OK);
            }
        }

        return new JsonResponse(0, JsonResponse::HTTP_BAD_REQUEST);
    }
}
