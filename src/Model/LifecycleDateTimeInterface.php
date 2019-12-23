<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Model;

use DateTime;

interface LifecycleDateTimeInterface
{
    public function getCreatedAt(): ?DateTime;

    public function getUpdatedAt(): ?DateTime;

    public function setCreatedAt(?DateTime $createdAt): void;

    public function setUpdatedAt(?DateTime $updatedAt): void;
}
