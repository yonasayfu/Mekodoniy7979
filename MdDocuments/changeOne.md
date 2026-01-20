# Change Log – Phase 0 Kickoff Prep

## 1. Documentation Alignment
- Updated `MdDocuments/finializeProject.md` to highlight the urgent guest experience gaps (broken CTAs, placeholder assets, RBAC drift) and rewrote **Phase 0** to focus on welcome-flow fixes, RBAC/seed refresh, and stability work before deeper features.
- Rebuilt `MdDocuments/upcomingtasklist.md` so Phase 0 tasks mirror the refreshed blueprint (welcome CTA cleanup, pre-sponsorship → donation hand-off, elder gallery UX, sidebar/routing audit, RBAC + seeder refresh, asset cleanup, smoke tests, and audit-ready exports).

## 2. Welcome Page Enhancements
- Prevented hero title/description overlap by wrapping the slide copy in a centered translucent card, increasing line-height, and stacking CTAs responsively to keep the text legible on short viewports (`resources/js/pages/Welcome.vue`).
- Added a looping muted background video (`public/images/6096-188704568_small.mp4`) behind the hero content with stronger overlays so copy stays readable while showcasing Mekodonia visuals.
