run the migrations:

    $ cd protected
    $ ./yiic migrate

JS libs are converted to AMD format: 
http://requirejs.org/docs/whyamd.html

For converting to AMD module:
https://github.com/jrburke/requirejs/wiki/Updating-existing-libraries

Git :
Create Empty Git Repository in remote machine
mkdir /home/truglobal/website.git
cd /home/truglobal/website.git
git init --bare

Create post receive hook to automatically update the webserver document root:
vi /home/truglobal/website.git/hooks/post-receive

#!/bin/sh
GIT_WORK_TREE=/var/www/website git checkout -f

chmod +x /home/truglobal/website.git/hooks/post-receive

Local git:
git remote add origin truglobal@119.82.99.44:/home/truglobal/website.git
git push origin master

Ref: http://toroid.org/ams/git-website-howto
