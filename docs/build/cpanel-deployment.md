# Connect GitHub to cPanel

The [deployment manifest](../../.cpanel.yml) installs the Savannah Crest theme and
plugin into the WordPress site in your cPanel account's `public_html` directory.

## Connect once

1. Open **cPanel → Git Version Control → Create** and enable **Clone a Repository**.
2. Paste `https://github.com/Allan-Gakuru/savannah-crest-safaris.git` as the clone URL.
3. Set **Repository Path** to `repositories/savannah-crest-safaris` under your account
   home. Name it **Savannah Crest Safaris** and click **Create**.
   [cPanel Git Version Control](https://docs.cpanel.net/cpanel/files/git-version-control/)

## Deploy

1. Back up the site before the first deployment. Open **Manage → Pull or Deploy** and
   confirm the checked-out branch is **main**.
2. Click **Update from Remote**, check the intended revision, then click **Deploy HEAD Commit**.
   [cPanel deployment guide](https://docs.cpanel.net/knowledge-base/web-services/guide-to-git-set-up-deployment/)
3. Check the deploy log for success. On the first deploy, follow the
   [hosted WordPress setup](../../README.md#hosted-setup) to activate the theme and
   plugin. Check the homepage, `/safaris/`, and the Safari draft editor.

Repeat **Update from Remote → Deploy HEAD Commit** for later releases. The script
overlays the two project directories. If a release removes old project files,
include the cleanup in that release's instructions.
