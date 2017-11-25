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
     * @ORM\Column(name="date_accomplished", type="datetime", nullable=true)
     */
    private $dateAccomplished;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_sent", type="datetime")
     */
    private $dateSent;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="sender_id", type="integer")
	 */
	private $senderId;

	/**
	 * @var integer
	 *
	 * @ORM\Column(name="receiver_id", type="integer")
	 */
	private $receiverId;


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
     * @return Invitation
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
     * @return int
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param int $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
    }

    /**
     * @return int
     */
    public function getReceiverId()
    {
        return $this->receiverId;
    }

    /**
     * @param int $receiverId
     */
    public function setReceiverId($receiverId)
    {
        $this->receiverId = $receiverId;
    }


}

