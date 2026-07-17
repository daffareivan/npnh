# Dependencies

## Brand Intro Animation

No new frontend package was added for the NPNHCREATIVE brand intro animation.

The application frontend is currently Laravel Blade with Alpine.js and Tailwind CSS. Framer Motion is a React animation library, so installing it without a React runtime would add unused JavaScript and would not be the best fit for this stack.

The intro uses:

- Alpine.js for session-aware state and reduced-motion checks.
- CSS keyframes for the logo reveal, glow, waveform, page fade, slide, and scale transitions.
- `sessionStorage` key `npnhcreative_intro_shown` to show the intro once per browser session.

This keeps the animation lightweight, compatible with the existing stack, and easy to maintain.
