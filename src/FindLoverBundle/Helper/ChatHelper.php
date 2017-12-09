<?php
/**
 * Created by PhpStorm.
 * User: zotakk
 * Date: 12/8/17
 * Time: 9:44 AM
 */

namespace FindLoverBundle\Helper;

class ChatHelper
{
    private $senderId;

    private $dateSent;

    private $message;

    public function __construct($chatLine)
    {
        $this->parseChatLine($chatLine);
    }

    /**
     * @param string $chatLine
     */
    private function parseChatLine($chatLine)
    {
        $data = explode('|=>', $chatLine);
        if(count($data) !== 1) {
            $this->setMessage($data[0]);
            $this->setSenderId(explode('=', $data[1])[1]);
            $this->setDateSent(explode('=', $data[2])[1]);
        } else {
            $this->setMessage($chatLine);
        }

    }

    /**
     * @return string
     */
    public function getSenderId()
    {
        return $this->senderId;
    }

    /**
     * @param string $senderId
     */
    public function setSenderId($senderId)
    {
        $this->senderId = $senderId;
    }

    /**
     * @return string
     */
    public function getDateSent()
    {
        return $this->dateSent;
    }

    /**
     * @param string $dateSent
     */
    public function setDateSent($dateSent)
    {
        $this->dateSent = $dateSent;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }


}