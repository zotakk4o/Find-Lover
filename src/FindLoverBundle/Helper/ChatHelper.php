<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 12/8/17
 * Time: 9:44 AM
 */

namespace FindLoverBundle\Helper;

use FindLoverBundle\Entity\Lover;

class ChatHelper
{

    private $messages;

    private $currentLover;

    private $guestLover;

    /**
     * @throws \Exception
     */
    public function __construct($data, $currentLover, $guestLover)
    {
        if(is_array($data)) {
            foreach ($data as $datum) {
                $this->parseChatLine($datum);
            }
        } else if (is_string($data)) {
            $this->parseChatLine($data);
        } else {
            throw new \Exception("Data supplied for ChatHelper not in correct format!");
        }

        if($currentLover instanceof Lover) {
            $this->setCurrentLover($currentLover);
        } else {
            throw new \Exception("Data supplied for CurrentLover not in correct format!");
        }

        if($guestLover instanceof Lover) {
            $this->setGuestLover($guestLover);
        } else {
            throw new \Exception("Data supplied for GuestLover not in correct format!");
        }
    }

    /**
     * @param string $chatLine
     */
    private function parseChatLine($chatLine)
    {
        $this->addMessage(new Message($chatLine));
    }

    /**
     * @return Lover
     */
    public function getCurrentLover()
    {
        return $this->currentLover;
    }

    /**
     * @param Lover $currentLover
     */
    public function setCurrentLover($currentLover)
    {
        $this->currentLover = $currentLover;
    }

    /**
     * @return Lover
     */
    public function getGuestLover()
    {
        return $this->guestLover;
    }

    /**
     * @param Lover $guestLover
     */
    public function setGuestLover($guestLover)
    {
        $this->guestLover = $guestLover;
    }

    /**
     * @return Message[]
     */
    public function getMessages()
    {
        return $this->messages;
    }

    /**
     * @param Message $message
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

}
