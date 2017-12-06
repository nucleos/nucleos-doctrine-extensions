<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model\Traits;

trait DeleteableTrait
{
    /**
     * @var \DateTime
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
     *
     * @return $this
     */
    public function setDeletedAt(?\DateTime $deletedAt)
    {
        $this->deletedAt = $deletedAt;

        return $this;
    }

    /**
     * Set deleted.
     *
     * @param bool $deleted
     *
     * @return $this
     */
    public function setDeleted(bool $deleted)
    {
        if ($deleted) {
            $this->setDeletedAt(new \DateTime());
        } else {
            $this->setDeletedAt(null);
        }

        return $this;
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
