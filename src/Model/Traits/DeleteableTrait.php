<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Model\Traits;

use DateTimeImmutable;

trait DeleteableTrait
{
    protected ?DateTimeImmutable $deletedAt = null;

    public function getDeletedAt(): ?DateTimeImmutable
    {
        return $this->deletedAt;
    }

    public function setDeletedAt(?DateTimeImmutable $deletedAt): void
    {
        $this->deletedAt = $deletedAt;
    }

    public function setDeleted(bool $deleted): void
    {
        if ($deleted) {
            $this->setDeletedAt(new DateTimeImmutable());
        } else {
            $this->setDeletedAt(null);
        }
    }

    public function isDeleted(): bool
    {
        return null !== $this->deletedAt;
    }
}
