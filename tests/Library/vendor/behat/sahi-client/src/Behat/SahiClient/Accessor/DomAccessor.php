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
 * DOM Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class DomAccessor extends AbstractAccessor
{
    /**
     * Element ID
     *
     * @var     string
     */
    protected   $id;

    /**
     * Initialize Accessor.
     *
     * @param   string      $string DOM expression
     * @param   Connection  $con    Sahi connection
     */
    public function __construct($string, Connection $con)
    {
        parent::__construct($con);

        $this->id = $string;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessor()
    {
        return sprintf('_sahi._accessor("%s")', str_replace('"', '\"', $this->id));
    }
}
