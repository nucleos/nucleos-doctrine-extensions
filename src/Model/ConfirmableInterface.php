<?php

declare(strict_types=1);

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\Doctrine\Model;

interface ConfirmableInterface
{
    /**
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime;

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): void;

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void;

    /**
     * @return bool
     */
    public function isConfirmed(): bool;
}
