<?php

namespace App\Entity;

use App\Repository\ChatUserRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatUserRepository::class)
 */
class ChatUser
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=workers::class, inversedBy="chatUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $worker;

    /**
     * @ORM\ManyToOne(targetEntity=chat::class, inversedBy="chatUsers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chat;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getWorker(): ?workers
    {
        return $this->worker;
    }

    public function setWorker(?workers $worker): self
    {
        $this->worker = $worker;

        return $this;
    }

    public function getChat(): ?chat
    {
        return $this->chat;
    }

    public function setChat(?chat $chat): self
    {
        $this->chat = $chat;

        return $this;
    }
}
