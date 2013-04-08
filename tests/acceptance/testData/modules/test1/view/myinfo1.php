<?php
class myinfo1 extends myinfo1_parent {

    public function render()
    {
        $sTpl = parent::render();
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.' + info1');
        return $sTpl;
    }
}