<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\Persistence\Event\LifecycleEventArgs;

/**
 * @ORM\Entity(repositoryClass=OrderRepository::class)
 * @ORM\Table(name="`order`")
 * @HasLifecycleCallbacks
 */
class Order
{
    const ORDER_IS_PENDING = 0;
    const ORDER_IN_DELIVERY = 1;
    const ORDER_IS_READY = 2;
    const statuses = ["Замовлення в обробці", "Замовлення в службі доставки", "Замовлення виконано!"];
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="orders")
     */
    private $authorizedUser;

    /**
     * @ORM\ManyToOne(targetEntity=UnauthorizedUser::class, inversedBy="orders")
     */
    private $unauthorizedUser;

    /**
     * @ORM\Column(type="integer")
     */
    private $total;

    /**
     * @ORM\OneToMany(targetEntity=OrderLine::class, mappedBy="clientOrder")
     */
    private $orderLines;

    /**
     * @ORM\Column(type="integer")
     */
    private $status;

    /**
     * @ORM\Column(name="created_at", type="datetime", nullable=false)
     */
    private $createdAt;

    /**
     * @ORM\PrePersist
     */
    public function updatedTimestamps(LifecycleEventArgs $eventArgs): void
    {
        $dateTimeNow = new DateTime('now');
        $dateTimeNow->format('Y-m-d H:i:s');


        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt($dateTimeNow);
        }
    }

    public function getCreatedAt() :?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(DateTime $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function __construct()
    {
        $this->orderLines = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthorizedUser(): ?User
    {
        return $this->authorizedUser;
    }

    public function setAuthorizedUser(?User $authorizedUser): self
    {
        $this->authorizedUser = $authorizedUser;

        return $this;
    }

    public function getUnauthorizedUser(): ?UnauthorizedUser
    {
        return $this->unauthorizedUser;
    }

    public function setUnauthorizedUser(?UnauthorizedUser $unauthorizedUser): self
    {
        $this->unauthorizedUser = $unauthorizedUser;

        return $this;
    }

    public function getTotal(): ?int
    {
        return $this->total;
    }

    public function setTotal(int $total): self
    {
        $this->total = $total;

        return $this;
    }

    /**
     * @return Collection<int, OrderLine>
     */
    public function getOrderLines(): Collection
    {
        return $this->orderLines;
    }

    public function addOrderLine(OrderLine $orderLine): self
    {
        if (!$this->orderLines->contains($orderLine)) {
            $this->orderLines[] = $orderLine;
            $orderLine->setClientOrder($this);
        }

        return $this;
    }

    public function removeOrderLine(OrderLine $orderLine): self
    {
        if ($this->orderLines->removeElement($orderLine)) {
            // set the owning side to null (unless already changed)
            if ($orderLine->getClientOrder() === $this) {
                $orderLine->setClientOrder(null);
            }
        }

        return $this;
    }

    public function getStatus(): ?string
    {
        return self::statuses[$this->status];
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }
}
