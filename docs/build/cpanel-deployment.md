# Connect GitHub to cPanel

Use cPanel's Git Version Control to pull this repository's `main` branch and deploy
the revision you choose. The root [deployment manifest](../../.cpanel.yml) runs the
project deploy script. The script extracts the two committed project directories
into your existing WordPress installation.
[cPanel deployment guide](https://docs.cpanel.net/knowledge-base/web-services/guide-to-git-set-up-deployment/)

## Set up the connection

1. In **cPanel → Domains**, find Savannah Crest's Document Root. In **File Manager**,
   open the actual WordPress directory and check for `wp-load.php`, `wp-includes`, and
   `wp-content`. If WordPress is installed in a subdirectory, use that subdirectory.
   Record the absolute path, for example `/home/CPANEL_USER/public_html`.
   [cPanel Domains](https://docs.cpanel.net/cpanel/domains/domains/)
2. In **File Manager**, go to your account's **Home Directory** and enable
   **Settings → Show Hidden Files (dotfiles)**. Create
   `.savannah-crest-deploy-root` there. Put only the absolute WordPress directory
   path on one line, with no quotes. The file belongs beside `public_html` or
   the other hosted site directories in your home directory.
3. In **Files → Git Version Control**, click **Create** and enable **Clone a
   Repository**. For a public repo, use
   `https://github.com/Allan-Gakuru/savannah-crest-safaris.git`. For a private repo,
   use the SSH setup below.
4. Set **Repository Path** to `repositories/savannah-crest-safaris` under your account
   home and **Repository Name** to `Savannah Crest Safaris`. Keep the checkout
   outside the site's web directory. Click **Create** and wait for the clone.
   [cPanel Git Version Control](https://docs.cpanel.net/cpanel/files/git-version-control/)

### Private GitHub repository

Follow cPanel's SSH setup for private repositories using a dedicated cPanel-generated
SSH key. Add its **public** key under **GitHub → Repository Settings → Deploy keys**,
leaving **Allow write access** unchecked. The SSH clone URL is
`git@github.com:Allan-Gakuru/savannah-crest-safaris.git` when the key is configured for
`github.com`. If you configure a distinct SSH host alias for the key, use that alias
in the clone URL. Verify GitHub's SSH host key when cPanel prompts.
[cPanel private repository setup](https://docs.cpanel.net/knowledge-base/web-services/guide-to-git-set-up-access-to-private-repositories/)

## Deploy `main`

1. Back up the site's files and database before the first deployment. Open
   **Git Version Control → Manage** and confirm the checked-out branch is **main**.
2. Open **Pull or Deploy** and click **Update from Remote**. Check that **HEAD Commit**
   shows the intended revision. If Terminal is available, you can check the paths
   before the first deploy:

   ~~~bash
   cd "$HOME/repositories/savannah-crest-safaris"
   /bin/bash ./tools/deploy-cpanel.sh "$HOME/.savannah-crest-deploy-root" --check
   ~~~

3. Click **Deploy HEAD Commit** and confirm the deploy log reports success. It installs
   or updates `wp-content/themes/savannah-crest` and `wp-content/plugins/scs-core`.
   WordPress activation and configuration happen in WP Admin. The deploy overlays
   existing project files, so a release that retires old files needs a scoped cleanup
   after review. Backups and a tested revert from `main` remain the rollback path.
4. Follow the [hosted WordPress setup](../../README.md#hosted-setup), then check the
   homepage, `/safaris/` and the Safari draft editor. For later releases, repeat
   **Update from Remote → Deploy HEAD Commit** and record the revision.
