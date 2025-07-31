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

interface Confirmable
{
    public function getConfirmedAt(): ?DateTimeImmutable;

    public function setConfirmedAt(?DateTimeImmutable $confirmedAt): void;

    public function setConfirmed(bool $confirmed): void;

    public function isConfirmed(): bool;
}
