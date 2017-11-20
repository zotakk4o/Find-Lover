<?php

namespace FindLoverApiBundle\Controller;

use FindLoverBundle\Entity\Lover;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends Controller
{
	/**
	 * @Route("/api/search", name="search_route")
	 * @return Response
	 */
    public function indexAction(Request $request)
    {
    	$term = $request->get('term');
    	$offset = $request->get('offset');

    	$repository = $this->getDoctrine()->getRepository(Lover::class);
    	$result = $repository->createQueryBuilder('l')
	               ->where("l.firstName LIKE :search ")
	               ->orWhere("l.lastName LIKE :search ")
	               ->orWhere("l.nickname LIKE :search ")
	               ->setParameter('search', "%$term%")
	               ->setFirstResult($offset)
	               ->setMaxResults(6)
	               ->orderBy('l.firstName', 'ASC')
	               ->getQuery()
	               ->getArrayResult();

        return new JsonResponse($result, 200);
    }
}
