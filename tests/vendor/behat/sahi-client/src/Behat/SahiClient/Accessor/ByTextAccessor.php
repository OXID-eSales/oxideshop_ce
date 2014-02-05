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
 * By Text Accessor.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class ByTextAccessor extends AbstractAccessor
{
    /**
     * Tag text
     *
     * @var     string
     */
    protected   $text;
    /**
     * Tag name
     *
     * @var     string
     */
    protected   $tag;

    /**
     * Initialize Accessor.
     *
     * @param   string      $text   tag text
     * @param   string      $tag    tag name
     * @param   Connection  $con    Sahi connection
     */
    public function __construct($text, $tag, Connection $con)
    {
        parent::__construct($con);

        $this->text = $text;
        $this->tag  = $tag;
    }

    /**
     * {@inheritdoc}
     */
    public function getAccessor()
    {
        return sprintf('_sahi._byText("%s", "%s")', $this->text, $this->tag);
    }
}
