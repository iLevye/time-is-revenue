<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="fos_user")
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
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

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Receipt", mappedBy="user")
     */
    private $receipts;

    /**
     * @ORM\OneToOne(targetEntity="App\Entity\InvoiceSettings", mappedBy="user", cascade={"persist", "remove"})
     */
    private $invoiceSettings;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $asanaAccessToken;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\AsanaProject", mappedBy="user")
     */
    private $asanaProjects;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $telegramReportChatId;

    public function __construct()
    {
        parent::__construct();
        $this->workspaces = new ArrayCollection();
        $this->authorizedWorkspaces = new ArrayCollection();
        $this->receipts = new ArrayCollection();
        $this->asanaProjects = new ArrayCollection();
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

    /**
     * @return Collection|Receipt[]
     */
    public function getReceipts(): Collection
    {
        return $this->receipts;
    }

    public function addReceipt(Receipt $receipt): self
    {
        if (!$this->receipts->contains($receipt)) {
            $this->receipts[] = $receipt;
            $receipt->setUser($this);
        }

        return $this;
    }

    public function removeReceipt(Receipt $receipt): self
    {
        if ($this->receipts->contains($receipt)) {
            $this->receipts->removeElement($receipt);
            // set the owning side to null (unless already changed)
            if ($receipt->getUser() === $this) {
                $receipt->setUser(null);
            }
        }

        return $this;
    }

    public function getInvoiceSettings(): ?InvoiceSettings
    {
        return $this->invoiceSettings;
    }

    public function setInvoiceSettings(InvoiceSettings $invoiceSettings): self
    {
        $this->invoiceSettings = $invoiceSettings;

        // set the owning side of the relation if necessary
        if ($this !== $invoiceSettings->getUser()) {
            $invoiceSettings->setUser($this);
        }

        return $this;
    }

    public function getAsanaAccessToken(): ?string
    {
        return $this->asanaAccessToken;
    }

    public function setAsanaAccessToken(?string $asanaAccessToken): self
    {
        $this->asanaAccessToken = $asanaAccessToken;

        return $this;
    }

    /**
     * @return Collection|AsanaProject[]
     */
    public function getAsanaProjects(): Collection
    {
        return $this->asanaProjects;
    }

    public function addAsanaProject(AsanaProject $asanaProject): self
    {
        if (!$this->asanaProjects->contains($asanaProject)) {
            $this->asanaProjects[] = $asanaProject;
            $asanaProject->setUser($this);
        }

        return $this;
    }

    public function removeAsanaProject(AsanaProject $asanaProject): self
    {
        if ($this->asanaProjects->contains($asanaProject)) {
            $this->asanaProjects->removeElement($asanaProject);
            // set the owning side to null (unless already changed)
            if ($asanaProject->getUser() === $this) {
                $asanaProject->setUser(null);
            }
        }

        return $this;
    }

    public function getTelegramReportChatId(): ?string
    {
        return $this->telegramReportChatId;
    }

    public function setTelegramReportChatId(string $telegramReportChatId): self
    {
        $this->telegramReportChatId = $telegramReportChatId;

        return $this;
    }
}