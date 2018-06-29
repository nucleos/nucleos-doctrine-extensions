<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model\Traits;

trait ConfirmableTrait
{
    /**
     * @var \DateTime|null
     */
    protected $confirmedAt;

    /**
     * Get confirmedAt.
     *
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime
    {
        return $this->confirmedAt;
    }

    /**
     * Set confirmedAt.
     *
     * @param \DateTime|null $confirmedAt
     *
     * @return self
     */
    public function setConfirmedAt(?\DateTime $confirmedAt)
    {
        $this->confirmedAt = $confirmedAt;

        return $this;
    }

    /**
     * Set confirmed.
     *
     * @param bool $confirmed
     *
     * @return self
     */
    public function setConfirmed(bool $confirmed)
    {
        if ($confirmed) {
            $this->setConfirmedAt(new \DateTime());
        } else {
            $this->setConfirmedAt(null);
        }

        return $this;
    }

    /**
     * Get confirmed.
     *
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return null !== $this->confirmedAt;
    }
}
