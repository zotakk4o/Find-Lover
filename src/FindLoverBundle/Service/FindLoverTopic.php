<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 11/26/17
 * Time: 10:55 AM
 */

namespace FindLoverBundle\Service;


use Doctrine\ORM\EntityManager;
use FindLoverBundle\Entity\Chat;
use FindLoverBundle\Entity\Lover;
use FindLoverBundle\Helper\ChatHelper;
use Gos\Bundle\WebSocketBundle\Client\ClientManipulatorInterface;
use Gos\Bundle\WebSocketBundle\Router\WampRequest;
use Gos\Bundle\WebSocketBundle\Topic\TopicInterface;
use JMS\Serializer\Serializer;
use Ratchet\ConnectionInterface;
use Ratchet\Wamp\Topic;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class FindLoverTopic implements TopicInterface
{
    private $rootDir;

    private $entityManager;

    private $jmsSerializer;

    private $clientManipulator;

    public function __construct(ClientManipulatorInterface $clientManipulator, string $rootDir, EntityManager $entityManager, Serializer $serializer)
    {
        $this->setRootDir($rootDir);
        $this->setEntityManager($entityManager);
        $this->setJmsSerializer($serializer);
        $this->setClientManipulator($clientManipulator);
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
        $topic->broadcast(true);
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
     * @throws \Exception
     */
    public function onPublish(ConnectionInterface $connection, Topic $topic, WampRequest $request, $event, array $exclude, array $eligible)
    {
        $participants = $request->getAttributes()->get('participants');
        $regExp = preg_match('/^[0-9]+-[0-9]+$/', $participants);
        /**@var $currUser Lover*/
        $currUser = $this->getClientManipulator()->getClient($connection);

        if($currUser && is_string($participants) && $regExp && in_array($currUser->getId(), explode('-', $participants))) {
            $otherParticipant = str_replace($currUser->getId(), '', str_replace('-', '', $participants));
            $guestLover = $this->getEntityManager()->getRepository('FindLoverBundle:Lover')->find($otherParticipant);
            $chat = $this->getEntityManager()->getRepository('FindLoverBundle:Chat')
                                             ->findOneBy(array(
                                                 'participants' => array(
                                                     "{$currUser->getId()}, $otherParticipant",
                                                     "$otherParticipant, {$currUser->getId()}"
                                                 )
                                             ));

            if(strpos($participants, strval($currUser->getId())) !== false) {
                if(null == $chat) {
                    if($guestLover != null) {
                        $chat = new Chat();
                        $chatPath = "{$this->getRootDir()}/../src/FindLoverBundle/Resources/chats/chat-$participants.txt";
                        $chat->setParticipants(str_replace('-', ', ', $participants));
                        $chat->setChatFilePath($chatPath);
                        fclose(fopen($chatPath, 'w'));
                    } else {
                        $topic->broadcast(false);
                        return;
                    }
                }
                $dateWritten = new \DateTime();

                $event = trim($event);
                $event = "$event|=>id={$currUser->getId()}|=>date={$dateWritten->format('Y-m-d H:i:s')}";

                $chat->writeDownMessage($event);

                $this->getEntityManager()->persist($chat);
                $this->getEntityManager()->flush();

                $topic->broadcast($this->getJmsSerializer()->serialize(new ChatHelper($event, $currUser, $guestLover), 'json'));
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
     * @return ClientManipulatorInterface
     */
    public function getClientManipulator()
    {
        return $this->clientManipulator;
    }

    /**
     * @param ClientManipulatorInterface $clientManipulator
     */
    public function setClientManipulator($clientManipulator): void
    {
        $this->clientManipulator = $clientManipulator;
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

    /**
     * @return string
     */
    public function getRootDir()
    {
        return $this->rootDir;
    }

    /**
     * @param string $rootDir
     */
    public function setRootDir($rootDir)
    {
        $this->rootDir = $rootDir;
    }


}
