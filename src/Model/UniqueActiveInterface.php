<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model;

interface UniqueActiveInterface
{
    /**
     * Get id.
     *
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set active.
     *
     * @param bool $active
     *
     * @return self
     */
    public function setActive(bool $active);

    /**
     * Get active.
     *
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Get list of unique fields.
     *
     * @return string[]
     */
    public function getUniqueActiveFields(): array;
}
