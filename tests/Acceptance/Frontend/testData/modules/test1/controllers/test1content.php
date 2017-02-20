<?php
class test1Content extends test1Content_parent
{
    public function render()
    {
        $sTpl = parent::render();
        $this->_oContent->oxcontents__oxtitle->setValue($this->_oContent->oxcontents__oxtitle.' + info1');
        return $sTpl;
    }
}
