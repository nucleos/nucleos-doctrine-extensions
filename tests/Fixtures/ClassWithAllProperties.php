<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Tests\Fixtures;

use Core23\Doctrine\Model\ConfirmableInterface;
use Core23\Doctrine\Model\DeletableInterface;
use Core23\Doctrine\Model\LifecycleDateTimeInterface;
use Core23\Doctrine\Model\PositionAwareInterface;
use Core23\Doctrine\Model\Traits\ConfirmableTrait;
use Core23\Doctrine\Model\Traits\DeleteableTrait;
use Core23\Doctrine\Model\Traits\LifecycleDateTimeTrait;
use Core23\Doctrine\Model\Traits\SortableTrait;
use Core23\Doctrine\Model\UniqueActiveInterface;

final class ClassWithAllProperties implements DeletableInterface, ConfirmableInterface, LifecycleDateTimeInterface, PositionAwareInterface, UniqueActiveInterface
{
    use DeleteableTrait, ConfirmableTrait, LifecycleDateTimeTrait, SortableTrait;

    /**
     * @var bool
     */
    private $active;

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * Get list of unique fields.
     *
     * @return string[]
     */
    public function getUniqueActiveFields(): array
    {
        return ['field', 'otherfield'];
    }
}
