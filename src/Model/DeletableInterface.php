<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model;

interface DeletableInterface
{
    /**
     * Get deletedAt.
     *
     * @return \DateTime
     */
    public function getDeletedAt(): ?\DateTime;

    /**
     * Set deletedAt.
     *
     * @param \DateTime|null $deletedAt
     *
     * @return self
     */
    public function setDeletedAt(?\DateTime $deletedAt);

    /**
     * Set deleted.
     *
     * @param bool $deleted
     *
     * @return self
     */
    public function setDeleted(bool $deleted);

    /**
     * Get deleted.
     *
     * @return bool
     */
    public function isDeleted(): bool;
}
