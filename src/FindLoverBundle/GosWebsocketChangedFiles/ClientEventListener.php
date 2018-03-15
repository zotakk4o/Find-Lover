<?php

namespace Gos\Bundle\WebSocketBundle\Event;

use Doctrine\ORM\EntityManager;
use Gos\Bundle\WebSocketBundle\Client\Auth\WebsocketAuthenticationProviderInterface;
use Gos\Bundle\WebSocketBundle\Client\ClientStorageInterface;
use Gos\Bundle\WebSocketBundle\Client\Exception\ClientNotFoundException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpKernel\Log\NullLogger;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @author Johann Saunier <johann_27@hotmail.fr>
 */
class ClientEventListener
{
    /**
     * @param ClientStorageInterface $clientStorage
     */
    protected $clientStorage;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var WebsocketAuthenticationProvider
     */
    protected $authenticationProvider;

    /**
     * @param ClientStorageInterface $clientStorage
     * @param WebsocketAuthenticationProvider $authenticationProvider
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        EntityManager $entityManager,
        ClientStorageInterface $clientStorage,
        WebsocketAuthenticationProviderInterface $authenticationProvider,
        LoggerInterface $logger = null
    ) {
        $this->entityManager = $entityManager;
        $this->clientStorage = $clientStorage;
        $this->authenticationProvider = $authenticationProvider;
        $this->logger = null === $logger ? new NullLogger() : $logger;
    }

    /**
     * @param ClientEvent $event
     *
     * @throws \Exception
     * @throws \Gos\Bundle\WebSocketBundle\Client\Exception\StorageException
     */
    public function onClientConnect(ClientEvent $event)
    {
        $conn = $event->getConnection();
        $userId = $this->authenticationProvider->authenticate($conn)->getUser()->getId();

        $user = $this->getEntityManager()->getRepository('FindLoverBundle:Lover')->find($userId);
        $user->setLastOnline(null);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     * Called whenever a client disconnects.
     *
     * @param ClientEvent $event
     */
    public function onClientDisconnect(ClientEvent $event)
    {
        $conn = $event->getConnection();
        try {
            $user = $this->clientStorage->getClient($conn->WAMP->clientStorageId);
            $userId = $this->authenticationProvider->authenticate($conn)->getUser()->getId();
            //go here only if getClient doesn't throw error
            $this->clientStorage->removeClient($conn->resourceId);
            $username = $user instanceof UserInterface
                ? $user->getUsername()
                : $user;

            $loggerContext['username'] = $username;

            $this->logger->info(sprintf(
                '%s disconnected',
                $username
            ), $loggerContext);

            $user = $this->getEntityManager()->getRepository('FindLoverBundle:Lover')->find($userId);
            $user->setLastOnline(new \DateTime());

            $em = $this->getEntityManager();
            $em->persist($user);
            $em->flush();

            $conn = $event->getConnection();

            $loggerContext = array(
                'connection_id' => $conn->resourceId,
                'session_id' => $conn->WAMP->sessionId,
                'storage_id' => $conn->WAMP->clientStorageId,
            );
        } catch (ClientNotFoundException $e) {
            $this->logger->info('user timed out', $loggerContext);
        }
    }

    /**
     * Called whenever a client errors.
     *
     * @param ClientErrorEvent $event
     */
    public function onClientError(ClientErrorEvent $event)
    {
        $conn = $event->getConnection();
        $e = $event->getException();

        $loggerContext = array(
            'connection_id' => $conn->resourceId,
            'session_id' => $conn->WAMP->sessionId,
        );

        if ($this->clientStorage->hasClient($conn->resourceId)) {
            $loggerContext['client'] = $this->clientStorage->getClient($conn->WAMP->clientStorageId);
        }

        $this->logger->error(sprintf(
            'Connection error occurred %s in %s line %s',
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ), $loggerContext);
    }

    /**
     * @param ClientRejectedEvent $event
     */
    public function onClientRejected(ClientRejectedEvent $event)
    {
        $this->logger->warning('Client rejected, bad origin', [
            'origin' => $event->getOrigin(),
        ]);
    }

    /**
     * @return EntityManager
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
}
