<?php

namespace Behat\SahiClient\Accessor;

use Behat\SahiClient\Exception;
use Behat\SahiClient\Connection;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Heading Element Accessor (h1, h2, h3, ...).
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class HeadingAccessor extends AbstractDomAccessor
{
    /**
     * Heading level
     *
     * @var     integer
     */
    private     $level = 1;

    /**
     * Initialize Heading accessor.
     *
     * @param   integer     $level      heading level (1 for H1, 2 for H2 etc.)
     * @param   string      $id         element identifier (if null - "0" will be used)
     * @param   array       $relations  relations array array('near' => accessor, 'under' => accessor)
     * @param   Connection  $con        Sahi connection
     */
    public function __construct($level, $id, array $relations, Connection $con)
    {
        parent::__construct($id, $relations, $con);

        if (null !== $level) {
            $this->level = $level;
        }
    }

    /**
     * Return heading level.
     *
     * @return  integer
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'heading' . $this->getLevel();
    }
}
