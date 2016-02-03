<?php
class myinfo7 extends myinfo7_parent {

    public function render()
    {
        $sTpl = parent::render();
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.' + info7');
        return $sTpl;
    }
}