<?php

/**
 * Class myinfo2
 */
class myinfo2 extends myinfo2_parent
{
    /**
     * @return mixed
     */
    public function render()
    {
        $sTpl = parent::render();
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.' + info2');
        return $sTpl;
    }
}
