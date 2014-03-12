<?php


/**
 * Admin state manager.
 * Returns template, that arranges two other templates ("state_list.tpl"
 * and "state_main.tpl") to frame.
 * @package admin
 */
class state extends oxAdminView
{
    /**
     * Current class template name.
     * @var string
     */
    protected $_sThisTemplate = 'state.tpl';
}