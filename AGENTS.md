# AGENTS.md: Grav 2.1 MAW Starter

This is the guide for AI coding agents (Claude Code, Cursor, Codex, etc.) and for humans. Read it before changing anything.

## What this repo is

A starter for new client sites on **Grav 2.1** (flat-file PHP CMS). It includes **Admin2** (a SvelteKit admin that talks to the API plugin) and the **`maw-starter`** theme, a block-based page builder built on design tokens with no build step.

- PHP 8.3+ is required (dev machine has 8.4). Twig 3. Symfony 7. No database.
- Content = Markdown files with YAML frontmatter in `user/pages/`.
- Every page is also served as Markdown for agents: `/<route>.md`, `/index.md` for home, or `Accept: text/markdown`.

## Repo map

| Path | What | Edit? |
|---|---|---|
| `system/`, `vendor/`, `bin/` | Grav core | **Never** |
| `user/plugins/*` (installed) | GPM packages (admin2, api, login, form, email, ...) | No, update via `bin/gpm` |
| `user/config/` | Site config (`system.yaml`, `site.yaml`, `plugins/*.yaml`, `themes/*.yaml`) | Yes |
| `user/pages/` | Content | Yes |
| `user/themes/maw-starter/` | **The theme. Most work happens here** | Yes |
| `user/themes/quark2/` | Stock theme kept for reference | No |
| `docs/ai/` | Reference docs; `blocks.md` is generated | See below |
| `cache/ logs/ tmp/ backup/ images/ assets/` | Runtime, git-ignored | No |

### Theme layout (`user/themes/maw-starter/`)

```
maw-starter.php            Twig helpers only (maw_blocks, maw_media, fa_icon, maw_embed_url, maw_slug, `is maw_medium`)
maw-starter.yaml           theme defaults (brand, header, footer); overridden by user/config/themes/maw-starter.yaml
blueprints.yaml            theme settings form in Admin2
bin/maw.php                CLI: sync | new-block <type> | lint | styleguide
css/tokens.css             ALL design tokens (--maw-*) + @layer order
css/{base,layout,components,utilities}.css
css/blocks/<type>.css      per-block CSS, auto-loaded only when that block is on the page
css/custom.css             project overrides (unlayered, wins)
js/site.js                 nav toggle, color mode, reveal-on-scroll (progressive enhancement)
templates/partials/base.html.twig   page shell; content MUST stay inside <main id="content">
templates/{default,blocks,blog,item,modular,error}.html.twig   page types
templates/blocks/_render.html.twig  loops a blocks list
templates/blocks/_section.html.twig wrapper implementing shared block settings
templates/blocks/<type>.html.twig   one file per block
templates/modular/<type>.html.twig  generated-style wrappers so modular pages reuse blocks
templates/macros/ui.html.twig       ui.head, ui.buttons, ui.image, ui.rich
blueprints/blocks/<type>.yaml       SOURCE OF TRUTH for a block: title, description, example, fields
blueprints/blocks/_settings.yaml    shared block settings; _buttons.yaml reusable buttons list
blueprints/partials/blocks-field.yaml  GENERATED Admin2 blocks field
blueprints/modular/<type>.yaml         GENERATED modular blueprints
```

## Content model

Page type = the Markdown filename (`blocks.md` uses `templates/blocks.html.twig`).

- `default`: title, Markdown body, then optional `blocks:`
- `blocks`: landing page made of `blocks:` (body shown per `content_position: top|bottom|none`)
- `blog`: lists child `item` pages (`content:` collection in frontmatter)
- `item`: blog post (`date`, `author`, `description`, `image`, `taxonomy.tag`)
- `modular`: children `_name/<block-type>.md`, where each module's frontmatter holds the block's fields

A block has a `type`, flat shared settings, and its **content nested under a key equal to the type**. This is exactly the shape Admin2 reads and writes, so pages stay editable in the admin:

```yaml
blocks:
  - type: features          # must match blueprints/blocks/features.yaml
    background: alt         # shared settings, flat: anchor, background, spacing, width, align, class, reveal, hidden
    features:               # content fields, nested under the type name
      heading: Why us
      columns: 3
      items:
        - { icon: fa-bolt, title: Fast, text: No database. }
```

Templates receive `block` already flattened (settings + content merged), so write `block.heading`, not `block.features.heading`.
If you write content flat by mistake, `bin/maw.php lint` warns and `bin/maw.php normalize` fixes it.
Modular modules are the exception: a module's frontmatter holds the content fields flat.

**The full field reference and a YAML example for every block is in [`docs/ai/blocks.md`](docs/ai/blocks.md).** Check it before writing blocks.
The live example of every block is at `/styleguide`.

