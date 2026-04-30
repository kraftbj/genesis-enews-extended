# Working on Genesis eNews Extended

Notes for agents and contributors working in this repository. Read this before opening a PR.

## What this is

A WordPress widget plugin that renders newsletter subscription forms (MailChimp, AWeber, ConvertKit, Flodesk, etc.) for sites using the Genesis Framework. It is published on WordPress.org and used in production on a lot of long-running Genesis sites, so backward compatibility is a recurring concern.

## Layout

- `plugin.php` — plugin bootstrap, header, version constant, default filter wiring.
- `class-bjgk-genesis-enews-extended.php` — the entire widget (extends `WP_Widget`). Form rendering, admin UI, save/sanitize all live here.
- `readme.txt` — WordPress.org plugin readme (changelog, Stable tag, Upgrade Notice).
- `tests/` — PHPUnit tests, run via `phpunit`.
- `.phpcs.xml.dist` — PHPCS config; uses the Jetpack coding standard.
- `.github/workflows/deploy.yml` — on GitHub Release publish, deploys to the WordPress.org SVN repo via `10up/action-wordpress-plugin-deploy`.

## Compatibility floor

- **PHP:** 5.4+ (per `.phpcs.xml.dist` and `readme.txt`). Don't use short array syntax assumptions, null coalescing, typed properties, etc. — sniffs will catch most of it but it's easy to forget.
- **WordPress:** 4.9.6+. Don't reach for APIs newer than that without a fallback.
- **Genesis:** runs on top of Genesis but should fail gracefully when Genesis isn't present.

## Versioning

The version number lives in **three** places and they all need to move together when prepping a release:

1. `plugin.php` — both the docblock `@version` and the plugin header `Version:` line.
2. `class-bjgk-genesis-enews-extended.php` — the docblock `@version`.
3. `readme.txt` — the `Stable tag:` line. **But see the next section.**

### Stable tag rule

**Don't bump `Stable tag` in feature or fix PRs.** Bump `@version` and the plugin header `Version:` line, but leave `readme.txt`'s `Stable tag:` at the currently released version.

Why: `Stable tag` is what WordPress.org serves to users. Bumping it before the release is actually cut would publish unreleased code immediately. Flipping `Stable tag` is a separate, deliberate step done in the release commit ("Prepare X.Y.Z release") right before tagging.

## Scoping PRs (issue 162)

Scope each PR by **intent**, not by what happened to be touched. Don't bundle categories.

| Category | What belongs | What doesn't |
|---|---|---|
| **Dead code removal** | Removing demonstrably unreachable code or retired-service integrations (FeedBurner, MailPoet 2). | Anything that changes rendered HTML for a working configuration. |
| **Security fix** | Escaping, sanitization, nonce, capability checks, input validation. | Markup or attribute changes for legitimate input. |
| **Behavior / markup change** | Anything that alters rendered HTML attributes, IDs, classes, structure, or admin UI. **Requires a "Backward compatibility" section in the PR description** calling out CSS selectors, JS selectors, or theme integrations that may break. | (This is its own PR; don't tuck it into a cleanup or security PR.) |
| **Refactor** | Pure code reorganization with no behavior change. | Any change a user could observe. |

Bisectability gut-check before opening a PR: *"If a user reports a regression in this area in six months, can someone bisect to a single PR and immediately understand what was intended?"* If the PR mixes categories, the answer is no — split it.

This rule exists because PR 159 (titled "Remove dead code and fix security issues") also stripped the long-standing `id="subbox"` / `id="subbox1"` / `id="subbox2"` / `id="subbutton"` form attributes. The IDs weren't dead and weren't a security concern — sites used them as CSS hooks. It shipped in 2.3.0, broke styling on real sites, and had to be reverted in 2.3.1. Issue 162 has the full retrospective.

### Special caution: visible HTML output

Form `id`s, classes, attribute names, attribute order, and surrounding structure are all part of the plugin's de facto public API because themes and site CSS target them directly. Treat changes to them like a breaking API change:

- Don't remove or rename them as part of a "cleanup".
- If a change is genuinely needed, propose it as its own PR with explicit before/after HTML and a "Backward compatibility" section.
- When in doubt, keep the legacy markup and add new hooks alongside it.

## Filter hooks

The widget exposes filters that themes and other plugins extend. Public filters are part of the contract — don't rename, remove, or change their argument shape without a release note and a backward-compatibility plan. As of 2.4.0:

- `gee_text` / `gee_after_text` — raw user input, run before `wpautop`.
- `gee_text_content` / `gee_after_text_content` — post-`wpautop`, mirrors core's `widget_text_content` split. `do_shortcode` is registered on these by default (guarded with `has_filter` so sites that already added it don't double-register).
- `genesis-enews-extended-args` — full widget args before render.

If you add a new filter, document it in `readme.txt`'s changelog with a one-line description.

## Coding standards

- PHPCS via `composer install` then `vendor/bin/phpcs` (rules in `.phpcs.xml.dist`, ruleset is Jetpack's).
- Text domain is `genesis-enews-extended`. Sniffs enforce it.
- Escape on output (`esc_attr`, `esc_url`, `esc_html`, `wp_kses` with the appropriate allowed-tags array) — don't trust stored widget options.

## Tests

PHPUnit tests live in `tests/` and run via `phpunit` against the WordPress test suite. If you add behavior, add or extend a test. If you change rendered output, snapshot the change in a test so future regressions are obvious.

## Commit messages

This machine has a global git hook that enforces:

1. A conventional-commit prefix: `feat:`, `fix:`, `docs:`, `style:`, `refactor:`, `test:`, `chore:`, `perf:`, `ci:`, `build:`, `revert:`, `add:`, `update:`, `remove:`.
2. **75 characters max for the entire message body**, not just the subject. Multi-paragraph commit messages will be rejected.

Keep commits to a single short line. Put longer explanations in the PR description.

Examples:
- `fix: restore Hidden Fields attributes stripped in 2.3.0`
- `feat: allow shortcodes in widget text areas`
- `docs: clarify backward-compatibility expectations`

Don't use `--no-verify` on regular commits.

## Issue / PR cross-references

- Use `Fixes #123` when the PR completely closes the issue. Use `See #123` only when more changes are still needed.
- Don't write `#123` inline in prose unless you really mean to link to issue/PR 123 — GitHub auto-linkifies it. Use "issue 123" or "PR 123" in narrative text. (This bites hardest in numbered lists, where `#1` becomes a wrong link.)
- Don't use `@name` in commit messages or PR/issue prose outside code blocks — GitHub treats it as a user mention.

## Releasing

1. On a release-prep branch, bump every version string **including** `readme.txt`'s `Stable tag:` to the new version, and finalize the changelog and Upgrade Notice in `readme.txt`.
2. Merge to `develop`, then to whatever branch is current `main`-equivalent for the release.
3. Cut a GitHub Release with the new tag. The `deploy.yml` workflow takes it from there: builds the plugin zip, pushes to the WordPress.org SVN repo (using `SVN_USERNAME` / `SVN_PASSWORD` secrets), tags the SVN release, and attaches the zip back to the GitHub Release.
4. Verify the new version appears on https://wordpress.org/plugins/genesis-enews-extended/.

## Useful references

- Plugin home: https://kraft.blog/genesis-enews-extended/
- WordPress.org listing: https://wordpress.org/plugins/genesis-enews-extended/
- Issue 162 (PR-scoping retrospective): https://github.com/kraftbj/genesis-enews-extended/issues/162
