<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\PrizeRepository")
 */
class Prize
{
    public const TYPE_MONEY = 'money';
    public const TYPE_GIFT = 'gift';
    public const TYPE_BONUS = 'bonus';

    public const STATUS_ACTIVE = 'active';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_CANCELED = 'canceled';

    public const GIFT_TYPES = [
        'iphone', 'accordion', 'cookie'
    ];

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $type;

    /**
     * @ORM\Column(type="string")
     */
    private $status;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdDate;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $value;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    private $gift;

    /**
     * @ORM\Column(type="boolean")
     */
    private $isFinished;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="posts")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct(User $user, string $type)
    {
        $this->user = $user;
        $this->type = $type;
        $this->status = self::STATUS_ACTIVE;
        $this->isFinished = false;

        if ($type !== self::TYPE_GIFT) {
            $this->value = rand(1, 1000);
        } else {
            $this->gift = self::GIFT_TYPES[array_rand(self::GIFT_TYPES)];
        }

        $this->createdDate = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function markAsCanceled(): void
    {
        $this->status = self::STATUS_CANCELED;
    }

    public function markAsAccepted(): void
    {
        $this->status = self::STATUS_ACCEPTED;
    }

    public function markAsFinished(): void
    {
        $this->isFinished = true;
    }

    public function isMoneyPrize(): bool
    {
        return $this->type === self::TYPE_MONEY;
    }

    public function isBonusPrize(): bool
    {
        return $this->type === self::TYPE_BONUS;
    }

    public function isGiftPrize(): bool
    {
        return $this->type === self::TYPE_GIFT;
    }

    public function ensureUserHavePermissions(User $user): bool
    {
        return $this->user->getId() === $user->getId();
    }
}
