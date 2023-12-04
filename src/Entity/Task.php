<?php

namespace App\Entity;

use App\Repository\TaskRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TaskRepository::class)]
class Task
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $title = null;

    #[ORM\ManyToOne]
    private ?User $assignedTo = null;

    #[ORM\Column]
    private ?bool $enable = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $lastCompletedAt = null;

    #[ORM\Column(nullable: true)]
    private ?int $recurrence = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        $this->title = $title;

        return $this;
    }

    public function getAssignedTo(): ?User
    {
        return $this->assignedTo;
    }

    public function setAssignedTo(?User $assignedTo): static
    {
        $this->assignedTo = $assignedTo;

        return $this;
    }

    public function isEnable(): ?bool
    {
        return $this->enable;
    }

    public function setEnable(bool $enable): static
    {
        $this->enable = $enable;

        return $this;
    }

    public function getLastCompletedAt(): ?\DateTimeInterface
    {
        return $this->lastCompletedAt;
    }

    public function setLastCompletedAt(?\DateTimeInterface $lastCompletedAt): static
    {
        $this->lastCompletedAt = $lastCompletedAt;

        return $this;
    }

    public function getRecurrence(): ?int
    {
        return $this->recurrence;
    }

    public function setRecurrence(?int $recurrence): static
    {
        $this->recurrence = $recurrence;

        return $this;
    }

}
