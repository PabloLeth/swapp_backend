<?php

namespace App\Entity;

use App\Repository\ChatTypeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ChatTypeRepository::class)
 */
class ChatType
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
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=chat::class, mappedBy="chatType")
     */
    private $chat;

    public function __construct()
    {
        $this->chat = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

        return $this;
    }

    /**
     * @return Collection|chat[]
     */
    public function getChat(): Collection
    {
        return $this->chat;
    }

    public function addChat(chat $chat): self
    {
        if (!$this->chat->contains($chat)) {
            $this->chat[] = $chat;
            $chat->setChatType($this);
        }

        return $this;
    }

    public function removeChat(chat $chat): self
    {
        if ($this->chat->removeElement($chat)) {
            // set the owning side to null (unless already changed)
            if ($chat->getChatType() === $this) {
                $chat->setChatType(null);
            }
        }

        return $this;
    }
}
