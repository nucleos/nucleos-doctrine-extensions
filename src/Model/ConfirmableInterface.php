<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model;

interface ConfirmableInterface
{
    /**
     * Get confirmedAt.
     *
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime;

    /**
     * Set confirmedAt.
     *
     * @param \DateTime|null $confirmedAt
     *
     * @return $this
     */
    public function setConfirmedAt(?\DateTime $confirmedAt);

    /**
     * Set confirmed.
     *
     * @param bool $confirmed
     *
     * @return $this
     */
    public function setConfirmed(bool $confirmed);

    /**
     * Get deleted.
     *
     * @return bool
     */
    public function isConfirmed(): bool;
}
