<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Model\Traits;

trait SortableTrait
{
    protected ?int $position = null;

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @return string[]
     */
    public function getPositionGroup(): array
    {
        return [];
    }
}
