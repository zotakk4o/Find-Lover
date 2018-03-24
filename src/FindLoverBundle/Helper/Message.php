<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 3/24/18
 * Time: 4:12 PM
 */

namespace FindLoverBundle\Helper;


class Message
{
    private $senderId;
    private $dateSent;
    private $message;

    public function __construct($message)
    {
        $this->parseMessage($message);
    }
    /**
     * @param string $message
     */
    private function parseMessage($message)
    {
        $data = explode('|=>', $message);
        if(count($data) !== 1) {
            $this->setMessage($data[0]);
            $this->setSenderId(explode('=', $data[1])[1]);
            $this->setDateSent(explode('=', $data[2])[1]);
        } else {
            $this->setMessage($message);
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