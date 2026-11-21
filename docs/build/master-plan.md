# Savannah Crest Safaris WordPress master plan

## Product and architecture

Build a luxury, enquiry-led safari site. Visitors should be able to browse a real itinerary, learn about a destination or experience, or go directly to a planner. All three paths lead to a single, context-aware enquiry form. Operate it as a catalogue of sample itineraries and bespoke trip planning. Bookings, live inventory, and payments belong to a later scope if the business needs them.

**Theme** — Build `savannah-crest` as a standalone WordPress block theme. The homepage, catalogue, itinerary, and destination layouts are specific to Savannah Crest, so the theme owns its templates and design system. Use native blocks and the Site Editor for shared parts and editorial patterns. Keep `theme.json` and templates in Git. [WordPress theme structure](https://developer.wordpress.org/themes/core-concepts/theme-structure/).

**CMS** — Use WordPress core for title, excerpt, rich body copy, media, publication, and editorial Pages. Secure Custom Fields stores structured safari facts, itinerary rows, and linked content. A separate `scs-core` plugin registers the custom types and taxonomies, loads SCF definitions, validates data, controls the catalogue query, and registers server-rendered blocks. Use one forms plugin and one SEO plugin. [WordPress plugin guidance](https://developer.wordpress.org/plugins/post-types/registering-custom-post-types/) · [SCF handbook](https://developer.wordpress.org/secure-custom-fields/).

**Launch assumption** — English-language marketing with a Kenya-first catalogue. Kenya and its destinations are examples until operations confirms the offer. Treat USD as the proposed single catalogue currency. Confirm currency, pricing basis, trip availability, enquiry inbox, public contact details, and response commitment with the business before publishing. Add cross-border itineraries only after they are sellable. Content intake is from Savannah Crest's own tour briefs. Synthetic fixtures are for testing only.

## Step-by-step build order

Phase 1 starts with the versioned WordPress files and the editor workflow. Make each increment suitable for a direct push to `main`. Run the repository checks before pushing. The owner then merges and deploys the selected commit in cPanel and confirms it in the hosted WordPress Admin or on the website. A local WordPress installation or separate staging site is unnecessary for this workflow.

| Step | Work and dependencies | Done when |
| --- | --- | --- |
| 1. Confirm the hosted baseline | Use the existing checkout for source files. Record the installed WordPress, PHP and SCF versions, site URL, cPanel checkout and deploy paths, and current active theme and plugins. Confirm backup and rollback procedures before the first deployment. | The owner can identify where to deploy and what to check on the website. The repo is ready for versioned theme, plugin, and verification files. |
| 2. Create theme and core plugin | Build the standalone `savannah-crest` theme and `scs-core` plugin in the repo. Include fallback template, `theme.json`, header, footer, navigation, and bundled brand assets. Validate the scaffold and plugin dependency handling before pushing. Follow [Theme and UI](theme-and-ui.md). | After cPanel deployment, the owner can confirm activation and check the home or theme preview for layout, menu, logo, and favicon. |
| 3. Build safari entry in WP Admin | Register post types, taxonomies, permalinks, editor capabilities, and SCF groups through [Content model](content-model.md). Apply rewrite setup on activation or as a one-time migration. Add the route, itinerary days, media, and rates fields. Run standalone checks for draft and publish validation. | In hosted WP Admin, an editor can save and reopen an incomplete draft, see the expected fields, and get actionable feedback on invalid publication attempts. |
| 4. Enter one safari through the editor | Add one destination term and enter an approved safari or a clearly labelled synthetic draft in hosted WP Admin. Assign terms, add itinerary rows, set a rate state, and attach permitted images. | An editor can reopen the saved record and preview its destination, days, rate state, and images. Synthetic records stay unpublished. |
| 5. Prove one complete journey | Build the destination hub, safari card, itinerary page, and contextual planner. Set up entry storage and approved notification routing. Use drafts for authorized preview and an approved published safari to test the public journey. | After deployment, the owner confirms the public route, stored test enquiry and delivered notification with the correct trip context. Public checks wait for approved content when needed. |
| 6. Build discovery | Implement server-rendered archive, filters, cards, pagination, and empty states per [Safari catalogue](safari-catalogue.md). Reuse the same card for term hubs and related trips. | The same safari appears in expected destinations, and filter state survives reload, sharing, and browser Back. |
| 7. Build public templates | Add the homepage, catalogue, detail, destination, experience, and guide templates. Enable public stay pages only after the inventory is ready. Establish internal links and proof modules. | Published pages have one clear intent, credible copy and imagery, and a route to a relevant trip or the planner. |
| 8. Populate and review | Collect approved Savannah Crest trip briefs, rates, destination coverage, cleared photography, and planner inbox details. Draft roughly 4–6 launch itineraries, 1 country hub, 2–4 park hubs, 2–3 experience hubs, and 3 useful planning guides as capacity allows. Launch only approved records and preserve a coherent smaller catalogue if needed. | Every published trip has an owner, accurate route and price language, usable images, and a tested enquiry link. |
| 9. Configure SEO and measurement | Configure metadata, canonical URLs, sitemap, crawl policy, and conversion events. Follow [SEO and release](seo-and-release.md). | Hosted URLs pass status, canonical and sitemap checks. An owner-approved test submission fires one conversion event without personal data. |
| 10. Launch and operate | Prepare the database and uploads backup, review approved inventory and settings, push the release revision, and pass the SHA and site checklist to the owner. The owner deploys it in cPanel and confirms the release on the hosted site. | The live site serves the intended URLs, accepts an approved test enquiry, and lets an editor publish a trip without editing code. Record the deployed SHA and any follow-up checks. |

## Repository and deploy boundaries

```text
AGENTS.md                            Agent routing and build rules
Brand.md                            Approved palette and logo usage
assets/brand/                       Editable brand sources and exports
docs/build/                         Build contracts
tools/                              PHP lint, static validation, regression tests
wp-content/
  themes/savannah-crest/             Standalone block theme, templates, patterns, styles
  plugins/scs-core/                  Types, taxonomies, SCF schema, PHP blocks
```

The hosted WordPress installation already exists. Create the directories above as the versioned sources and deploy the project-owned theme and plugin into the corresponding hosted directories. Manage WordPress core and third-party plugin updates through the installation's normal process. Record the tested WordPress, PHP and SCF versions and the deployed revision. Include required brand assets in the theme's runtime directory and retain their masters in `assets/brand/`. Back up the WordPress database and uploads separately from Git.

Commit coherent increments directly to `main`. Before pushing, run PHP syntax lint, applicable static repository validation and feature-specific standalone regression tests. Review the related guide with each change. Send the owner the confirmed `origin/main` SHA and any cPanel, WP Admin, or browser checks still to run. Track sanitized form configuration and intended Site Editor changes as versioned files where possible. Set notification destinations and credentials in the hosted installation.

## Scope after launch

Consider a local-device shortlist, currency conversion, accessible route maps, season comparison, and a richer travel planner after the core paths and lead handling are stable. Add an estimator only when someone owns seasonal price inputs. Prioritize changes from search usage, form completion, and qualified enquiry rate.
