---
name: frontend-design
description: Implement and refactor frontend styling, CSS, Tailwind CSS 4, Figma designs, design tokens, layouts, responsive behavior, and visual consistency in the Laravel, Blade, Livewire, and Alpine.js application. Use when tasks involve styling, design implementation, layout changes, Figma fidelity, or visual verification.
---

# Frontend Design

## Scope

Apply these instructions when implementing or modifying:

- Figma designs and visual references.
- CSS and Tailwind utilities.
- Design tokens, colors, and themes.
- Layouts, spacing, sizing, and responsive behavior.
- Component presentation and visual consistency.
- Frontend visual validation.

## Design tokens and colors

- Tailwind CSS 4 tokens are defined in `app/resources/css/theme.css`.
- Additional layout-related tokens may be defined in `app/resources/css/base.css`.
- Check the existing CSS entry point and shared styles for additional utilities.
- ALWAYS use existing semantic color tokens defined in `theme.css`. NEVER use Tailwind's default color palette, hardcoded colors, or invented color tokens.
- Use exact token names, including numeric suffixes, as defined in the stylesheet.
- Check `theme.css` for available tokens rather than assuming names.
- Prefer semantic tokens over colors tied to a particular visual appearance.
- Respect existing light/dark theme implementations.
- Avoid redundant `dark:` overrides when tokens already handle themes.
- Don't apply text size like `text-2xl`, `text-lg`, to `<x-heading>` components; use the `variant` prop instead.
- Don't apply radius like `rounded-lg`, `rounded-full`, to elements with the `card` class.

## Figma fidelity

When implementing a design from Figma:

- Treat the provided Figma design as the primary visual reference.
- Respect the intended layout, proportions, typography, spacing, colors, and responsive behavior.
- Reuse existing design tokens and components whenever possible.
- Preserve established layout utilities and wrappers.
- NEVER add horizontal padding, width constraints, or centering utilities to page sections, this is already handled by full-width, breakout and content-grid utilities defined in `base.css`.
- Do NOT add `max-w-*`, `mx-auto`, or extra `px-*` merely to constrain or center content.
- If the design is ambiguous, prefer existing project conventions instead of inventing new ones.

## Layout and responsive behavior

- Prefer using default Tailwind breakpoints (sm, md, lg, xl, 2xl) instead of creating custom ones.
- Avoid unnecessary nested containers.

## Visual validation

- For Figma implementations, compare the result against the provided design when visual testing is available and justified.
- Avoid repeatedly navigating or capturing screenshots when code inspection is sufficient.
