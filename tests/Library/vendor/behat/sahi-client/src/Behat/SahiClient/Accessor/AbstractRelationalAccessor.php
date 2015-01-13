<?php

namespace Behat\SahiClient\Accessor;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Abstract Relational Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
abstract class AbstractRelationalAccessor extends AbstractAccessor
{
    /**
     * DOM relations.
     *
     * @var     array
     */
    private     $relations = array();

    /**
     * Add _in DOM relation.
     *
     * @param   AbstractAccessor    $accessor   accessor for relation
     *
     * @return  AbstractRelationalAccessor
     */
    public function in(AbstractAccessor $accessor)
    {
        $this->relations[] = sprintf('_sahi._in(%s)', $accessor->getAccessor());

        return $this;
    }

    /**
     * Add _near DOM relation.
     *
     * @param   AbstractAccessor    $accessor   accessor for relation
     *
     * @return  AbstractRelationalAccessor
     */
    public function near(AbstractAccessor $accessor)
    {
        $this->relations[] = sprintf('_sahi._near(%s)', $accessor->getAccessor());

        return $this;
    }

    /**
     * Add _under DOM relation.
     *
     * @param   AbstractAccessor    $accessor   accessor for relation
     *
     * @return  AbstractRelationalAccessor
     */
    public function under(AbstractAccessor $accessor)
    {
        $this->relations[] = sprintf('_sahi._under(%s)', $accessor->getAccessor());

        return $this;
    }

    /**
     * Return true if accessor has relations.
     *
     * @return  boolean
     */
    public function hasRelations()
    {
        return 0 < count($this->relations);
    }

    /**
     * Return relations Sahi arguments.
     *
     * @return  string
     */
    protected function getRelationArgumentsString()
    {
        return implode(', ', $this->relations);
    }
}
