<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Model;

interface DeletableInterface
{
    /**
     * @return \DateTime
     */
    public function getDeletedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $deletedAt
     */
    public function setDeletedAt(?\DateTime $deletedAt): void;

    /**
     * @param bool $deleted
     */
    public function setDeleted(bool $deleted): void;

    /**
     * @return bool
     */
    public function isDeleted(): bool;
}
