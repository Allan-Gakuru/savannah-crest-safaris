# Savannah Crest Safaris build instructions

Read this file at the start of every build or maintenance task in this repository. Read [the master plan](docs/build/master-plan.md) first, then open every guide that applies to the requested change. The guides are the implementation contract. When the feature changes, update the relevant guide and implementation together. Follow the owner's current direction when it supersedes a documented choice.

For a new build, set up local and staging, then build the theme, core plugin, content model, and WP Admin safari entry workflow. Once an editor can save a safari draft, enter a test safari and verify the destination → itinerary → enquiry journey. Then expand the catalogue and landing templates, enter approved launch inventory, and complete SEO and release checks. For a targeted change, start at the relevant guide and check downstream templates, links, and enquiry behavior.

## Route each task to its instructions

| Work | Read before editing |
| --- | --- |
| Scope, sequencing, architecture, or environments | [Master plan](docs/build/master-plan.md) |
| Theme, layout, navigation, components, responsive design, or brand assets | [Theme and UI](docs/build/theme-and-ui.md) and [Brand.md](Brand.md) |
| Custom post types, SCF field groups, taxonomies, or editor workflows | [Content model](docs/build/content-model.md) |
| Search, safari listings, filters, sort, result cards, or pagination | [Safari catalogue](docs/build/safari-catalogue.md) and [Content model](docs/build/content-model.md) |
| Home, itinerary, destination, experience, stay, or guide templates | [Templates and publishing](docs/build/templates-and-publishing.md), [Theme and UI](docs/build/theme-and-ui.md), and [Content model](docs/build/content-model.md) |
| Trip-planning flow, forms, CRM handoff, or conversion events | [UX and enquiries](docs/build/ux-and-enquiries.md) |
| URLs, indexation, redirects, structured data, deployment, or QA | [SEO and release](docs/build/seo-and-release.md) and the feature guide |

## Build rules

- Build `savannah-crest` as a standalone WordPress block theme. Keep its styles, templates, parts, patterns, and brand presentation in Git. Use native WordPress blocks and the Site Editor.
- Put content types, taxonomies, Secure Custom Fields definitions, and shared catalogue logic in `scs-core` so these features remain available when the active theme changes.
- Keep one canonical source for each URL, field, taxonomy relationship, price, and form conversion event. Commit schema and template changes with their versioned sources.
- Use the approved logo files under `assets/brand/` and the palette in [Brand.md](Brand.md). Follow the visitor journeys in [UX and enquiries](docs/build/ux-and-enquiries.md) and use original Savannah Crest layouts, copy, and cleared media.
- Build the catalogue from Savannah Crest's own tour briefs and approved supplier material. Do not fetch, scrape, import, or transcribe third-party tours into the CMS. Use synthetic test records on local and staging until the owner supplies real inventory.
- Keep public content and catalogue results in server-rendered HTML. Enhance with JavaScript where it improves the flow. Check normal navigation, keyboard and mobile behavior, and empty/error states.
- Keep secrets, leads, supplier contracts, private rates, and production exports with personal data out of the public repo. Work in local or staging first. Use an approved release path for production changes and external integrations.
- Verify the feature against the acceptance criteria in its guide. Record any migration, rollback step, dependency, or unfinished assumption that affects the next build.
- Use the terminal for Git operations. After every verified repository update, commit the task's changes, push them to `origin/main`, and confirm GitHub has the new commit. If the remote has moved, integrate its changes before pushing. Respect branch protection and report any blocker.
