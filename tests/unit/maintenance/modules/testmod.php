<?php

if (class_exists('testmod')) {
    throw new Exception("redefinition occured");
}

// this is the place which calls autoload again (for testmod_parent)
class testmod extends testmod_parent
{

    public function sayHi()
    {
        return "Hi!";
    }
}
