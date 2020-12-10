<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Tests\Fixtures;

use Nucleos\Doctrine\Model\ConfirmableInterface;
use Nucleos\Doctrine\Model\DeletableInterface;
use Nucleos\Doctrine\Model\LifecycleDateTimeInterface;
use Nucleos\Doctrine\Model\PositionAwareInterface;
use Nucleos\Doctrine\Model\Traits\ConfirmableTrait;
use Nucleos\Doctrine\Model\Traits\DeleteableTrait;
use Nucleos\Doctrine\Model\Traits\LifecycleDateTimeTrait;
use Nucleos\Doctrine\Model\Traits\SortableTrait;
use Nucleos\Doctrine\Model\UniqueActiveInterface;

final class ClassWithAllProperties implements DeletableInterface, ConfirmableInterface, LifecycleDateTimeInterface, PositionAwareInterface, UniqueActiveInterface
{
    use ConfirmableTrait;
    use DeleteableTrait;
    use LifecycleDateTimeTrait;
    use SortableTrait;

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
