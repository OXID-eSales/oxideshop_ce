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
 * By Class Name Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ByClassNameAccessor extends AbstractRelationalAccessor
{
    /**
     * Tag class
     *
     * @var     string
     */
    protected   $class;
    /**
     * Tag name
     *
     * @var     string
     */
    protected   $tag;

    /**
     * Initialize Accessor.
     *
     * @param   string      $class      tag class name
     * @param   string      $tag        tag name
     * @param   array       $relations  relations
     * @param   Connection  $con        Sahi connection
     */
    public function __construct($class, $tag, array $relations, Connection $con)
    {
        parent::__construct($con);

        foreach ($relations as $relation => $accessor) {
            $this->$relation($accessor);
        }

        $this->class = $class;
        $this->tag   = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessor()
    {
        $arguments   = array();
        $arguments[] = '"' . $this->class . '"';
        $arguments[] = '"' . $this->tag . '"';
        if ($this->hasRelations()) {
            $arguments[] = $this->getRelationArgumentsString();
        }

        return sprintf('_sahi._byClassName(%s)', implode(', ', $arguments));
    }
}
