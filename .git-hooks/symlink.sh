#!/bin/sh
if [ ! -L .git/hooks ];
then
    echo ".git/hooks is not symlink"
    echo "copying .git/hooks to .git/old_hooks"
    mv .git/hooks .git/old_hooks

    echo "symlinking ../.git-hooks .git/hooks"
    ln -s ../.git-hooks .git/hooks
else
    echo ".git/hooks is already a symlink"
fi
