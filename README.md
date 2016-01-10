GitLab AutoDeploy
==============

Author: **Afaan Bilal ([@AfaanBilal](https://github.com/AfaanBilal))**   
Author URL: **[Google+][1]**

##### Project Page: [afaan.ml/gitlab-autodeploy](https://afaan.ml/gitlab-autodeploy)

## Introduction
**GitLab AutoDeploy** is a PHP script to easily deploy web app repositories on `git push` or 
any other hook. It is based on a [similar script][3] [I][1] wrote for GitHub.

## Features
- No SSH access required.
- No shell access required.
- Fully compatible with shared servers.
- Both public, internal and private repos are supported.

## Setup
1. Download [gitlab-autodeploy.php](gitlab-autodeploy.php).
2. Update the GitLab repo ID and your private token by replacing `[REPO_ID]` and `[PRIVATE_TOKEN]`.
3. Set the deploy directory by replacing `[DEPLOY_DIR]` relative to the script.
4. Set your timezone.
5. Upload the script to your server.
6. On GitLab, [add a WebHook][2] to your repo for a `push` event (or any other) and
set it to the uploaded script.
7. You're done!

Now, whenever you `git push` to your gitlab repo, it will be automatically deployed
to your web server!

## Adding a WebHook
*You must have administrative access to the repo for adding WebHooks*

1. Go to your GitLab repo &raquo; `Settings` &raquo; `Web Hooks`.  
2. Enter the URL of the gitlab-autodeploy.php script in the `URL` field.  
3. If your server does not use SSL, then uncheck `Enable SSL Verification`.  
4. Leave everything else as is, click `Add Web Hook`.  
5. You're done!  

## Contributing
All contributions are welcome. Please create an issue first for any feature request
or bug. Then fork the repository, create a branch and make any changes to fix the bug 
or add the feature and create a pull request. That's it!
Thanks!

## License
**GitLab AutoDeploy** is released under the MIT License.
Check out the full license [here](LICENSE).

[1]: https://google.com/+AfaanBilal "Afaan Bilal"
[2]: #adding-a-webhook "Adding a WebHook"
[3]: https://github.com/AfaanBilal/github-autodeploy
