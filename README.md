# Savannah Crest Safaris

The Savannah Crest block theme and Savannah Crest Core plugin are versioned under
[wp-content](wp-content). [AGENTS.md](AGENTS.md) routes feature work to the build guides.
Use [the master plan](docs/build/master-plan.md) for the release order and
[the release guide](docs/build/seo-and-release.md) for runtime QA.

## Before pushing to main

Run these in the repository root using PHP CLI, PowerShell, Python 3.9 or newer, and Node.js.

~~~powershell
.\tools\lint-php.ps1
python -B tools/validate_repository.py
php tools/test_safari_core.php
node tools/test_events.cjs
node --check wp-content/plugins/scs-core/assets/editor.js
node --check wp-content/plugins/scs-core/assets/events.js
git diff --check
~~~

The static validator checks the deployable templates, registered blocks, SCF schema,
brand files and font licenses. The standalone tests exercise PHP and browser logic
with stubs. The hosted installation is used for WordPress Admin and website testing.

## Hosted setup

1. Follow the [cPanel connection and deployment guide](docs/build/cpanel-deployment.md).
   Verify the deployed revision and the installed WordPress, PHP and Secure Custom
   Fields versions before activating the project files.
2. Activate Secure Custom Fields with Repeater and Gallery support, then activate
   **Savannah Crest Core**. Check the Safari details fields and sync groups in SCF
   if the installed version reports a versioned definition ready to sync.
3. In **Appearance → Themes**, preview and activate **Savannah Crest**. In
   **Settings → Reading**, assign the static Home and a separate Guides posts page.
   Set a cleared featured image on Home to replace the bundled illustrative hero.
   Review any saved template overrides and the navigation in the Site Editor.
4. Configure pretty permalinks and the guides path described in the
   [content model](docs/build/content-model.md). Keep the native Safari archive at
   **/safaris/**. Create the Destinations and Experiences Pages with the matching slugs
   so their directories and navigation can appear. Publish only approved content.
5. Save and reopen an incomplete Safari draft in WP Admin. Add a featured image,
   destination assignment, duration, route, full itinerary, inclusions and exclusions.
   Save those fields as a draft before publishing. Preview the itinerary as an
   authorized editor and use an approved published safari for the public browse test.

### Planner and site verification

Use [the enquiry setup](docs/build/ux-and-enquiries.md) to create the two-step
Forminator form and map its three context fields in **Settings → Safari Planner**.
Configure the approved inbox, submission storage, spam protection, and visitor
acknowledgement before opening the planner to visitors. Exclude the planner from
server and CDN full-page caching, along with `/enquire/`.

After deploying, open WP Admin once as an administrator. The core plugin creates
the **Enquire**, **About Us**, **Traveller Stories**, **Destinations** and
**Experiences** pages if their slugs are available. Edit their copy under **Pages**.
The header links to Enquire while the homepage actions link separately to the
catalogue and trip planner. Both enquiry pages use the form configured under
**Settings → Safari Planner**.

Check the homepage, catalogue filters and clean reset, destination page, itinerary
accordion, and planner on the hosted site. From an approved itinerary, send a
clearly marked test enquiry and verify the stored context and inbox delivery.
