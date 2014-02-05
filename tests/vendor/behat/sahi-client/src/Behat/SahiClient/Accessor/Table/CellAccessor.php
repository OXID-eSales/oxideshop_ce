<?php

namespace Behat\SahiClient\Accessor\Table;

use Behat\SahiClient\Connection;
use Behat\SahiClient\Accessor\AbstractDomAccessor;
use Behat\SahiClient\Exception;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Cell Element Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class CellAccessor extends AbstractDomAccessor
{
    private     $table;
    private     $rowText;
    private     $colText;

    /**
     * Initialize Accessor.
     *
     * @param   string|array    $id         simple element identifier or array of (Table, rowText, colText)
     * @param   array           $relations  relations array array('near' => accessor, 'under' => accessor)
     * @param   Connection      $con        Sahi connection
     */
    public function __construct($id, array $relations, Connection $con)
    {
        if (is_array($id)) {
            if (!($id[0] instanceof TableAccessor) || !isset($id[0]) || !isset($id[1])) {
                throw new \InvalidArgumentException(
                    'Cell table identificator must have type: array(TableAccessor, "rowText", "colText")'
                );
            }

            if (count($relations)) {
                throw new \InvalidArgumentException(
                    'Can not use relations in cell accessor, that depends on table accessor'
                );
            }

            $this->table    = $id[0];
            $this->rowText  = $id[1];
            $this->colText  = $id[2];

            parent::__construct(null, $relations, $con);
        } else {
            parent::__construct($id, $relations, $con);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessor()
    {
        if (null !== $this->table) {
            $arguments   = array();
            $arguments[] = $this->table->getAccessor();
            $arguments[] = '"' . str_replace('"', '\"', $this->rowText) . '"';
            $arguments[] = '"' . str_replace('"', '\"', $this->colText) . '"';

            if ($this->hasRelations()) {
                $arguments[] = $this->getRelationArgumentsString();
            }

            return sprintf('_sahi._%s(%s)', $this->getType(), implode(', ', $arguments));
        }

        return sprintf('_sahi._%s(%s)', $this->getType(), $this->getArgumentsString());
    }

    /**
     * {@inheritdoc}
     */
    public function getType()
    {
        return 'cell';
    }
}