## Recipes

### Build a page
1. Create `user/pages/NN.slug/blocks.md` (`NN.` prefix = visible in nav and sets its order; omit it to hide the page).
2. Write frontmatter `title`, `description`, `blocks:` using types and examples from `docs/ai/blocks.md` (nested shape).
3. Put images in the same folder and reference them by filename (`image: hero.jpg`).
4. Run `php user/themes/maw-starter/bin/maw.php lint` (and `normalize` if it warns), then check `/<route>` and `/<route>.md`.

### Add a new block type
1. Run `php user/themes/maw-starter/bin/maw.php new-block team-grid`. It creates the template, blueprint, css and modular wrapper, then syncs.
2. Edit `blueprints/blocks/team-grid.yaml`. Field names start with `.` (relative to the list item). Fill in `description` and `example`.
3. Edit `templates/blocks/team-grid.html.twig`. Keep the `{% embed 'blocks/_section.html.twig' %}` wrapper and import macros *inside* `{% block inner %}`.
4. Style it in `css/blocks/team-grid.css` inside `@layer blocks { .block--team-grid ... }`, using tokens only.
5. Run `bin/maw.php sync`, then `bin/maw.php styleguide`, then `bin/maw.php lint`, and view `/styleguide`.

### Change the look
Set brand values (accent, radius, fonts, container) in Admin2 → Themes → MAW Starter, or `user/config/themes/maw-starter.yaml`. For deeper changes override `--maw-*` tokens in `css/custom.css`. Never hard-code colours in block CSS.

### Add a form
Define it under `forms:` in the page frontmatter (Form plugin syntax) and add a `contact` block with `form: <name>`. See `user/pages/04.contact/blocks.md`. Set `cache_enable: false` on form pages.

## Grav 2 rules (these break things if ignored)

- **Twig 3**: use `items|filter(i => i.visible)` rather than `for x in y if cond`. Use `{% apply spaceless %}` instead of `{% spaceless %}`. `replace` takes a map: `|replace({'a': 'b'})`.
- **No arbitrary PHP functions in Twig.** Add a helper in `maw-starter.php` (`onTwigInitialized`) instead.
- Twig written inside page content is **sandboxed**. Theme templates on disk are trusted.
- `page.header` is an object. Use `page.header|array` before `merge`.
- Macros imported at the top of a template are **not** visible inside `{% embed %}` blocks. Import inside the block.
- Subscribe to events statically in `getSubscribedEvents()`. Never gate on `isAdmin()` in `onPluginsInitialized` (Admin2 sets admin context later).
- Keep main content inside `<main id="content">`. Use `<nav>`/`<aside>` for chrome, since Markdown output drops them.
- In Admin2 blueprints, fields inside an `element` save under the element key, and a `fieldset` inside an element binds to the wrong path. Keep block blueprints flat (no fieldsets). Add shared lists with a form-level `import@`, as `_buttons` does.
- Don't edit generated files (`blocks-field.yaml`, `blueprints/modular/*`, `docs/ai/blocks.md`, `user/pages/styleguide/blocks.md`). Run `sync`/`styleguide` instead.
- Escape output. Only use `|raw` on Markdown-rendered or trusted HTML.
- Admin2 custom fields are web components at `admin-next/fields/<name>.js` (the planned `maw-builder` plugin).

## Commands

The local PHP needs the curl, mbstring, openssl, gd, zip and intl extensions enabled (see README "Local PHP").

```bash
php -S localhost:8000 system/router.php                 # dev server (admin at /admin)
php bin/grav clearcache                                  # after config/blueprint changes
php bin/gpm install <package>                            # install plugins
php user/themes/maw-starter/bin/maw.php sync             # regenerate derived block files
php user/themes/maw-starter/bin/maw.php lint             # validate blocks + pages
php user/themes/maw-starter/bin/maw.php normalize        # convert flat blocks to the Admin2 shape
curl http://localhost:8000/about.md                      # what an agent sees
```

## Remote agents (MCP)

Grav's MCP server (`grav-mcp`, npm) exposes about 70 tools (pages, media, config, users, packages) through the API plugin with the same permissions as a user. Setup is in `README.md` and `.mcp.json.example`. When writing pages over MCP, use the same `blocks:` frontmatter documented above.

## Official Grav skills

For plugin work install the Grav team's Claude Code skills:
`/plugin marketplace add getgrav/grav-skills` then `/plugin install grav-skills@grav-skills`
(grav-api-integration, grav-api-admin-next-integration, grav-admin-ui-polish, grav-translations, grav-plugin-review, grav-2-migration, grav-release).

