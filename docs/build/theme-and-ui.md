# Theme and UI build guide

## Set up the theme

1. Create `wp-content/themes/savannah-crest/` as a standalone block theme. Add `style.css` with `Theme Name: Savannah Crest`, `Text Domain: savannah-crest`, and a version. Add `templates/index.html` as the required fallback with the shared header and footer and a core Query Loop using the current query. [WordPress theme structure](https://developer.wordpress.org/themes/core-concepts/theme-structure/).
2. Add `theme.json` with a schema supported by the installed WordPress version. Define palette, typography, spacing, layout widths, buttons, links, and form baseline styles there. Define the header and footer template parts and their areas there.
3. Create `parts/header.html`, `parts/footer.html`, and `templates/front-page.html`. Add the remaining route templates in the build order in [Templates and publishing](templates-and-publishing.md). Keep reusable editorial sections in `patterns/` and use core blocks for ordinary copy. Use focused CSS for custom components such as filters and itinerary accordions. Keep server-rendered safari blocks and their data logic in `scs-core`.
4. Deploy the approved [brand files](../../Brand.md) into the theme runtime asset directory. Header uses the horizontal espresso lockup against cream. Footer uses the horizontal or stacked cream-and-gold reverse on espresso. Use the provided SVG favicon and ICO fallback. Use either core Site Icon output with the PNG or theme-managed head links, with a single owner for each icon size.
5. After the owner deploys the theme through cPanel, check it in the hosted **Appearance → Themes** screen and preview where available. The owner activates **Savannah Crest** as part of the rollout and configures the static Home page and published navigation. Check **Appearance → Editor** for saved template or global-style overrides. Version intentional template, part, or style changes in Git. Compare and back up database overrides before resetting them. Verify the affected pages and assets in the browser after activation. [Style hierarchy](https://developer.wordpress.org/themes/core-concepts/global-settings-and-styles/) · [Template hierarchy](https://developer.wordpress.org/themes/templates/template-hierarchy/).

## Visual system

| Element | Launch direction |
| --- | --- |
| Palette | Espresso `#2B1A14` for type and primary actions. Light Cream `#F7F1E5` for main surfaces and reverse text. Antique Gold `#B08A48` for accents and decorative detail. Use the approved pairing rules in [Brand.md](../../Brand.md). |
| Typography | Proposed UI pairing is Instrument Serif for display and Inter for body, controls and numerals. Self-host licensed WOFF2 files and include license text. Use system fallbacks and confirm kerning and reading comfort in the prototype before finalizing. The approved logo is supplied artwork. |
| Layout | Target a 76rem wide content frame and a 42–44rem long-form reading column. Use fluid page gutters starting at 1.25rem and generous section spacing. Use one column by default on narrow screens, two at roughly 48rem and three at 70rem where content fits. Adjust at the width where content breaks. |
| Hierarchy | One H1 per page. Use large photos, a single dominant action per section, substantial whitespace and a restrained border system. Content readability is the priority when type sits over photography. |
| Primary button | Espresso fill and cream text on cream surfaces. Use a visibly reversed version in dark sections. Make focus, hover, active, pending and disabled states apparent. |
| Images | Use cleared wildlife, landscapes, camp and guide photography. Record source and permissions. Use a wide editorial crop for hero images and a consistent 4:3 crop for trip cards. Set focal position, width and height. Give meaningful photos descriptive alt text and use empty alt on decorative imagery. |

## Reusable components and states

- **Header and navigation.** Logo linked to home, Safaris, Destinations, Experiences, Travel Guide, About, and the primary **Plan your safari** action. On large screens use a compact accessible menu with one level of well-curated links. On mobile add a labelled menu toggle, visible focus, Escape handling if implemented as a modal, and focus return to its trigger. Closeable panels should expose expanded state.
- **Hero.** Use one strong responsive photograph. Keep copy and CTA as real text. For the homepage, add the labelled trip finder below the headline or immediately after the image. Use a static media area and stable dimensions to avoid layout shift.
- **Safari card.** Image, linked trip title, duration, location, CTA, and rate state. Avoid duplicating multiple keyboard stops for a single destination. Put consistent baseline alignment on cards without forcing fixed heights that clip content.
- **Quote or proof block.** Use real names or authorized attribution and link to the source where possible. Display factual team proof if guest reviews are still unavailable.
- **Itinerary jump bar and accordion.** Jump links use real fragment URLs and account for sticky header height. Use native `details` / `summary` for day rows and FAQs when suitable. Keep the body and accessible names in the HTML.
- **Sticky planning CTA.** Use one contextual CTA on mobile itinerary pages. Reserve room for it, handle bottom safe area insets and the on-screen keyboard, and suppress it on the form and wherever it would cover controls.
- **Footer.** Include real contact details, destination and guide links, and policy links. Add verified credentials only when supplied by the business.

## UI acceptance checks

- Verify widths 320, 375, 768, 1024 and 1440 CSS pixels, mobile portrait and landscape, and 200% zoom. No horizontal page scrolling, cropped headings, or overlapping CTAs.
- Tab through menus, cards, form, and accordion. Ensure a discernible focus indicator, descriptive links, useful order and status text. Aim for 44 × 44 CSS px touch targets for primary controls.
- Check light, dark, and image-backed sections for readable contrast. Do not use gold-only copy on cream. Honor reduced-motion settings and avoid auto-rotating hero slides.
- Confirm the icon appears in an actual browser tab and the header and footer use the correct source variants. Verify that templates still render when secondary media or optional fields are absent.
