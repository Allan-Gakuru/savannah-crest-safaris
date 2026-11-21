# SEO, QA, and release

## Index and link policy

| URL class | Expected treatment |
| --- | --- |
| Home, published tours, main catalogue and substantive guides | HTTP 200, indexable, descriptive metadata and self-canonical. Include canonical content in sitemap. |
| Ready destination and experience hubs | HTTP 200, self-canonical and indexable only when editorially ready. Include in nav where relevant, in sitemap and in contextual internal links. |
| Clean catalogue pagination | HTTP 200 and self-canonical for valid pages, real sequential links. Do not canonicalize later pages to page 1. Sitemap can list the root archive without page-number variants. |
| Active filter/sort URLs and native search results | Keep usable, `noindex, follow`, outside sitemap. For filter subsets, avoid canonicalizing distinct result sets to the unfiltered archive. No automatic links generating combinations. |
| UTM-only versions | Serve the same content and canonicalize to the equivalent clean URL while preserving permitted attribution for the visit. |
| Planner and thank-you | HTTP 200, `noindex, follow` and outside sitemap. Show generic confirmation text on any public thank-you URL. |
| Unready term pages and utility archive pages | Keep out of menus and sitemap. If they resolve to 200, use `noindex, follow` until the substantive landing page is ready. |
| Missing route, unknown term, out-of-range pagination | Correct HTTP 404, with links back to browsing. Retired pages with a genuine replacement use a single 301 to it. |

Keep pages with `noindex` crawlable so the directive can be seen. If parameter crawling becomes a real issue, inspect logs and Search Console before deciding whether to block selected parameters. Avoid attaching indexable country, park and experience content to a query-string filter. Build an authored landing page for the corresponding intent. [Google faceted navigation](https://developers.google.com/crawling/docs/faceted-navigation) · [Canonical guidance](https://developers.google.com/search/docs/crawling-indexing/consolidate-duplicate-urls) · [Sitemap guidance](https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview).

## Set up SEO in order

1. Choose one SEO plugin for metadata, robots directives, and sitemap output. Give it one canonical source per route. Use its supported integrations with `scs-core` to exclude unready terms and filtered URLs and to override archive titles if necessary. Verify public output rather than relying on admin indicators. Configure English site language and one HTTPS canonical host. Check www and protocol redirects.
2. Write page-specific titles and descriptions. Use an accurate social image and OG tags for each tour and hub. Give H1 and breadcrumb labels a corresponding visible page purpose. Reserve the same intent for one primary URL during launch.
3. Ensure real links from hubs to trips, trips to stays and destinations, and guides to tours. Use a consistent visible breadcrumb path with meaningful landmarks. Emit a single supported `BreadcrumbList` and accurate site organization information. Use article markup where supported for guides. Treat FAQs as reader content. Require an eligibility review and visible claim match before adding price, self-serving ratings, or product schema. [Google on links](https://developers.google.com/search/docs/crawling-indexing/links-crawlable) · [Breadcrumb guidance](https://developers.google.com/search/docs/appearance/structured-data/breadcrumb).
4. Use native WordPress responsive image markup with explicit dimensions. Load the intended hero promptly and lazy-load supporting galleries/media. Keep third-party scripts out of the critical path, select a static hero and few font files, and verify visible content without JS. Target field LCP ≤ 2.5 s, INP ≤ 200 ms and CLS ≤ 0.1 at the 75th percentile when sufficient field data exists. Run mobile lab checks on the hosted website after cPanel deployment. [Core Web Vitals](https://developers.google.com/search/docs/appearance/core-web-vitals).
5. Keep drafts and test content out of navigation and sitemaps, and use authenticated previews for unfinished pages. Allow indexing of approved public routes when the owner releases them. Confirm HTTP status, canonical host, robots and sitemap on the hosted URL after deployment. Submit the sitemap in the verified Search Console property.

## Repository checks before pushing

- **PHP syntax lint.** Run `php -l` across all project-owned PHP in `wp-content/themes/savannah-crest/` and `wp-content/plugins/scs-core/`, plus any PHP test files changed. Use a PHP CLI version compatible with the hosted site. Add `tools/lint-php.ps1` with the scaffold for repeatable syntax lint.
- **Static repository validation.** Check the affected scaffold, file references, block and theme JSON, SCF definitions, and deploy paths without loading WordPress. Run the relevant checked-in validators as they are added. Check the patch with `git diff --check`.
- **Standalone regression tests.** Run relevant PHP CLI tests against the real site code with minimal WordPress boundary stubs and synthetic fixtures. Use matching JavaScript checks when client-side behavior changes. Keep mail, integrations, and writes to the hosted site out of these tests.

Commit coherent changes directly to `main` after the applicable checks pass. Push to `origin/main` and verify the pushed SHA. Report the checks run, any tests deferred until deployment, and the affected URLs and WP Admin steps. The cPanel deployment and hosted QA are confirmed by the owner.

## Hosted QA after cPanel deployment

Use this matrix for the affected routes and behaviors. Run the relevant checks on the deployed revision. Leave checks pending when the required content or configuration is unavailable.

| Area | Test and evidence |
| --- | --- |
| Functional browse | Record a working route from home → filter results → itinerary → enquiry and from destination and experience pages into relevant trips. Test zero matches, pagination, no JS for browsing, and wrong slugs. |
| CMS data | Test complete and partial drafts, preview, day validation, edited trips and expired prices in hosted WP Admin. Verify CPT and term URLs after rewrite setup. Cover theme-independent registration in the repository checks. |
| Visual and accessibility | Test iOS Safari and Android Chrome where available plus desktop Chrome/Firefox. Check keyboard, headings, zoom, contrast, screen reader labels, responsive logo/icon and sticky CTA placement. |
| SEO | Inspect status, HTML canonical, meta robots, sitemap membership and fetchable CSS/JS on a clean trip, term, filtered archive, page 2, search, planner, and unknown URL. Check metadata and schema match visible content. |
| Lead delivery | Use owner-approved test data and notification routing. Verify the stored entry, planner notification, visitor acknowledgement, and cached context. Confirm one conversion event on acceptance. Test failure and retry paths with standalone stubs. |
| Performance | Test cold mobile homepage, catalogue and detail with a realistic content set and slow connection. Record hero bytes, loading behavior and observed layout shifts. |

## Deploy and maintain

1. Before deploying, confirm the versioned sources and applicable repository checks, the pushed SHA, and the cPanel checkout, branch and deploy paths. For a public launch, confirm approved content, contact details, media and operator-owned booking and privacy text. Keep synthetic fixtures unpublished and configuration exports sanitized.
2. The owner records the currently deployed SHA and dependencies and takes a verified database and uploads backup before configuration or schema changes. The owner merges the intended `origin/main` revision through cPanel and deploys the project files into the existing WordPress installation. Keep WordPress core, uploads, credentials and live data outside the deployable paths. Sync versioned SCF groups and apply hosted configuration changes when required.
3. The owner verifies the affected pages and admin flows on the hosted site. Check the active theme, Site Editor overrides, rewrite routes, asset freshness and cache behavior as applicable. Use the matrix for broader releases. Record the deployed SHA and which checks passed or are still pending.
4. If a release breaks the site, the owner can redeploy the last known-good project files through cPanel or deploy a tested revert from `main`. Restore database or uploads only when the failing change requires it. Reconcile enquiries created since the backup before replacing database state.
5. After launch, check Search Console coverage, sitemap status, 404s, performance, and lead delivery. Use the same repository and hosted checks for later releases. For changes to WordPress or installed plugins via WP Admin, record dependency versions and confirm the affected website and admin flows after updating.
