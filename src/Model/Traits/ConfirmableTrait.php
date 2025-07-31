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

trait ConfirmableTrait
{
    protected ?DateTimeImmutable $confirmedAt = null;

    public function getConfirmedAt(): ?DateTimeImmutable
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?DateTimeImmutable $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    public function setConfirmed(bool $confirmed): void
    {
        if ($confirmed) {
            $this->setConfirmedAt(new DateTimeImmutable());
        } else {
            $this->setConfirmedAt(null);
        }
    }

    public function isConfirmed(): bool
    {
        return null !== $this->confirmedAt;
    }
}
