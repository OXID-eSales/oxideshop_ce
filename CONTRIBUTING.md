Getting involved
================

OXID eShop is available under two different licenses, GPLv3 and a commercial license.

That's why, before contributing for the first time, you must <a href="https://gist.github.com/OXID-Admin/6df6ed126d074a54507d">sign the Contributor License Agreement</a>.
You can find more information about it on the FAQ page OXID Contribution and Contributor Agreement FAQ:
http://oxidforge.org/en/oxid-contribution-and-contributor-agreement-faq

Process:<br>
First off, you have to fork the repository OXID-eSales/oxideshop_ce to your list of repositories.

You will find three branches now in youraccount/oxideshop_ce:

* <b>b-dev-ce</b>, presently our main branch on this repo, is the so called <b>feature branch</b>: All new features will be developed here as well as bug fixes for the next major version.
* <b>b-5.2-ce</b> is the <b>maintenance branch</b> for the present major version. Only bug fixes here, no new features, no DB changes, no template changes if possible.
* <b>b-5.1-ce</b> appears as the so called <b>legacy branch</b>: fixes for bugs with higher priority only.

In general, contributions can be taken over for all branches. Bug fixes committed to only one branch will be pushed to the other branches manually. Of course you can also consider to commit e.g. bug fixes to more than one branch.

<b>Best practice</b>:
* please leave the the branch names as they are
* if you want to fix a bug or develop a new feature, define an own branch in your repository off of one of the three branches above. Name it e.g. feature/foo or bug/bugname for better tracability
* change whatever you want and push it back to the original branch (b-dev-ce).

For more information about this, please see:<br>
http://codeinthehole.com/writing/pull-requests-and-other-good-practices-for-teams-using-github/

Now you'll be asked for signing an OXID Contributor Agreement (this has to be done once). After that we can start checking your code. In every case, whether or not we could take over your contribution, you'll be informed.

![Image alt](git_contributor-activity.png)

You will find technical help with Git and GitHub on this place:<br>
https://help.github.com/

Code quality:<br>
Please find a collection of helpful development tools as well as a link to the OXID specific Coding style guidelines at http://oxidforge.org/en/coding-standards.html.
We also kindly request to PHP Unit tests for your code.

When sending your pull request, please provide a clear, meaningful and detailed information what your code is about and what it will do, best including a screen shot if possible.
If you want to discuss your contribution and your code before committing it, please go to the dev-general mailing list: http://lists.oxidforge.org/mailman/listinfo/dev-general.

Cloning:<br>

To reduce the size of the repository when cloning you can use a so called "shallow clone". 
With it, the history will be truncated and can save more than 90% of the disk space and traffic in comparison to a full repository clone.

Here is an example of how to use a shallow clone:

`git clone --depth 1 https://github.com/OXID-eSales/oxideshop_ce.git`