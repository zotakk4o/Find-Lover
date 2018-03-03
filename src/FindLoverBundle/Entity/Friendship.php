<?php

namespace FindLoverBundle\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Friendship
 *
 * @ORM\Table(name="friendship")
 * @ORM\Entity(repositoryClass="FindLoverBundle\Repository\FriendshipRepository")
 */
class Friendship
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_accomplished", type="datetime")
     */
    private $dateAccomplished;

    /**
     * @var string
     *
     * @ORM\Column(name="participants", type="string", length=255)
     */
    private $participants;


    /**
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set dateAccomplished
     *
     * @param \DateTime $dateAccomplished
     *
     * @return Friendship
     */
    public function setDateAccomplished($dateAccomplished)
    {
        $this->dateAccomplished = $dateAccomplished;

        return $this;
    }

    /**
     * Get dateAccomplished
     *
     * @return \DateTime
     */
    public function getDateAccomplished()
    {
        return $this->dateAccomplished;
    }

    /**
     * @return string
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @param string $participants
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;
    }


}
