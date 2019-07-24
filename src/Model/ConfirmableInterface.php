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
    public function getConfirmedAt(): ?\DateTime;

    public function setConfirmedAt(?\DateTime $confirmedAt): void;

    public function setConfirmed(bool $confirmed): void;

    public function isConfirmed(): bool;
}
