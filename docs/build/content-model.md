# WordPress and SCF content model

## 1. Register the content types

Register on `init` in `scs-core`. Set `show_in_rest` for the block editor. Add title, editor, excerpt, thumbnail and revisions support for the public types. Use namespaced PHP, prefixed keys, and explicit supports and capabilities. Reserve the URL bases in this table before seeding any records. [WordPress registration guidance](https://developer.wordpress.org/plugins/post-types/registering-custom-post-types/).

| WordPress object | Key or slug | Public URL | Purpose |
| --- | --- | --- | --- |
| Custom post type | `scs_safari` | `/safaris/` and `/safaris/{trip}/` | Searchable itinerary. Set `has_archive` to `safaris` and rewrite slug to `safaris`, with front prefix disabled. Do not create a competing Page at the archive slug. |
| Custom post type | `scs_stay` | `/stays/{stay}/` | Reusable approved camp and lodge records. Register the single route. Defer the public directory until it has a useful collection. Draft records and unconfirmed stays are hidden from public queries. |
| Built-in post | `post` | `/guides/{article}/` | Planning guidance. Set global Post permalinks to `/guides/%postname%/`, with front prefix disabled on custom rewrites. Use a static Guides posts page at `/guides/`. |
| Built-in page | `page` | `/`, `/destinations/`, `/experiences/`, `/about/`, `/plan-your-safari/`, `/privacy/`, `/booking-terms/` | Editorial entry pages, business information and form. The home page and posts page are selected under **Settings → Reading**. |

Register public singles for stays from the outset, but only publish complete stay records. Build a team roster and approved quotes with editorial blocks initially. Introduce expert or review post types later if those entities need their own pages and relationships.

## 2. Wire the taxonomies

Register with the object types listed below and expose the relevant admin and editor controls. Prefix the keys and disable the permalink front prefix. Set `rewrite.hierarchical` as well as `hierarchical` on geography. Confirm both the base Pages and the term rewrite paths on the hosted site after cPanel deployment.

| Taxonomy | Attached to | Structure and URL | Meaning |
| --- | --- | --- | --- |
| `scs_destination` | Safaris, stays, guides | Hierarchical. `/destinations/kenya/` and `/destinations/kenya/maasai-mara/`. | Country → destination. Candidate Kenyan children include Maasai Mara, Amboseli, Laikipia, and the coast. Introduce another level only when readers need it. |
| `scs_experience` | Safaris, guides | Flat. `/experiences/{experience}/`. | Assign supported intents such as family, honeymoon, migration or safari-and-beach. A trip may carry multiple experiences. |
| `scs_comfort` | Safaris, stays | Flat. Admin and REST editor support. No public archive or rewrite. | Optional single editorial tier. Draft candidates are Classic, Luxury and Signature. Define and agree on differences before publishing the filter. |

- Use taxonomy relationships for grouping and filtering. Use fields for numbers, text, dates, media and editorial picks. Store duration as an integer, never as a term. Calculate display bands from it.
- Assign the most specific visited destination terms. A Kenya query includes its descendants, so do not add Kenya to every trip that already has a Kenyan child term. Assign a country directly when a trip genuinely has no more specific applicable term. Assign leaves in both countries for a cross-border trip.
- Use the standard destination and experience term controls as the only writers of those assignments. An SCF taxonomy field for a primary destination or itinerary stop returns a term ID with **Save Terms**, **Load Terms**, and **Create Terms** disabled. Use a single selection SCF comfort field with both save and load enabled and suppress the duplicate native comfort meta box.
- Public hub terms require authored landing content and an editorial flag that indicates readiness. Unready term URLs that resolve return `noindex, follow` and stay out of nav and sitemaps. Use them internally in filters when they have published inventory. Render their labels on cards without turning them into hub links.

## 3. Create and version SCF field groups

Author versioned SCF field-group JSON under `wp-content/plugins/scs-core/scf-json/` using the location rules and field definitions below. Keep stable group and field keys. Set IDs as the return format for image, gallery, term and post relationships. Use tabs with descriptive editor help. Configure the plugin to load the directory through SCF Local JSON and validate the definitions before pushing. In hosted WP Admin after deployment, confirm the schema loads and sync the versioned groups when required. Preserve intentional admin edits by exporting the affected definitions back into Git, and resolve conflicting definitions before syncing. Register CPTs and taxonomies in the plugin, independently of the active theme. [SCF field types](https://developer.wordpress.org/secure-custom-fields/features/field/) · [Local JSON](https://developer.wordpress.org/secure-custom-fields/features/local-json/) · [Taxonomy field](https://developer.wordpress.org/secure-custom-fields/features/field/taxonomy/).

### Safari fields

Use the native title for H1, featured image for the main photograph, excerpt for the card text, and body editor for the trip-fit narrative. The structured fields below are in the `Safari details` group on `scs_safari`. Labels are editorial, keys are stable.

| Field key | SCF type | Validation and rendering |
| --- | --- | --- |
| `scs_duration_days` | Number, integer | Required positive value. Source of card duration, day range limit and catalogue bands. Derive nights only where the trip brief confirms the relationship. |
| `scs_primary_destination` | Taxonomy, single term ID | Required for publish. Must be an assigned destination. Used for the lead route label and related-trip selection. |
| `scs_start_location`, `scs_end_location` | Text | Required. Guest-facing departure and end locations. |
| `scs_price_mode` | Select | Required. `on_request`, `from`, or `range`. Default to `on_request`. |
| `scs_price_from`, `scs_price_to` | Number | Lower bound only in `from` mode, both required in `range`. Upper bound must be at least the lower. Positive numeric values. Hidden inactive values are ignored by public rendering and sorting. |
| `scs_price_currency`, `scs_price_basis`, `scs_price_note`, `scs_price_valid_until` | Select, Select, Textarea, Date Picker | Currency, basis and pricing note are required for visible rates. Launch with one currency and agreed basis, proposed as per person sharing for the full trip. The validity date is optional. If set, the rate is displayed through that date. Expired rates resolve to Price on request and are excluded from price ordering. The pricing note explains season, party assumptions or exclusions. |
| `scs_highlights`, `scs_includes`, `scs_excludes` | Separate repeaters, each with `label` text | Keep short, specific bullets. Inclusions and exclusions are required before launch. |
| `scs_gallery` | Gallery | Optional set of cleared attachment IDs. Core featured image remains the hero default. |
| `scs_itinerary` | Repeater | At least one day or grouped range, in order. See subfields below. |
| `scs_best_time` | Repeater of month checkboxes and note | Optional. Set after operations validates the advice. Keep the explanation visible in text. |
| `scs_related_safaris` | Relationship, safari IDs | Optional manual picks, excluding self. Fall back to matching primary destination and then experience. |

The itinerary repeater has `day_from` and optional `day_to` positive integer fields, `heading` text, `destination` taxonomy term ID, `narrative` WYSIWYG, optional `stay` post object ID, optional `stay_label` fallback, optional `meals` and `transfer_note` text, and an optional `image` attachment ID. Require ordered ranges beginning on Day 1, ending at `scs_duration_days`, and covering each day once. Default an empty end to the start. Validate linked terms and published stay status on render. If a stay is unpublished, render an approved fallback label or omit the stay link. Derive the displayed route sequence by collapsing consecutive repeated day destinations. [SCF Repeater](https://developer.wordpress.org/secure-custom-fields/features/field/repeater/) · [Post Object](https://developer.wordpress.org/secure-custom-fields/code-reference/fields/post-object/).

### Destination and experience fields

Use the standard term Name and Description. Add `scs_term_hero` Image, `scs_term_intro` Textarea, `scs_term_body` WYSIWYG, `scs_term_ready` True/False (default false), `scs_term_faqs` Repeater with question and answer, `scs_term_featured_trips` Relationship, and `scs_term_guides` Relationship. Destination terms can also have `scs_term_seasons` Repeater (months, short label, conditions text). Experience pages explain suitability, season constraints, and what to expect. Use authored content for those nuances and keep travel dates out of taxonomy names.

### Stay, guide, and homepage fields

- **Stay.** Native title, featured image, excerpt and body. Add `scs_stay_gallery` Gallery, `scs_stay_facilities` Repeater (label), `scs_stay_transfer` Textarea and `scs_stay_access_notes` Textarea. Assign a destination via taxonomy. Show verified accessibility facts with their limitations.
- **Guide.** Title, body, excerpt, primary photo, author, and reviewed date. Add `scs_guide_reviewed_on` Date and `scs_guide_trips` Relationship for editorial picks. Use the destination or experience relationship for fallback related trips.
- **Home.** Editable core blocks for headline, intro, imagery and rotating editorial content. Add `scs_home_featured_trips` Relationship and `scs_home_featured_places` SCF taxonomy field returning term IDs with term saving disabled. Scope the group to the assigned front page.

## 4. Validate and operate the CMS

Allow incomplete drafts. Block publication when core itinerary, destination, price, media, or disclosure requirements fail. Use SCF field validation for editor feedback, plus shared application-level validation for publish, REST, bulk edits, and imports if those paths are enabled. Check repeatability on an already published trip. Derive filter values and public price state at read time or update derived metadata consistently on all write paths. Do not expose internal notes or enquiry data through SCF REST. Escape field output for its display context and use WordPress media helpers for render sizes.

Use standalone regression tests for publish rules and filtering boundaries with controlled fixtures. After deployment, verify the hosted WP Admin Safari editor can save and reopen an incomplete draft. Use an approved safari or an unpublished, clearly labelled synthetic draft to check field entry and authorized preview. Use approved published trips with contrasting terms and prices for public catalogue checks as inventory permits. Confirm an editor can add a trip, reorder days, change a stay, preview, and publish without editing a block template. See [Templates and publishing](templates-and-publishing.md) for the editorial handoff.
