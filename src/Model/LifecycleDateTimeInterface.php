<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model;

interface LifecycleDateTimeInterface
{
    /**
     * Get createdAt.
     *
     * @return \DateTime|null
     */
    public function getCreatedAt(): ? \DateTime;

    /**
     * Get updatedAt.
     *
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ? \DateTime;

    /**
     * Set createdAt.
     *
     * @param \DateTime|null $createdAt
     *
     * @return $this
     */
    public function setCreatedAt(? \DateTime $createdAt);

    /**
     * Set updatedAt.
     *
     * @param \DateTime|null $updatedAt
     *
     * @return $this
     */
    public function setUpdatedAt(? \DateTime $updatedAt);
}
