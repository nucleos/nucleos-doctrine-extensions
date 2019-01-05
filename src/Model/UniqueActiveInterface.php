<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Model;

interface UniqueActiveInterface
{
    /**
     * Set active.
     *
     * @param bool $active
     */
    public function setActive(bool $active): void;

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
