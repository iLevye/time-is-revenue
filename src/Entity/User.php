<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Workspace", mappedBy="owner")
     */
    private $workspaces;

    /**
     * @ORM\ManyToMany(targetEntity="App\Entity\Workspace", mappedBy="users")
     */
    private $authorizedWorkspaces;

    public function __construct()
    {
        parent::__construct();
        $this->workspaces = new ArrayCollection();
        $this->authorizedWorkspaces = new ArrayCollection();
        // your own logic
    }

    /**
     * @return Collection|Workspace[]
     */
    public function getWorkspaces(): Collection
    {
        return $this->workspaces;
    }

    public function addWorkspace(Workspace $workspace): self
    {
        if (!$this->workspaces->contains($workspace)) {
            $this->workspaces[] = $workspace;
            $workspace->setOwner($this);
        }

        return $this;
    }

    public function removeWorkspace(Workspace $workspace): self
    {
        if ($this->workspaces->contains($workspace)) {
            $this->workspaces->removeElement($workspace);
            // set the owning side to null (unless already changed)
            if ($workspace->getOwner() === $this) {
                $workspace->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Workspace[]
     */
    public function getAuthorizedWorkspaces(): Collection
    {
        return $this->authorizedWorkspaces;
    }

    public function addAuthorizedWorkspace(Workspace $authorizedWorkspace): self
    {
        if (!$this->authorizedWorkspaces->contains($authorizedWorkspace)) {
            $this->authorizedWorkspaces[] = $authorizedWorkspace;
            $authorizedWorkspace->addUser($this);
        }

        return $this;
    }

    public function removeAuthorizedWorkspace(Workspace $authorizedWorkspace): self
    {
        if ($this->authorizedWorkspaces->contains($authorizedWorkspace)) {
            $this->authorizedWorkspaces->removeElement($authorizedWorkspace);
            $authorizedWorkspace->removeUser($this);
        }

        return $this;
    }
}