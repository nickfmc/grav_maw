# MAW Grav 2.1 Starter

Starting repo for new Mountain Air Web projects: **Grav 2.1.2**, **Admin2**, and the **`maw-starter`** theme, a block-based page builder built on design tokens with no build step. AI agents can work on it safely.

- `AGENTS.md` has the rules, file map and recipes for AI agents and developers. Start there.
- `docs/ai/blocks.md` documents every block's fields plus a YAML example (generated).
- `/styleguide` renders every block on one page, for visual QA.
- Any page can be read as Markdown at `/<route>.md` (Grav 2.1 Markdown for Agents).

## New project checklist

1. Clone this repo and rename it. Delete `.git` and run `git init` if you want a clean history.
2. Enable the required PHP extensions (see *Local PHP* below), then run `php -S localhost:8000 system/router.php`.
3. Open http://localhost:8000/admin and create the administrator account (the account file is git-ignored).
4. In **Admin → Configuration → Site**, set the title, author and description.
5. In **Admin → Themes → MAW Starter**, set the logo, accent colour, radius, fonts, header button and footer.
6. Replace the demo pages in `user/pages/`. Keep `styleguide/` (hidden, noindex) for QA, or delete it.
7. Production: in `user/config/system.yaml` turn on `assets.css_pipeline`/`js_pipeline` and set `errors.display: false`.

## Local PHP (Windows)

`C:\php` currently has **no `php.ini`**, so GPM and Grav can't load their extensions. Create one once:

```bash
copy C:\php\php.ini-development C:\php\php.ini
```

Then uncomment `extension_dir = "ext"` and `extension=curl`, `fileinfo`, `gd`, `intl`, `mbstring`, `openssl`, `sodium`, `zip`, and set `memory_limit = 512M`.

## Theme CLI

```bash
php user/themes/maw-starter/bin/maw.php new-block team-grid   # scaffold a block
php user/themes/maw-starter/bin/maw.php sync                  # regenerate admin field, modular blueprints, docs
php user/themes/maw-starter/bin/maw.php styleguide            # rebuild /styleguide from block examples
php user/themes/maw-starter/bin/maw.php lint                  # validate blocks + page frontmatter
php user/themes/maw-starter/bin/maw.php normalize             # convert flat blocks to the Admin2 shape
```

## AI tooling

**Local agents** (Claude Code etc.) read `AGENTS.md` / `CLAUDE.md`. For plugin and Admin2 work, install the official Grav skills:

```bash
claude plugin marketplace add getgrav/grav-skills
```

**Remote agents over MCP.** Grav's `grav-mcp` server drives a site through the API plugin (installed):

1. Generate an API key: `php bin/plugin api keys:generate --user=<admin> --name="Claude MCP"`
2. Copy `.mcp.json.example` to `.mcp.json` (git-ignored) and paste the key.
3. Restart Claude Code. The agent gets page, media, config, user and package tools, with the same permissions as that user.

## Roadmap

- **`maw-builder` plugin**: a visual Admin2 block editor (web component at `admin-next/fields/blocks.js`) with drag-reorder, block picker and live preview. It will write the same `blocks:` YAML, so files, MCP and git stay compatible.

---

Grav documentation: https://learn.getgrav.org · Grav 2 AI docs: https://learn.getgrav.org/2/migration/ai-assisted-development

