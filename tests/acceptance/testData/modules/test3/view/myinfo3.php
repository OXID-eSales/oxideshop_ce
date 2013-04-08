<?php
class myinfo3 extends myinfo3_parent {

    public function render()
    {
        $sTpl = parent::render();
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.' + info3');
        return $sTpl;
    }
}