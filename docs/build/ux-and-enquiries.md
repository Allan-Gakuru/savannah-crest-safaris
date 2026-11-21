# User journeys and enquiries

## Design the three main journeys

| Visitor | Route | Decisions supported |
| --- | --- | --- |
| Has a place in mind | Destinations → country/park → matching safari → plan | When to go, what the route covers, how many days, and what the quoted basis includes |
| Has an experience in mind | Experiences → relevant trips → detail → plan | Suitability for family, honeymoon or migration goals, plus relevant seasonal and accommodation constraints |
| Wants a planner | **Plan your safari** → short form → acknowledgement | Tell us just enough to start, with a valid path for flexible or uncertain plans |

Use **Explore safaris** for browsing and **Plan your safari** for the main lead action. On a safari page use **Enquire about this safari** with the trip name echoed on the form. Explain that the itinerary is a starting point and pricing is indicative where applicable. Show verified expertise, real trip inclusions, and a response window approved by the operations owner.

## Build one form with two steps

1. Choose one form plugin with stored entries, multipage support, notifications, and query prepopulation. The default candidate is **Forminator**. Confirm the installed version's behavior in hosted WP Admin after deployment and version the sanitized configuration. Use a native Page at `/plan-your-safari/`. Embed the form with a supported block or shortcode. [Forminator documentation](https://wpmudev.com/docs/changelog/forminator/).
2. **Step 1 – Your safari.** Show the known trip or origin context. Ask for a travel window with a flexible/unsure option and party size. Adults must be at least one; children can be zero. Let guests select a place or “Help me choose.” Keep budget range, interests and length preferences optional and distinguish per-person from group budgets if displayed.
3. **Step 2 – Contact details.** Require name and email. Keep phone/WhatsApp and additional notes optional unless a verified business process needs them. Add a brief privacy notice with a link. Any permission to receive promotional email is a separate optional control.
4. Put trip context in the enquiry URL as a public slug, e.g. `/plan-your-safari/?trip=example-safari`. Allow one context source from itinerary, destination, experience or general visit. Resolve it server-side against published objects. Render the readable label and store authoritative ID and title at submission. Treat request and hidden form values as untrusted. A retired or unknown context falls back to the generic form with no trip asserted. Never put guest information in a query string.
5. Use dedicated labelled fields, an accurate step indicator, a Back control that preserves values, and useful autocomplete attributes. Validate per step and again on submit. On error, show a summary and associate error text with each field. On mobile, avoid layout shifts and focus the new step heading after a deliberate step change.
6. Apply server-side validation, the supported anti-spam configuration, and appropriate request throttling at the site level. Exclude prepopulated form pages from full-page cache unless context and nonce handling are verified under cache. Confirm entries remain protected in WP Admin. Publish no real entry exports.
7. Persist the entry first, then send the assigned planner a notification with a reference. Use authenticated transactional mail with an approved From address and the visitor as Reply-To. Send the guest a short acknowledgement and show a success state giving the next step. Confirm notification failures can be retried or handled from the stored entries. Disable the submit control while pending and reconcile duplicate submissions without creating duplicate leads.
8. Keep detailed requirements such as flight availability, room combinations and rate adjustments for the advisor's response. Define routing and fallback owner for leads before go-live. Store first-party attribution such as UTM source, medium, campaign and originating public page when approved and available. Keep marketing consent and enquiry handling as separate records where applicable.

## Hosted Forminator configuration

1. In Forminator, publish a two-step form using the fields and rules above. Enable stored submissions, AJAX submission, the inline confirmation, and the approved spam protection. Use normal page rendering for this form so the verified trip context is present when it renders.
2. Add three distinct Hidden fields for context type, context ID, and context title. Use blank custom defaults. Copy their field IDs and the published form ID to **Settings → Safari Planner**. The planner template embeds the configured form, so the editorial Page only needs its title and any introductory copy.
3. In the planner notification, include the three context fields and the submission reference using Forminator's merge fields. Configure the approved recipient, authenticated From address, Reply-To and visitor acknowledgement. Use private WP Admin submissions to confirm the authoritative stored values.
4. Exclude **/plan-your-safari/**, including contextual variants, in cPanel, caching plugins, and any CDN. The plugin also sends no-cache headers. Test the form from two different published itineraries after deployment.

## Conversion measurement and operations

| Event | When to fire | Allowed context |
| --- | --- | --- |
| `safari_view` | Detail page rendered | Public trip ID or slug |
| `safari_filter_apply` | Visitor submits new filter selection | Public term slugs and duration band |
| `enquiry_cta_click` | Primary contact or planner action | CTA placement and public content ID |
| `enquiry_start` | First meaningful interaction with planner | Context type, public trip ID and surface |
| `enquiry_step_complete` | First step validation succeeds | Step number and context type |
| `enquiry_submit_success` | Unique accepted lead confirmed by the form | Public trip ID and completion surface. Deduplicate by a non-PII event key. |

The browser emits a scs:analytics event after statistics consent through the WordPress Consent API. Connect the approved analytics listener to its event detail, which contains public context only. Test that moving back and forward or receiving repeated success callbacks does not emit the same step or submission event twice.

Respect the site's configured consent behavior. Keep names, email addresses, phones, free-text messages and personal travel details out of analytics and tracking URLs. Track WhatsApp and phone clicks as contact intent, separately from a stored web lead. Review submissions, qualified leads, delivery failures and form abandonment with the business owner. Establish a baseline before considering a longer planner.

## Enquiry acceptance checks

- After cPanel deployment, check the general, trip-specific, and small-screen destination flows in the hosted browser. For submissions, the owner uses clearly marked test data and the approved notification route. Verify the saved entry, trip context, planner notification, and visitor acknowledgement.
- Use standalone regression tests for validation, storage and notification failure handling, duplicate submissions, and event deduplication where applicable. Stub external delivery. On the hosted site, check flexible dates, invalid email, refresh, Back between steps, and the success state without interrupting live delivery.
- Test form cache with two different trip contexts. Verify editing the incoming trip slug cannot set a private or unpublished trip. Ensure error, success and focus states work with a keyboard and screen reader.
