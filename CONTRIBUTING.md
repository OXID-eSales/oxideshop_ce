# Getting involved

OXID eShop is available under two different licenses, OXID Community License and a commercial license.

That's why, before contributing for the first time, you must <a href="https://gist.github.com/oxid-devops/6d1ae6df18ab7d54ab122762b76c48a5">sign the Contributor License Agreement</a>.
You can find more information about it on the FAQ page OXID Contribution and Contributor Agreement FAQ:
https://docs.oxid-esales.com/developer/en/latest/development/modules_components_themes/contribution.html

## Types of contributions

There can be different reasons for contributions:

* Bug fixes and small tweaks/improvements
  - For this kind of contributions - simple pull request is enough. Be sure to select correct branch for the fix. Read more about current branching strategy in next sections.
  - Make sure you check the github workflow results (if such configured) and **add tests for your fixed case**.
  - Please check the [bugtracker](https://bugs.oxid-esales.com/) if your bug is reported and mension the bug number(-s) in the pull request description.
* Feature
  - New Features are NOT analyzed and merged via Pull requests, before:
    - [quality requirements](https://docs.oxid-esales.com/developer/en/latest/development/modules_components_themes/quality.html#code-quality-requirements) achieved 
    - the feature is coordinated with OXID
  - For [partners](https://www.oxid-esales.com/en/partners/become-a-partner/) and employees, Before implementing the feature:
    - Check our [UserVoice](https://feedback.oxid-esales.com/) and see if there is anyone interested in your feature.
    - By using this gateway, you can offer new features and check if other people are interested in it. 
    - Most likely we will put your idea in our backlog if the feature is requested and voted for by a lot of people.
  - Consider, maybe implementing the feature as a separate module would be a good idea.
  - If your idea is popular, and you are willing to introduce the feature to shop core:
    - contact us first, and discuss the way you are planning to implement it, so there will be higher chances to get it merged
  - For others than partners, use [the form](https://www.oxid-esales.com/en/contact-us/) to contact us.
  - For not partners, to request new feature without implementing it yourself, please use our [bugtracker](https://bugs.oxid-esales.com/).

## Pull request process

First off, you have to fork the repository OXID-eSales/oxideshop_ce to your list of repositories.

### Compilation branch naming introduction

To find the correct branch to contribute to, read this introduction to our branch naming strategy:

First one caveat: You really need to understand the difference between OXID *components* and
the eShop *compilation*. The compilation is a bundle that incorporates the shop core *and*
several essential modules. But although each *component* has it's own versioning (and follows
strict semantic versioning), it's the versioning of the *compilation*, that determines the
naming of branches that exist in the OXID eShop *components*.

The versioning of the *components* themselves may differ
from the *compilation* (which as a bundle can't follow strict semantic versioning).
So although you are contributing to a *component*, the numbering of the branches follows the
versioning of the *compilation*. The reason for this is obvious: We want to see, which changes
go to the next release and releases means that we publish a new *compilation* - although we
might have several new versions of the *component* in between.

Please take a look at our [release log](https://docs.oxid-esales.com/eshop/en/latest/releases/index.html) to see OXID eShop versioning in practice. 

Among many, the following types of branches are relevant to you as a contributor in the *\<github_username\>/oxideshop_ce* repository:

* The **next major version** with a name like **b-{next major}.x.x** All new features including compatibility breaking changes will be developed here as well as bug fixes.
* The **next minor version** branch with a name like **b-{current major}.{next minor}.x** for the currently maintained major version. Only Backwards compatible changes as well as new compatible features are possible.
* The **current patch** branch with a name like **b-{current major}.{current minor}.x**: bug fixes only. (will only be created if needed)
* The **previous patch** branch with a name like **b-{current major}.{previous minor}.x**: critical bug fixes only. (will only be created if needed)

In general, contributions can be taken over for all branches. Bug fixes committed to specific branch, will be merged up to higher branches manually. Of course, you can also consider making pull requests to several branches, if the original code differs - it will help us merging everything up together.

### Finding the best branch for a pull request

* In case you have found a security issue - do not create a pull request please, but instead follow the security procedures as outlined [here](https://docs.oxid-esales.com/en/security/security.html) - thank you!

* For any database involved changes, or anything that breaks the shops backwards compatibility - **next major version** branch is the best spot for you.
* Small tweaks and improvements that are not breaking the compatibility goes to the **next minor version** branch.
* If your change fixes the registered bug, and is not breaking backwards compatibility - the **current patch version** is the best spot, as it will be released with the next patch.

## Development installation

We recommend using our docker based [SDK](https://github.com/OXID-eSales/docker-eshop-sdk) and [recipes](https://github.com/OXID-eSales/docker-eshop-sdk-recipes) that use the SDK as a base, and installs the development version of the shop for you.

## Best practice

* install the shop by using our SDK and recipe
* register your repository as a git remote in place of our default one
* create your own branch from the one you want the improvement to be merged to. Name it e.g. b-6.4.x-feature_foo or b-6.5.x-bug_bugname for better traceability
* change whatever you want and push it to your forked repository
* when changes are pushed, create a Pull request on github for your branch
  - When sending your pull request, please provide a clear, meaningful and detailed information what your code is about and what it will do, best including a screenshot if possible.
* additional changes to pull request can be done by making additional commits on your branch

For more information about this, please see:<br>
http://codeinthehole.com/writing/pull-requests-and-other-good-practices-for-teams-using-github/

Now you'll be asked for signing an OXID Contributor Agreement (this has to be done once). After that we can start checking your code. In every case, whether or not we could take over your contribution, you'll be informed.

![Image alt](git_contributor-activity.png)

You will find technical help with Git and GitHub on this place: [https://help.github.com/](https://help.github.com/)

## Code quality

Please find an overview of helpful development tools, coding style and code quality guidelines on the corresponding [documentation page](https://docs.oxid-esales.com/developer/en/latest/development/modules_components_themes/quality.html).
