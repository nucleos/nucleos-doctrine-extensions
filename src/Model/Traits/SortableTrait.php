<?php

/*
 * (c) Christian Gripp <mail@core23.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Core23\DoctrineExtensions\Model\Traits;

trait SortableTrait
{
    /**
     * @var int|null
     */
    protected $position = 0;

    /**
     * @param int|null $position
     *
     * @return $this
     */
    public function setPosition(? int $position)
    {
        $this->position = $position;

        return $this;
    }

    /**
     * @return int|null
     */
    public function getPosition() : int
    {
        return $this->position;
    }

    /**
     * @return array
     */
    public function getPositionGroup() : array
    {
        return array();
    }
}
