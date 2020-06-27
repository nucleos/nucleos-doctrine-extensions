<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Nucleos\Doctrine\Model\Traits;

use DateTime;

trait ConfirmableTrait
{
    /**
     * @var DateTime|null
     */
    protected $confirmedAt;

    public function getConfirmedAt(): ?DateTime
    {
        return $this->confirmedAt;
    }

    public function setConfirmedAt(?DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    public function setConfirmed(bool $confirmed): void
    {
        if ($confirmed) {
            $this->setConfirmedAt(new DateTime());
        } else {
            $this->setConfirmedAt(null);
        }
    }

    public function isConfirmed(): bool
    {
        return null !== $this->confirmedAt;
    }
}
