<?php

namespace App\Entity;

use App\Repository\WorkSessionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WorkSessionRepository::class)]
class WorkSession
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $start = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $finish = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $employee = null;

    #[ORM\OneToMany(mappedBy: 'workSession', targetEntity: WorkSessionComment::class, orphanRemoval: true)]
    private Collection $comments;

    #[ORM\OneToMany(mappedBy: 'workSession', targetEntity: Task::class)]
    private Collection $completedTasks;

    public function __construct()
    {
        $this->comments = new ArrayCollection();
        $this->completedTasks = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): static
    {
        $this->start = $start;

        return $this;
    }

    public function getFinish(): ?\DateTimeInterface
    {
        return $this->finish;
    }

    public function setFinish(?\DateTimeInterface $finish): static
    {
        $this->finish = $finish;

        return $this;
    }

    public function getEmployee(): ?User
    {
        return $this->employee;
    }

    public function setEmployee(?User $employee): static
    {
        $this->employee = $employee;

        return $this;
    }

    public function getDuration(): ?\DateInterval
    {
        if($this->start === null) {
            return null;
        }

        if($this->finish === null) {
            return null;
        }

        return $this->start->diff($this->finish);
    }

    /**
     * @return Collection<int, WorkSessionComment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(WorkSessionComment $workSessionComment): static
    {
        if (!$this->comments->contains($workSessionComment)) {
            $this->comments->add($workSessionComment);
            $workSessionComment->setWorkSession($this);
        }

        return $this;
    }

    public function removeComment(WorkSessionComment $workSessionComment): static
    {
        if ($this->comments->removeElement($workSessionComment)) {
            // set the owning side to null (unless already changed)
            if ($workSessionComment->getWorkSession() === $this) {
                $workSessionComment->setWorkSession(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Task>
     */
    public function getCompletedTasks(): Collection
    {
        return $this->completedTasks;
    }

    public function addCompletedTask(Task $completedTask): static
    {
        if (!$this->completedTasks->contains($completedTask)) {
            $this->completedTasks->add($completedTask);
            $completedTask->setWorkSession($this);
        }

        return $this;
    }

    public function removeCompletedTask(Task $completedTask): static
    {
        if ($this->completedTasks->removeElement($completedTask)) {
            // set the owning side to null (unless already changed)
            if ($completedTask->getWorkSession() === $this) {
                $completedTask->setWorkSession(null);
            }
        }

        return $this;
    }
}
