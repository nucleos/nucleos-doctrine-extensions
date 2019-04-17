<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Model\Traits;

use DateTime;

trait ConfirmableTrait
{
    /**
     * @var DateTime|null
     */
    protected $confirmedAt;

    /**
     * @return DateTime|null
     */
    public function getConfirmedAt(): ?DateTime
    {
        return $this->confirmedAt;
    }

    /**
     * @param DateTime|null $confirmedAt
     */
    public function setConfirmedAt(?DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        if ($confirmed) {
            $this->setConfirmedAt(new DateTime());
        } else {
            $this->setConfirmedAt(null);
        }
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return null !== $this->confirmedAt;
    }
}
