<?php

namespace Behat\SahiClient\Accessor;

use Behat\SahiClient\Connection;
use Behat\SahiClient\Exception;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract Element Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class AbstractDomAccessor extends AbstractRelationalAccessor
{
    /**
     * Element identifier
     *
     * @var     string
     */
    protected   $id;

    /**
     * Initialize Accessor.
     *
     * @param   string      $id         element identifier (if null - "0" will be used)
     * @param   array       $relations  relations array array('near' => accessor, 'under' => accessor)
     * @param   Connection  $con        Sahi connection
     */
    public function __construct($id, array $relations, Connection $con)
    {
        parent::__construct($con);

        foreach ($relations as $relation => $accessor) {
            $this->$relation($accessor);
        }

        $this->id = $id;
    }

    /**
     * Return DOM element type.
     *
     * @return  string
     */
    abstract public function getType();

    /**
     * {@inheritdoc}
     */
    public function getAccessor()
    {
        return sprintf('_sahi._%s(%s)', $this->getType(), $this->getArgumentsString());
    }

    /**
     * Return comma separated Sahi DOM arguments.
     *
     * @return  string
     */
    protected function getArgumentsString()
    {
        $arguments = array($this->getIdentifierArgumentString());

        if ($this->hasRelations()) {
            $arguments[] = $this->getRelationArgumentsString();
        }

        return implode(', ', $arguments);
    }

    /**
     * Convert identificator to JavaScript id instruction.
     *
     * @param   mixed   $id element identificator
     *
     * @return  string              JavaScript id instruction
     */
    private function getIdentifierArgumentString()
    {
        if (null === $this->id) {
            return '0';
        }

        if (is_float($this->id) || is_int($this->id)) {
            return $this->id;
        }

        return '"' . $this->id . '"';
    }
}
