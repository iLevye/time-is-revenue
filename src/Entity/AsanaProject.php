<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AsanaProjectRepository")
 */
class AsanaProject
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $asanaId;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="asanaProjects")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\Project", inversedBy="asanaProject", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     */
    private $project;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isPinned;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $asanaWorkspaceId;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAsanaId(): ?string
    {
        return $this->asanaId;
    }

    public function setAsanaId(string $asanaId): self
    {
        $this->asanaId = $asanaId;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(Project $project): self
    {
        $this->project = $project;

        return $this;
    }

    public function getIsPinned(): ?bool
    {
        return $this->isPinned;
    }

    public function setIsPinned(bool $isPinned): self
    {
        $this->isPinned = $isPinned;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getAsanaWorkspaceId(): ?string
    {
        return $this->asanaWorkspaceId;
    }

    public function setAsanaWorkspaceId(string $asanaWorkspaceId): self
    {
        $this->asanaWorkspaceId = $asanaWorkspaceId;

        return $this;
    }
}
