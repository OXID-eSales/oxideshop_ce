OXID eShop
==========

[![Build Status](https://travis-ci.org/OXID-eSales/oxideshop_ce.svg?branch=b-5.3-ce)](https://travis-ci.org/OXID-eSales/oxideshop_ce)

This repository contains the sources of OXID eShop Community Edition.

###About OXID eShop:

OXID eShop is a flexible open source e-commerce software with a wide range of functionalities. 
Thanks to its modular, modern and state-of-the-art architecture, it can be modified, expanded 
and customized to individual requirements with the greatest of ease. 

OXID eShop is just e-commerce software for agencies with deadlines :-)

![Image alt](frontend-flow.png)


### Installation

Please note: if you don't know what the following is about, please download the OXID eShop package from this place: https://www.oxid-esales.com/en/community/download-oxid-eshop.html and follow the [installation instruction] (https://www.oxid-esales.com/en/support-services/documentation-and-help/oxid-eshop/installation/oxid-eshop-new-installation/server-and-system-requirements.html "OXID eShop installation instruction").

When checking out this repository or downloading the zip file from this place, Flow theme has to be installed.

1. `$ git clone https://github.com/OXID-eSales/oxideshop_ce.git --branch b-5.3-ce`
1. `wget "https://raw.githubusercontent.com/OXID-eSales/oxideshop_demodata_ce/b-5.3/src/demodata.sql" -P oxideshop_ce/source/setup/sql/`
1. `$ cd oxideshop_ce/source/application/views`
1. `$ git clone https://github.com/OXID-eSales/flow_theme.git flow --branch b-1.0`
1. `$ cp -R flow/out/flow ../../out/`


Useful links:<br>
Project home page - http://www.oxidforge.org<br>
Vendor home page - http://www.oxid-esales.com<br>
Bug tracker - https://bugs.oxid-esales.com
VM and SDK - https://github.com/OXID-eSales/oxvm_eshop