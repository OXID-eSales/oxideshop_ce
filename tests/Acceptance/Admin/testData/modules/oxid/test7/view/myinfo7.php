<?php

/**
 * Class myinfo7
 */
class myinfo7 extends myinfo7_parent
{
    /**
     * @return mixed
     */
    public function render()
    {
        $sTpl = parent::render();
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.' + info7');
        return $sTpl;
    }
}
