---
layout: notepad
title: Git
---

I find myself repeatedly Googling for some basic Git commands:


#### Reset your local repository to the last committed state (leaves untracked files alone):

    git reset --hard

<br>

#### Remove the last remote commit:

    git push -f origin HEAD^:master

<br>

#### Delete a remote tag:

    git tag -d <tag_name>
    git push origin :refs/tags/<tag_name>

<br>

#### Merge a remote repository into a local copy:

    git pull git://github.com/<user_name>/<repo_name> <remote_branch_name>:<local_branch_name>

<br>

#### Track and merge a remote repository into a local copy:

    git remote add <new_remote_name> git://github.com/<user_name>/<repo_name>
    git fetch <new_remote_name>
    git checkout --track -b <remote_branch_name> <new_remote_name>/<remote_branch_name>
    git checkout <local_branch_name>
    git merge <new_remote_name>/<remote_branch_name>

<br>

#### Save your uncommitted changes in a named stash:

    git stash save "<stash_name>"

#### Apply your saved named stash (using regex):

    git stash apply stash^{/<stash_name_matcher>}

#### Remove a saved stash:

    git stash drop stash@{<stash_index>}
