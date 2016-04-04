OXID eShop
==========

[![Build Status](https://travis-ci.org/OXID-eSales/oxideshop_ce.svg?branch=master)](https://travis-ci.org/OXID-eSales/oxideshop_ce)

This repository contains the sources of OXID eShop Community Edition.

###About OXID eShop:

OXID eShop is a flexible open source e-commerce software with a wide range of functionalities. 
Thanks to its modular, modern and state-of-the-art architecture, it can be modified, expanded 
and customized to individual requirements with the greatest of ease. 

OXID eShop is just e-commerce software for agencies with deadlines :-)

![Image alt](frontend.png)

### Installation

Please note: if you don't know what the following is about, please download the OXID eShop package from this place: https://www.oxid-esales.com/en/community/download-oxid-eshop.html and follow the [installation instruction] (https://www.oxid-esales.com/en/support-services/documentation-and-help/oxid-eshop/installation/oxid-eshop-new-installation/server-and-system-requirements.html "OXID eShop installation instruction").

When checking out this repository or downloading the zip file from this place, composer is required for setting up OXID eShop.

1. make sure [composer] (https://getcomposer.org/) is installed on your system
2. `$ git clone https://github.com/OXID-eSales/oxideshop_ce.git`
3. `$ cd oxideshop_ce/source`
4. `$ composer install --no-dev`
5. `$ cp config.inc.php.dist config.inc.php`


### Useful links

* Project home page - http://www.oxid-esales.com
* Wiki - http://www.oxidforge.org
* Bug tracker - https://bugs.oxid-esales.com
* VM and SDK - https://github.com/OXID-eSales/oxvm_eshop
