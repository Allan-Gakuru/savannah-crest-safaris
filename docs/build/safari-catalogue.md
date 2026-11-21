# Safari catalogue and filters

## 1. Build the query and URLs

Use the `scs_safari` post-type archive at `/safaris/`. In `scs-core`, attach a narrowly scoped `pre_get_posts` handler to the front-end main query for this archive. Validate the input once and share the normalized filter state with the catalogue block. Do not alter admin queries, REST requests, site search, or unrelated archives. Render the WordPress main query through the catalogue block with a shared card renderer. Use server-rendered form controls, result counts, and pagination.

| Parameter | Input format | Query behavior |
| --- | --- | --- |
| `where` | One approved destination term slug from published inventory, or absent | Use a destination `tax_query` with descendants included. |
| `experience` | One experience term slug from published inventory, or absent | Match that specific term. |
| `duration` | `1-4`, `5-7`, `8-plus`, or absent | Query `scs_duration_days` numerically with inclusive boundaries. |
| `comfort` | One approved comfort slug or absent | Same term logic. Only enable once at least two useful choices exist and publication validation covers the tiers. |
| `sort` | `shortest`, `newest`, or absent | Default to latest published date unless a separate merchandising order is approved. Add post ID as a stable tie-breaker. |

Combine active filter dimensions with AND. Start with one choice per facet. Use `publish` only, a page size of 12, and the native `paged` value. For single-choice destination filters, assign stable unique term slugs across the hierarchy. Use an explicit allowlist, reject arrays or invalid term values with a clear reset path, and safely escape selected labels and query strings. Define the shortest sort as numeric duration ascending and then ID. Keep old/default values out of generated URLs. Keep filters in the new GET URL and drop page number when filters change.

Use `/safaris/page/2/` plus active query parameters for pagination. Pagination anchors are real links. Filter state survives reload, URL sharing, and browser Back. Use one query rule for both the homepage finder and listing form. The finder can pass just `where` and `experience`. Example input is `/safaris/?where=maasai-mara&duration=5-7`. Attribution parameters can be retained for enquiries without changing the archive's canonical URL. See [SEO and release](seo-and-release.md) for the indexing policy.

## 2. Implement the catalogue layout

1. Compose `archive-scs_safari.html` from the global parts, archive introduction, and `scs/safari-catalogue` dynamic block. Use consistent labels for each facet, with an explicit **Show safaris** submit control and a **Clear filters** link to the archive root.
2. On mobile place the form in an inline expandable region with an active-filter count. Expose the same labels and choices as desktop. Keep the results header near the first result and show selected chips with accessible remove controls.
3. Render cards from one presentation partial for archive, homepage, term pages and related sections. Include duration, full title, approved location summary, a useful image, and a link to the native permalink. Render price as `From USD … per person sharing`, a verified range, or **Price on request**, using the same formatting rule as the detail page. Render no stale or missing price as zero.
4. Show a count from the archive query and add paginated links. For a valid zero-match filter, show **No safaris match these filters**, let the guest remove a filter, and offer a link to the planner. Do not show unrelated results under the same count. For an invalid filter show the current inputs may have changed and a link back to all safaris.
5. Build term-page trip lists with explicit published safari queries, independent of the main archive filter hooks. An editor pick must match the page taxonomy or be labelled as a related alternative. Do not mix published stays or guides into trip results.

## 3. Enhance after the baseline works

The launch form can reload the page. Introduce asynchronous filtering only after validating the server implementation. Request the same results HTML, keep URL and history in sync, cancel stale requests, announce changed counts in a polite status region, and provide normal GET fallback on errors. Preserve keyboard focus. Any load-more enhancement should leave ordinary paginated URLs usable.

Add a budget filter or price sort after enough itineraries have current comparable rates. Define a single currency and basis, exclude expired and quote-only prices from numerical results, and never sort on display strings. A local-device wishlist is another later feature. Store public itinerary IDs only, handle deleted entries, and offer to bring the shortlist into the planner without an account.

## 4. Acceptance tests

- A Kenyan country filter returns trips assigned to Maasai Mara and Amboseli when those records exist. A park filter excludes the other park. A cross-border trip appears under both assigned countries.
- Verify numeric boundaries at 4, 5, 7 and 8 days and deterministic order across page 1 and page 2. Draft, private and trashed trips never appear. Removing a constraint broadens or preserves the result set.
- Verify direct URLs, refresh, Back, clear, malformed or duplicate controls, and zero results. Test with JavaScript disabled. Verify cards expose the same current price and name on catalogue, homepage and safari detail.
- Verify modified images and updated trips appear after cache expiry or targeted purge. Confirm no-cache or cache-key configuration preserves valid query strings. Link to a tour through a real `href` from an indexable page.
