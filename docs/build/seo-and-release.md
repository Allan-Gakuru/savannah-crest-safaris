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
4. Use native WordPress responsive image markup with explicit dimensions. Load the intended hero promptly and lazy-load supporting galleries/media. Keep third-party scripts out of the critical path, select a static hero and few font files, and verify visible content without JS. Target field LCP ≤ 2.5 s, INP ≤ 200 ms and CLS ≤ 0.1 at the 75th percentile when sufficient field data exists. Use mobile lab runs on staging for earlier feedback. [Core Web Vitals](https://developers.google.com/search/docs/appearance/core-web-vitals).
5. Set the protected staging environment to discourage indexing, and protect it with access controls. Set production to allow indexing of intended pages. Check server response headers, canonical host, robots and sitemap after switching environments. Submit the production sitemap in the verified Search Console property.

## Pre-release QA matrix

| Area | Test and evidence |
| --- | --- |
| Functional browse | Record a working route from home → filter results → itinerary → enquiry and from destination and experience pages into relevant trips. Test zero matches, pagination, no JS for browsing, and wrong slugs. |
| CMS data | Test complete and partial drafts, preview, day validation, edited trip and expired price state. Switch theme on staging and confirm registrations remain. Confirm term archive and CPT URLs after rewrite setup. |
| Visual and accessibility | Test iOS Safari and Android Chrome where available plus desktop Chrome/Firefox. Check keyboard, headings, zoom, contrast, screen reader labels, responsive logo/icon and sticky CTA placement. |
| SEO | Inspect status, HTML canonical, meta robots, sitemap membership and fetchable CSS/JS on a clean trip, term, filtered archive, page 2, search, planner, and unknown URL. Check metadata and schema match visible content. |
| Lead delivery | Submit approved test data to staging. Verify stored entry, planner mail, visitor acknowledgement, cached form context, spam path and retry behavior. Confirm conversion event fires once after accepted submission. |
| Performance | Test cold mobile homepage, catalogue and detail with a realistic content set and slow connection. Record hero bytes, loading behavior and observed layout shifts. |

## Deploy and maintain

1. Confirm real content, contact details, supplier approvals, brand media, and operator-owned booking and privacy text. Delete or unpublish synthetic fixtures from the launch database. Keep the latest sanitized configuration in version control.
2. Take a verified pre-release database and uploads backup. Record release identifier, dependencies and tested environment versions. Deploy the standalone `savannah-crest` theme and `scs-core` plugin, then sync approved SCF definitions and apply controlled configuration changes. Verify the active theme and check for database template overrides on staging. Avoid overwriting live entries with staging data.
3. Confirm mail configuration, query-sensitive caching, rewrite routes, content visibility and the production domain. Visit the home, one destination, one itinerary, and the planner. Verify favicon and fresh assets. Run a test enquiry only through the business-approved recipient path.
4. Roll back the project theme/plugin files to the previous tested revision if a severe release issue is found. Restore schema/data from the release backup only as required and reconcile any live enquiries created since the backup before replacing database state.
5. Check Search Console URL coverage, sitemap status, 404s, and performance after launch. Review lead delivery and qualification regularly. Keep a calendar for rates and travel guidance reviews. Test changes to WordPress, SCF, the Savannah Crest theme, and plugins on staging before production.
