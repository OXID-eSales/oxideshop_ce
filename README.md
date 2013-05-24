OXID eShop
==========

This repository contains the sources of OXID eShop Community Edition.

###About OXID eShop:

OXID eShop is a flexible open source e-commerce software with a wide range of functionalities. 
Thanks to its modular, modern and state-of-the-art architecture, it can be modified, expanded 
and customized to individual requirements with the greatest of ease. 

OXID eShop is just e-commerce software for agencies with deadlines :-)

![Image alt](frontend.png)


###Getting involved

OXID eShop is available under two different licenses, GPLv3 and a commercial license.

That's why, before contributing for the first time, you must send the filled form "OXID eSales Contributor Agreement" 
(http://wiki.oxidforge.org/File:Oxid_esales_ca_v151.pdf) to community@oxid-esales.com. You can find more 
information about it on the FAQ page OXID Contribution and Contributor Agreement FAQ
http://wiki.oxidforge.org/OXID_Contribution_and_Contributor_Agreement_FAQ

Process:<br>
First off, you have to fork the repository OXID-eSales/oxideshop_ce to your list of repositories. 

You will find three branches now in youraccount/oxideshop_ce:

* <b>b-dev-ce</b>, presently our main branch on this repo, is the so called <b>feature branch</b>: All new features will be developed here as well as bug fixes for the next major version.
* <b>b-5.0-ce</b> is the <b>maintenance branch</b> for the present major version. Only bug fixes here, no new features, no DB changes, no template changes if possible.
* <b>b-4.6-ce</b> appears as the so called <b>legacy branch</b>: fixes for bugs with higher priority only.

In general, contributions can be taken over for all branches. Bug fixes committed to only one branch will be pushed to the other branches manually. Of course you can also consider to commit e.g. bug fixes to more than one branche.

<b>Best practice</b>: 
* please leave the the branch names as they are
* if you want to fix a bug or develop a new feature, define an own branch in your repository off of one of the three branches above. Name it e.g. feature/foo or bug/bugname for better tracability
* change whatever you want and push it back to the original branch (b-dev-ce). 

For more information about this, please see:<br>
http://codeinthehole.com/writing/pull-requests-and-other-good-practices-for-teams-using-github/

Now you'll be asked for signing an OXID Contributor Agreement (this has to be done once). After that we can start checking your code. In every case, whether or not we could take your over your contribution, you'll be informed.

![Image alt](git_contributor-activity.png)

You will find technical help with Git and GitHub on this place:<br>
https://help.github.com/

Code quality:<br>
We are still working on providing OXID specific code guidlines and tutorials to you. Until this, we propose to refer to 
http://framework.zend.com/manual/1.12/en/coding-standard.coding-style.html
Also, please provide PHP Unit tests for your code.

When sending your pull request, please provide a clear, meaningful and detailed information about what your code is about and what it will do, best including a screen shot if possible.
If you want to discuss your contribution and your code before committing it, please go to the dev-general mailing list: http://lists.oxidforge.org/mailman/listinfo/dev-general.

Useful links:<br>
Project home page - http://www.oxid-esales.com<br>
Wiki - http://www.oxidforge.org<br>
Bug tracker - https://bugs.oxid-esales.com
