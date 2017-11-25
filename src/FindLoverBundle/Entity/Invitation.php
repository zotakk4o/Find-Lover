<?php

namespace FindLoverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Invitation
 *
 * @ORM\Table(name="invitation")
 * @ORM\Entity(repositoryClass="FindLoverBundle\Repository\InvitationRepository")
 */
class Invitation
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
     * @ORM\Column(name="date_sent", type="datetime")
     */
    private $dateSent;

    /**
     * @var string
     *
     * @ORM\Column(name="participants", type="string")
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
     * Set dateSent
     *
     * @param \DateTime $dateSent
     *
     * @return Invitation
     */
    public function setDateSent($dateSent)
    {
        $this->dateSent = $dateSent;

        return $this;
    }

    /**
     * Get dateSent
     *
     * @return \DateTime
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    /**
     * @return string
     */
    public function getParticipantsString()
    {
        return $this->participants;
    }

    /**
     * @return int[]
     */
    public function getParticipantsArray() {
        return explode(', ', $this->getParticipantsString());
    }

    /**
     * @param string $participants
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;
    }

}

