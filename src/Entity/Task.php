<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\TaskRepository")
 */
class Task
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Project", inversedBy="tasks")
     */
    private $project;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isBillable = true;

    /**
     * @var bool
     */
    private $isRunning = false;

    /**
     * @var int
     */
    private $elapsedTime;

    /**
     * @var \DateTime|null
     */
    private $lastTimeStartedAt;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Time", mappedBy="task")
     */
    private $times;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Workspace", inversedBy="tasks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $workspace;

    /**
     * @ORM\Column(type="float")
     */
    private $billableRate;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $asanaUrl;

    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $date;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Receipt", inversedBy="tasks")
     */
    private $receipt;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $receiptTime;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $receiptPrice;

    public function __construct()
    {
        $this->times = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getIsBillable(): ?bool
    {
        return $this->isBillable;
    }

    public function setIsBillable(bool $isBillable): self
    {
        $this->isBillable = $isBillable;

        return $this;
    }

    /**
     * @return Collection|Time[]
     */
    public function getTimes(): Collection
    {
        return $this->times;
    }

    public function addTime(Time $time): self
    {
        if (!$this->times->contains($time)) {
            $this->times[] = $time;
            $time->setTask($this);
        }

        return $this;
    }

    public function removeTime(Time $time): self
    {
        if ($this->times->contains($time)) {
            $this->times->removeElement($time);
            // set the owning side to null (unless already changed)
            if ($time->getTask() === $this) {
                $time->setTask(null);
            }
        }

        return $this;
    }

    /**
     * @return bool
     */
    public function isIsRunning(): bool
    {
        return $this->isRunning;
    }

    /**
     * @param bool $isRunning
     */
    public function setIsRunning(bool $isRunning)
    {
        $this->isRunning = $isRunning;
    }

    /**
     * @return int|null
     */
    public function getElapsedTime(): ?int
    {
        return $this->elapsedTime;
    }

    /**
     * @param int $elapsedTime
     */
    public function setElapsedTime(int $elapsedTime)
    {
        $this->elapsedTime = $elapsedTime;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastTimeStartedAt(): ?\DateTime
    {
        return $this->lastTimeStartedAt;
    }

    /**
     * @param \DateTime $lastTimeStartedAt
     */
    public function setLastTimeStartedAt(\DateTime $lastTimeStartedAt)
    {
        $this->lastTimeStartedAt = $lastTimeStartedAt;
    }

    public function getWorkspace(): ?Workspace
    {
        return $this->workspace;
    }

    public function setWorkspace(?Workspace $workspace): self
    {
        $this->workspace = $workspace;

        return $this;
    }

    public function getBillableRate(): ?float
    {
        return $this->billableRate;
    }

    public function setBillableRate(float $billableRate): self
    {
        $this->billableRate = $billableRate;

        return $this;
    }

    public function getAsanaUrl(): ?string
    {
        return $this->asanaUrl;
    }

    public function setAsanaUrl(?string $asanaUrl): self
    {
        $this->asanaUrl = $asanaUrl;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReceipt(): ?Receipt
    {
        return $this->receipt;
    }

    public function setReceipt(?Receipt $receipt): self
    {
        $this->receipt = $receipt;

        return $this;
    }

    public function getReceiptTime(): ?float
    {
        return $this->receiptTime;
    }

    public function setReceiptTime(?float $ReceiptTime): self
    {
        $this->receiptTime = $ReceiptTime;

        return $this;
    }

    public function getReceiptPrice(): ?float
    {
        return $this->receiptPrice;
    }

    public function setReceiptPrice(float $ReceiptPrice): self
    {
        $this->receiptPrice = $ReceiptPrice;

        return $this;
    }


}
