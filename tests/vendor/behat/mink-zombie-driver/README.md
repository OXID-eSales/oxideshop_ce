Mink Zombie.js Driver
=====================

- [![Build Status](https://secure.travis-ci.org/Behat/MinkZombieDriver.png?branch=master)](http://travis-ci.org/Behat/MinkZombieDriver)

Installation & Compatibility
----------------------------

You need a working installation of [NodeJS](http://nodejs.org/) and
[npm](https://npmjs.org/). Install the
[zombie.js](http://zombie.labnotes.org) library through npm:

``` bash
npm install -g zombie@0.12.15
```

There are some compatibility issues with newer versions of zombie.js.
Some are more or less PHP specific and kinda hard to resolve. If you
want to be 100% on the safe side, please use __version 0.12.15 or
lower__. 

Versions > 0.12.15 and < 2.0.0 are almost compatible
except for some edge cases.

__Versions >= 2.0.0-alpha* (or above) are neither compatible nor
supported at this
point of time due to significant changes of the zombie.js API.__ As soon
as version 2.0 is officially released, we will work on the driver's
compatibility.

Use [Composer](http://getcomposer.org/) to install all required PHP dependencies:

``` json
{
    "require": {
        "behat/mink":               "1.4.*",
        "behat/mink-zombie-driver": "1.0.*"
    }
}
```

``` bash
$> curl http://getcomposer.org/installer | php
$> php composer.phar install
```

Usage Example
-------------

``` php
<?php

use Behat\Mink\Mink,
    Behat\Mink\Session,
    Behat\Mink\Driver\ZombieDriver,
    Behat\Mink\Driver\NodeJS\Server\ZombieServer;

$host       = '127.0.0.1';
$port       = '8124';
$nodeBinary = '/usr/local/bin/node';

$mink = new Mink(array(
    'zombie' => new Session(new ZombieDriver(new ZombieServer(
        $host, $port, $nodeBinary
    ))),
));

$mink->setDefaultSessionName('zombie');

$session = $mink->getSession();
$session->visit('http://example.org');

$page = $session->getPage();
$elem = $page->find('css', 'h1');

echo $elem->getText();
```

Copyright
---------

Copyright (c) 2011-2012 Pascal Cremer <b00gizm@gmail.com>

Maintainers
-----------

* Pascal Cremer [b00gizm](http://github.com/b00gizm)
