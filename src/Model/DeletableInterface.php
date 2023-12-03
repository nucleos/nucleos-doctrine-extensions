<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Model;

use DateTime;

interface DeletableInterface
{
    public function getDeletedAt(): ?DateTime;

    public function setDeletedAt(?DateTime $deletedAt): void;

    public function setDeleted(bool $deleted): void;

    public function isDeleted(): bool;
}
