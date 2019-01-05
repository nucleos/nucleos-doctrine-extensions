<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Model\Traits;

trait DeleteableTrait
{
    /**
     * @var \DateTime|null
     */
    protected $deletedAt;

    /**
     * Get deletedAt.
     *
     * @return \DateTime|null
     */
    public function getDeletedAt(): ?\DateTime
    {
        return $this->deletedAt;
    }

    /**
     * Set deletedAt.
     *
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt(?\DateTime $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    /**
     * Set deleted.
     *
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void
    {
        if ($deleted) {
            $this->setDeletedAt(new \DateTime());
        } else {
            $this->setDeletedAt(null);
        }
    }

    /**
     * Get deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}
