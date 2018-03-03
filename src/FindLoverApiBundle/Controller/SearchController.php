<?php

namespace FindLoverApiBundle\Controller;

use Doctrine\ORM\Query\ResultSetMapping;
use FindLoverBundle\Entity\Lover;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class SearchController extends Controller
{
	/**
	 * @Route("/api/search", name="search_route")
	 * @Method("POST")
	 * @return JsonResponse
	 */
    public function searchAction(Request $request)
    {       
	    $term = $request->get('term');
	    $offset = $request->get('offset');

	    $em = $this->get('doctrine.orm.default_entity_manager');
	    $rsm = new ResultSetMapping();

	    $rsm->addEntityResult(Lover::class, 'l');
	    $rsm->addFieldResult('l','first_name','firstName');
	    $rsm->addFieldResult('l','last_name','lastName');
	    $rsm->addFieldResult('l','nickname','nickname');
	    $rsm->addFieldResult('l', 'id', 'id');
	    $rsm->addFieldResult('l','profile_picture', 'profilePicture');

	    $result = $em->createNativeQuery("
			SELECT * FROM lover AS l 
			WHERE( l.first_name LIKE :search 
				OR l.last_name LIKE :search  
				OR l.nickname LIKE :search ) 
			AND l.id != :id
			ORDER BY CASE
				WHEN l.first_name LIKE :search THEN 1
				WHEN l.last_name LIKE :search THEN 2
				WHEN l.nickname LIKE :search THEN 3
			END
			LIMIT 6
			OFFSET :offset
		", $rsm)
	        ->setParameters(
	    	    array(
	    		    'search' => "$term%",
			        'id'     => $this->getUser()->getId(),
			        'offset' => intval($offset)
		        )
	        )
	        ->getArrayResult();

	    $serializer = $this->get('jms_serializer');

	    return new JsonResponse($serializer->serialize($result, 'json'), JsonResponse::HTTP_OK);
    }

    /**
     * @param $request Request
     * @Route("/api/get-recent-searches", name="get_recent_searches")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getRecentSearchesAction(Request $request)
    {
        $offset = $request->get('offset');
        $lovers = $this->getDoctrine()->getRepository(Lover::class)
                       ->extractRecentSearches(
                           array(
                               'ids'    => $this->getUser()->getRecentSearchesIds(),
                               'offset' => $offset
                           )
                       );
        if(! empty($lovers)) {
            $serializer = $this->get('jms_serializer');
            $lovers[] = count($this->getUser()->getRecentSearchesIds());
            return new JsonResponse($serializer->serialize($lovers, 'json'), JsonResponse::HTTP_OK);
        }
        return new JsonResponse(0, JsonResponse::HTTP_OK);
    }

    /**
     * @param $request Request
     * @Route("/api/add-recent-search", name="add_recent_search")
     * @Method("POST")
     * @return JsonResponse
     */
    public function addRecentSearchAction(Request $request)
    {
        /** @var Lover $user */
        $user = $this->getUser();
        $searches = $user->getRecentSearchesIds();
        $searchedId = $request->request->get('searchedId');
        if( null !== $this->getDoctrine()->getRepository(Lover::class)->find($searchedId) ) {
            if(count($searches) === 36) {
                array_pop($searches);
                $user->setRecentSearches(implode(', ', $searches))->addRecentSearch($searchedId);
            } else {
                $user->addRecentSearch($searchedId);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse(1, JsonResponse::HTTP_OK);
        }
        return new JsonResponse(0, JsonResponse::HTTP_BAD_REQUEST);
    }
}
