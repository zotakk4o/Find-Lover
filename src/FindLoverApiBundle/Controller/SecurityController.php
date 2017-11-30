<?php

namespace FindLoverApiBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class SecurityController extends Controller
{

    /**
     * @Route("/api/login-security-action", name="login_api_action")
     * @Method("POST")
     * @return JsonResponse
     */
    public function loginAction()
    {
        $user = $this->getUser();
        if(! $user->isOnline()) {
            $user->setLastOnline(null);

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return new JsonResponse(1, JsonResponse::HTTP_OK);
    }

    /**
     * @Route("/api/logout-security-action", name="logout_api_action")
     * @Method("POST")
     * @return JsonResponse
     */
    public function logoutAction()
    {
        $user = $this->getUser();
        if($user->isOnline()) {
            $user->setLastOnline(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();
        }

        return new JsonResponse(1, JsonResponse::HTTP_OK);
    }
}
