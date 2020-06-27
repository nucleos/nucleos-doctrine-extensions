<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Model;

interface UniqueActiveInterface
{
    public function setActive(bool $active): void;

    public function isActive(): bool;

    /**
     * Get list of unique fields.
     *
     * @return string[]
     */
    public function getUniqueActiveFields(): array;
}
