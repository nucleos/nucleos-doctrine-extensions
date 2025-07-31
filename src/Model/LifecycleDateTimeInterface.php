<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Model;

use DateTimeImmutable;

interface LifecycleDateTimeInterface
{
    public function getCreatedAt(): ?DateTimeImmutable;

    public function getUpdatedAt(): ?DateTimeImmutable;

    public function setCreatedAt(?DateTimeImmutable $createdAt): void;

    public function setUpdatedAt(?DateTimeImmutable $updatedAt): void;
}
