<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model;

interface PositionAwareInterface
{
    /**
     * Get position.
     *
     * @return int|null
     */
    public function getPosition(): ? int;

    /**
     * Set position.
     *
     * @param int|null $position
     *
     * @return $this
     */
    public function setPosition(? int $position);

    /**
     * Get list of position fields.
     *
     * @return string[]
     */
    public function getPositionGroup() : array;
}
