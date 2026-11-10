
# Project Instructions

## Stack and architecture

- Laravel, PHP 8.5, Livewire, Blade, Tailwind CSS 4, Vite, Alpine.js, and vanilla TypeScript/JavaScript.

## Project commands

- Repository root: the directory containing AGENTS.md and the Docker Compose configuration.
- Laravel root: app/. Run PHP, Artisan, Composer, and frontend commands from this directory.
- Run Docker Compose and repository-level Git commands from the repository root.
- Use the package manager specified by app/package.json and its lockfile.
- Prefer the project's existing scripts over invoking tools directly.

### PHP and Laravel

- PHP syntax: `cd app && php -l app/Http/Controllers/ExampleController.php`
- Targeted tests: `cd app && php artisan test --filter=TestName`
- Blade compilation: `cd app && php artisan view:cache`
- PHP formatting check: `cd app && vendor/bin/pint --test`

### Frontend

- Frontend build: `cd app && pnpm run build`
- Formatting check: `cd app && pnpm run format:check`
- Apply formatting: `cd app && pnpm run format`
- Linting: `cd app && pnpm run lint`
- Use Oxfmt for supported frontend files and Oxlint for JavaScript/TypeScript linting.
- Use Laravel Pint for PHP formatting. Do not run Oxfmt or Oxlint on PHP files.

## Livewire and Blade

- Anonymous components: `app/resources/views/components/`; class-backed components: `app/app/View/Components/`.
- Prefer Livewire for server-driven interactive components and Blade for rendering.
- Keep database queries and business logic out of Blade templates.
- Reuse existing Blade components before writing new markup, especially layouts, forms, buttons, typography, cards, and pagination.
- Use existing component APIs rather than duplicating markup, styling, or behavior.
- Avoid duplicating state between Livewire and JavaScript unless necessary.
- For link elements `<a href="">` styled like buttons, use the `<x-button href=""/>` component; don't recreate the button style on a native link.
- For smaller buttons, add the `size="sm"` attribute to the `<x-button/>` component.
- Don't place any class, including size classes, on icon components inside `<x-button/>` components.

## Validation

Run Docker commands from the repository root.

```bash
# Inspect recent logs
docker compose logs --tail=100 app

# Run targeted PHP tests
docker compose exec -e APP_ENV=testing app php artisan test --filter=TestName

# Run full PHP test suite when justified
docker compose exec -e APP_ENV=testing app php artisan test

# Build frontend assets
cd app && pnpm build
```

- Assume containers are already running. Do not restart, rebuild, or install dependencies unnecessarily.
- Always use `APP_ENV=testing` for PHP tests.
- Add or update tests for new behavior, bug fixes, or meaningful logic changes when existing coverage is insufficient.
- Prefer targeted tests before running the full suite.
- Styling, markup-only, and documentation changes do not require new tests.
- Run frontend builds only when relevant to modified assets or compilation behavior.
- Avoid repeated validation unless changes or failures justify it.
- Report validation failures or checks that could not be performed.

## Browser and visual testing

- For frontend styling and visual verification, follow the `frontend-design` skill.
- Minimize browser interactions and screenshot analysis unless necessary for correctness.

## Agent efficiency and scope

- Use targeted searches (`rg`, `rg --files`). Read relevant files only; expand investigation when necessary.
- Avoid repeated file reads, commands, repository-wide searches, and unrelated audits.
- Never generate images.

## Communication

- Be concise, technical, and action-oriented.
- After changes, briefly summarize what changed, validation performed, and any remaining issues.
- Update README.md when the changes affect its correctness.
