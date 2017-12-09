<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 11/26/17
 * Time: 10:55 AM
 */

namespace FindLoverBundle\Service;


use Doctrine\ORM\EntityManager;
use FindLoverBundle\Helper\ChatHelper;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use JMS\Serializer\Serializer;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FindLoverTopic implements TopicInterface
{

    private $tokenStorage;

    private $entityManager;

    private $jmsSerializer;

    public function __construct(TokenStorageInterface $tokenStorage, EntityManager $entityManager, Serializer $serializer)
    {
        $this->setTokenStorage($tokenStorage);
        $this->setEntityManager($entityManager);
        $this->setJmsSerializer($serializer);
    }

    /**
     * This will receive any Subscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
        $topic->broadcast('Nice to connect with you');
    }

    /**
     * This will receive any UnSubscription requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @return void
     */
    public function onUnSubscribe(ConnectionInterface $connection, Topic $topic, WampRequest $request)
    {
    }


    /**
     * This will receive any Publish requests for this topic.
     *
     * @param ConnectionInterface $connection
     * @param Topic $topic
     * @param WampRequest $request
     * @param $event
     * @param array $exclude
     * @param array $eligible
     * @return mixed|void
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $currUser = $this->getTokenStorage()->getToken()->getUser();
        $participants = $request->getAttributes()->get('participants');
        $regExp = preg_match('/^[0-9]+-[0-9]+$/', $participants);

        if(is_string($participants) && $regExp && in_array($currUser->getId(), explode('-', $participants))) {
            $otherParticipant = str_replace($currUser->getId(), '', str_replace('-', '', $participants));
            $chat = $this->getEntityManager()->getRepository('FindLoverBundle:Chat')
                                             ->findOneBy(array(
                                                 'participants' => array(
                                                     "{$currUser->getId()}, $otherParticipant",
                                                     "$otherParticipant, {$currUser->getId()}"
                                                 )
                                             ));
            if(null !== $chat) {
                $dateWritten = new \DateTime();
                $chat->writeDownMessage("$event|=>id={$currUser->getId()}|=>date={$dateWritten->format('Y-m-d H:i:s')}");
                $this->getEntityManager()->persist($chat);
                $this->getEntityManager()->flush();

                $chatObject = new ChatHelper($event);
                $topic->broadcast($this->getJmsSerializer()->serialize($chatObject, 'json'));
                return;
            }
        }

        $topic->broadcast(false);
    }

    /**
     * Like RPC is will use to prefix the channel
     * @return string
     */
    public function getName()
    {
        return 'find_lover.topic';
    }

    /**
     * @return TokenStorageInterface
     */
    public function getTokenStorage()
    {
        return $this->tokenStorage;
    }

    /**
     * @param TokenStorageInterface $tokenStorage
     */
    public function setTokenStorage($tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }

    /**
     * @param EntityManager $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @return Serializer
     */
    public function getJmsSerializer()
    {
        return $this->jmsSerializer;
    }

    /**
     * @param Serializer $jmsSerializer
     */
    public function setJmsSerializer($jmsSerializer)
    {
        $this->jmsSerializer = $jmsSerializer;
    }


}