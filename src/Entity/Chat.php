<?php

namespace App\Entity;

use App\Repository\ChatRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatRepository::class)
 */
class Chat
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Message::class, mappedBy="chat", orphanRemoval=true)
     */
    private $messages;

    /**
     * @ORM\OneToMany(targetEntity=ChatUser::class, mappedBy="chat", orphanRemoval=true)
     */
    private $chatUsers;

    /**
     * @ORM\ManyToOne(targetEntity=ChatType::class, inversedBy="chat")
     * @ORM\JoinColumn(nullable=false)
     */
    private $chatType;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->chatUsers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Message[]
     */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): self
    {
        if (!$this->messages->contains($message)) {
            $this->messages[] = $message;
            $message->setChat($this);
        }

        return $this;
    }

    public function removeMessage(Message $message): self
    {
        if ($this->messages->removeElement($message)) {
            // set the owning side to null (unless already changed)
            if ($message->getChat() === $this) {
                $message->setChat(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ChatUser[]
     */
    public function getChatUsers(): Collection
    {
        return $this->chatUsers;
    }

    public function addChatUser(ChatUser $chatUser): self
    {
        if (!$this->chatUsers->contains($chatUser)) {
            $this->chatUsers[] = $chatUser;
            $chatUser->setChat($this);
        }

        return $this;
    }

    public function removeChatUser(ChatUser $chatUser): self
    {
        if ($this->chatUsers->removeElement($chatUser)) {
            // set the owning side to null (unless already changed)
            if ($chatUser->getChat() === $this) {
                $chatUser->setChat(null);
            }
        }

        return $this;
    }

    public function getChatType(): ?ChatType
    {
        return $this->chatType;
    }

    public function setChatType(?ChatType $chatType): self
    {
        $this->chatType = $chatType;

        return $this;
    }
}
