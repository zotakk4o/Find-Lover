<?php

namespace FindLoverBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Chat
 *
 * @ORM\Table(name="chat")
 * @ORM\Entity(repositoryClass="FindLoverBundle\Repository\ChatRepository")
 */
class Chat
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
     * @var string
     *
     * @ORM\Column(name="participants", type="string", length=255, unique=true)
     */
    private $participants;

    /**
     * @var string
     *
     * @ORM\Column(name="chat_file", type="string", length=255, unique=true)
     */
    private $chatFile;


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
     * Set participants
     *
     * @param string $participants
     *
     * @return Chat
     */
    public function setParticipants($participants)
    {
        $this->participants = $participants;

        return $this;
    }

    /**
     * Get participants
     *
     * @return string
     */
    public function getParticipants()
    {
        return $this->participants;
    }

    /**
     * @return string
     */
    public function getChatFilePath()
    {
        return $this->chatFile;
    }

    /**
     * @param string $chatFilePath
     *
     * @return Chat
     */
    public function setChatFilePath($chatFilePath)
    {
        $this->chatFile = $chatFilePath;

        return $this;
    }

    public function readFromLine($offset = 0)
    {
        $output = '';
        $file = new \SplFileObject($this->getChatFilePath());
        $file->seek($offset);

        for($i = $offset; !$file->eof() && $i < 6 + $offset; $i++) {
            $output .= $file->current();
            $file->next();
        }

        return $output;
    }

    public function writeDownMessage($string)
    {
        if(! empty($string)) {
            $originalName = $this->getChatFilePath();
            $context = stream_context_create();
            $originalFile = fopen($originalName, 'r', 1, $context);

            $tempName = tempnam(sys_get_temp_dir(), 'chat_prefix');
            file_put_contents($tempName, $string);
            file_put_contents($tempName, $originalFile, FILE_APPEND);

            fclose($originalFile);
            unlink($originalName);
            rename($tempName, $originalName);
        }
    }
}
