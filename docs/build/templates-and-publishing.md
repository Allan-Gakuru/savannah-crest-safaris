# Templates and publishing

## Company and browse Pages

The first administrator visit to WP Admin after deployment provisions About Us, Traveller Stories, Destinations and Experiences when those slugs are available. The initial native block content lives in `scs-core/pages/`. Editors maintain published copy under **Pages** in WordPress. The provisioning routine leaves existing Pages in place and runs once for each slug. Keep the About section anchors `meet-sam`, `our-standards`, `how-it-works` and `safari-faqs` in sync with the header and page jump links. The Stories page launches with Savannah Crest field notes. Add attributed guest accounts once the travellers have approved publication.

Destinations and Experiences include ready hubs and terms currently used by public safaris. Visitors can follow an available term straight to the catalogue while its editorial hub is in preparation.

## 1. Build the templates in order

Use HTML block templates in the standalone `savannah-crest` theme to compose pages, and native patterns for editable narrative sections. After creating the shared header, footer, and fallback, build one complete destination → itinerary → enquiry path before expanding the other routes. Register structured-data-facing blocks in `scs-core` with `block.json` and PHP render functions. Use `postId` context on singles and the queried object for term templates. Use the main query in the archive block. Do not issue a second Query Loop for the same archive results. Give editor previews illustrative placeholders without inventing production content, and keep internal block options small. [Dynamic blocks](https://developer.wordpress.org/block-editor/getting-started/fundamentals/static-dynamic-rendering/) · [WordPress template hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/).

| Template | Route | Section order |
| --- | --- | --- |
| `index.html` | Fallback when a specific template is unavailable | Shared header → current query results → shared footer |
| `front-page.html` | `/` | Photo hero with Plan and Explore actions → destination and duration finder → safari team introduction and planning steps → luxury safari country guides, Kenya first → featured safaris → parks and places → experiences → planning CTA |
| `archive-scs_safari.html` | `/safaris/` | Breadcrumbs and intro → filters → results and pagination → planner CTA/help |
| `single-scs_safari.html` | `/safaris/{trip}/` | Breadcrumbs → trip hero → key facts and price → overview → route and days → stays → inclusions/exclusions → timing → related trips and enquiry |
| `taxonomy-scs_destination.html` | `/destinations/{path}/` | Breadcrumbs → place hero and intro → matching trips → child places where relevant → when/where/how to visit → stays and guides → plan |
| `taxonomy-scs_experience.html` | `/experiences/{experience}/` | Hero and audience fit → matching trips → place and season guidance → practical FAQs → plan |
| `single-scs_stay.html` | `/stays/{stay}/` | Place and property → photo/camp overview → relevant facilities and transfer notes → trips using the stay → planner CTA |
| `home.html`, `single.html` | `/guides/` and article pages | Guide archive links to posts. Articles use visible author and reviewed date, distinct narrative and contextual links. |
| `page.html` and focused page template | Landing, company and planner Pages | Editable Get to know us, Destinations, Experiences and Terms pages. Planner template uses a quiet layout with brand, progress and a safe exit. |
| `page-enquire.html` | `/enquire/` | Quiet branded header → enquiry introduction and step indicator → configured enquiry form |

## 2. Build the itinerary detail vertical slice

1. Build the overview and facts first. Place H1, route and days immediately visible. Display start and end, inclusions, and accurate price context near the CTA. Format the currency and price basis identically to cards, with understandable explanations for single supplements, exclusions, or seasons where applicable.
2. Render copy from the native post body and the structured measures from SCF. Use section anchors for itinerary, stays and price details. Do not make visitors depend on a map to understand the route. Show an ordered, text-based route from published stops and use on-demand map behavior only when it adds value.
3. Render each day as native details/summary with a logical day label, content, and optional linked stay. Start with the first day open if usability tests support it. Use grouped labels for repeated stays when the range is intentional. Render meal and transfer notes only if those fields are populated. Keep layout stable when days lack images.
4. Build a pricing and inclusions section with two clear columns on desktop and readable vertical order on mobile. Copy must state what is covered and how the sample can be personalized. Show Price on request when there is no current published rate.
5. Add related trips and one contextual CTA. Use manually approved picks first, then related published safaris. For fallback match a destination or experience, exclude the current trip, and cap at three. Resolve the plan link from the public trip slug.

## 3. Build the landing pages

- **Home.** Pitch the local planning proposition in one sentence and show a clear path to trips. Feature up to five published itineraries, with editor picks first. Fill any remaining spaces from eligible published trips. Close the grid with the general enquiry card. Render the approved photographic and brand layout from [Theme and UI](theme-and-ui.md).
- **Destinations.** The country cards link to the existing taxonomy routes. Keep the guide copy in `scs-core/content/destination-guides.php` and the matching responsive photos in the theme. Show one card per guide with published trips, with Kenya first. Country guides show matched trips, places, season guidance, planning tips and FAQs. Zanzibar has its own guide and appears in Tanzania’s matching trips while keeping its existing URL.
- **Experiences.** Group family and honeymoon separately when the offer genuinely differs. Use a unique decision guide for each and explain relevant constraints. Link to curated trips and matching destinations.
- **Guides.** Build practical evergreen articles such as timing, choosing a route, and how bespoke trips are quoted. A travel advisor reviews facts and the published reviewed date. Use links with meaningful labels to relevant hubs and itineraries.
- **Stays.** Populate enough detail for a visitor to compare comfort, location and trade-offs. Render trips that use the stay from their itinerary association. For small launch inventory a capped candidate query and exact ID checks are acceptable. If the collection grows, index the derived association on save and reconcile it when a stay is unpublished.

## 4. Editorial intake and publishing workflow

1. **Tour owner supplies the source.** Use Savannah Crest's own tour briefs, with an internal owner, inclusive day schedule, start/end logistics, per-person or total basis, planned stays, inclusions, exclusions, and what the planner can change. Include a rate validity date where applicable. Record cleared media and which price claims can go public.
2. **Editor creates a draft.** Assign taxonomy terms, copy only the approved original material from the brief, write a short excerpt, fill SCF quantities and itinerary fields, and link approved stays. Draft trip flows should be testable by authorized preview.
3. **Travel advisor reviews.** Check routes, stops, overnight claims, transport, price currency/validity, public offers, and logistics. Review date-sensitive content and any wildlife or seasonal language.
4. **Editor previews.** Use authorized previews on the hosted site for desktop and mobile layout, real crop and alt text, missing optional sections, and price state. Check the enquiry context with an approved published trip and use the owner-approved recipient path for submissions. Set term readiness once the hub passes the content gate.
5. **Publisher releases.** Confirm index settings, navigation links, sitemap inclusion, and one working lead path. Route every published trip from at least one internal link. Track who owns future updates.
6. **Operations maintains.** Review rates regularly and reconfirm dated rates before expiry. Update featured picks, regenerate cache on content changes, and replace removed supplier records with a checked alternative or hide their link. Redirect retired URLs to a genuinely equivalent page when available. Otherwise return 404 or 410 and remove the URL from menus and sitemap.

Build the templates against standalone fixtures and authorized previews of unpublished synthetic drafts as needed. Use Savannah Crest's approved inventory for the first public catalogue and public journey checks.
