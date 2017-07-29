<?php

/**
 * Class myinfo6
 */
class myinfo6 extends myinfo6_parent
{
    /**
     * @return mixed
     */
    public function render()
    {
        $sTpl = parent::render();
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.' + info6');
        return $sTpl;
    }
}
