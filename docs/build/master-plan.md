# Savannah Crest Safaris WordPress master plan

## Product and architecture

Build a luxury, enquiry-led safari site. Visitors should be able to browse a real itinerary, learn about a destination or experience, or go directly to a planner. All three paths lead to a single, context-aware enquiry form. Operate it as a catalogue of sample itineraries and bespoke trip planning. Bookings, live inventory, and payments belong to a later scope if the business needs them.

**Theme** — Install the free [Twenty Twenty-Five parent theme](https://wordpress.org/themes/twentytwentyfive/) and activate the custom `savannah-crest` child theme. Use the Site Editor for shared block parts and editorial patterns. Use `theme.json` and versioned templates for the brand and layout system.

**CMS** — Use WordPress core for title, excerpt, rich body copy, media, publication, and editorial Pages. Secure Custom Fields stores structured safari facts, itinerary rows, and linked content. A separate `scs-core` plugin registers the custom types and taxonomies, loads SCF definitions, validates data, controls the catalogue query, and registers server-rendered blocks. Use one forms plugin and one SEO plugin. [WordPress plugin guidance](https://developer.wordpress.org/plugins/post-types/registering-custom-post-types/) · [SCF handbook](https://developer.wordpress.org/secure-custom-fields/).

**Launch assumption** — English-language marketing with a Kenya-first catalogue. Kenya and its destinations are examples until operations confirms the offer. Treat USD as the proposed single catalogue currency. Confirm currency, pricing basis, trip availability, enquiry inbox, public contact details, and response commitment with the business before publishing. Add cross-border itineraries only after they are sellable. Content intake is from Savannah Crest's own tour briefs. Synthetic fixtures are for testing only.

## Step-by-step build order

| Step | Work and dependencies | Done when |
| --- | --- | --- |
| 1. Record launch inventory | Agree on which journeys to sell, who prices them, where guests travel, which photography is cleared, and who owns enquiries. Choose the first itinerary for an end-to-end build. | Content owner signs off a trip brief, destination list, media source, and a planner inbox. |
| 2. Set up development | Start from this repository, record WordPress, PHP, SCF and parent-theme versions from the existing installation, and set up local and protected staging. Back up the existing WP database and uploads before configuration changes. | Both environments match on relevant versions and the repo contains no secrets or private exports. |
| 3. Create theme and core plugin | Install the parent and activate the child. Add minimal `theme.json`, header/footer, navigation, and local brand assets. Activate `scs-core` and verify its dependency handling. Follow [Theme and UI](theme-and-ui.md). | A light cream home page, menu, logo, and favicon render, and a theme switch leaves the content model registered. |
| 4. Register the data model | Add keys, taxonomies, permalinks, admin capabilities, and SCF groups through [Content model](content-model.md). Flush rewrites once after activation or a rewrite migration. Seed a Kenya term and one park term before adding a trip. | One draft safari displays the right fields, can be previewed, and validates malformed itinerary ranges and price states. |
| 5. Deliver one vertical slice | Build the smallest complete path from a destination hub to a safari card, a detail page, an itinerary day, and the contextual planning form. Set up entry storage and delivery before expanding catalogue content. | QA can browse from an actual route to a stored staging lead with the correct trip context, and an advisor receives it. |
| 6. Build discovery | Implement server-rendered archive, filters, cards, pagination, and empty states per [Safari catalogue](safari-catalogue.md). Reuse the same card for term hubs and related trips. | The same safari appears in expected destinations, and filter state survives reload, sharing, and browser Back. |
| 7. Build public templates | Add the homepage, catalogue, detail, destination, experience, and guide templates. Enable public stay pages only after the inventory is ready. Establish internal links and proof modules. | Published pages have one clear intent, credible copy and imagery, and a route to a relevant trip or the planner. |
| 8. Populate and review | Draft roughly 4–6 launch itineraries, 1 country hub, 2–4 park hubs, 2–3 experience hubs, and 3 useful planning guides as capacity allows. This is a content target. Launch only approved records and preserve a coherent smaller catalogue if needed. | Every published trip has an owner, accurate route and price language, usable images, and a tested enquiry link. |
| 9. Configure SEO and measurement | Configure clean metadata, verified canonical URLs, sitemap, crawl policy, and the conversion funnel. Follow [SEO and release](seo-and-release.md). | Production-intended URLs pass the status/canonical tests. A successful staging lead fires one event with no personal data. |
| 10. Launch and operate | Confirm staging QA, create a backup and release record, deploy code, sync the correct field groups and form configuration, and run smoke tests. | The live site serves the intended URLs, accepts a test enquiry through an approved inbox, and lets an editor publish a trip without editing code. |

## Repository and deploy boundaries

```text
AGENTS.md                            Agent routing and build rules
Brand.md                            Approved palette and logo usage
assets/brand/                       Editable brand sources and exports
docs/build/                         Build contracts
wp-content/
  themes/savannah-crest/             Child theme, templates, patterns, styles
  plugins/scs-core/                  Types, taxonomies, SCF schema, PHP blocks
```

The WordPress installation already exists. Create the above directories as the versioned sources and deploy only the project-owned theme and plugin into the respective directories of the WordPress installation. Manage the parent theme and third-party plugins with the installation's normal updates. Record tested versions. Deploy the required approved brand assets to the theme's runtime assets and keep their master versions in `assets/brand/`. WordPress content lives in its database. Back up uploads and content separately from Git.

Use a feature branch or separate coherent changesets for theme bootstrap, schema, enquiry, catalogue, and templates. Review related guide updates with their changes. Export only sanitized configuration when moving form settings between environments, and set mail destinations and external credentials per environment. Document any settings created in the Site Editor that need to be carried across environments.

## Scope after launch

Consider a local-device shortlist, currency conversion, accessible route maps, season comparison, and a richer travel planner after the core paths and lead handling are stable. Add an estimator only when someone owns seasonal price inputs. Prioritize changes from search usage, form completion, and qualified enquiry rate.
