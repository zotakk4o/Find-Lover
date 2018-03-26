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

    private $firstLover;

    private $secondLover;

    /**
     * @throws \Exception
     */
    public function __construct($data, $firstLover, $secondLover)
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

        if($firstLover instanceof Lover) {
            $this->setFirstLover($firstLover);
        } else {
            throw new \Exception("Data supplied for first lover not in correct format!");
        }

        if($secondLover instanceof Lover) {
            $this->setSecondLover($secondLover);
        } else {
            throw new \Exception("Data supplied for second lover not in correct format!");
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

    /**
     * @return Lover
     */
    public function getFirstLover()
    {
        return $this->firstLover;
    }

    /**
     * @param Lover $firstLover
     */
    public function setFirstLover($firstLover)
    {
        $this->firstLover = $firstLover;
    }

    /**
     * @return Lover
     */
    public function getSecondLover()
    {
        return $this->secondLover;
    }

    /**
     * @param Lover $secondLover
     */
    public function setSecondLover($secondLover)
    {
        $this->secondLover = $secondLover;
    }
}
