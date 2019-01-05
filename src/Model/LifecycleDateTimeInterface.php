<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Model;

interface LifecycleDateTimeInterface
{
    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime;

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime;

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(?\DateTime $createdAt): void;

    /**
     * Set updatedAt.
     *
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(?\DateTime $updatedAt): void;
}
