@AGENTS.md

## Claude Code notes

- Install the Grav skills for plugin or Admin2 work: `/plugin marketplace add getgrav/grav-skills`, then `/plugin install grav-skills@grav-skills`.
- To manage a running site remotely, copy `.mcp.json.example` to `.mcp.json` (git-ignored) and fill in `GRAV_API_KEY`.
- After editing any `blueprints/blocks/*.yaml`, run `php user/themes/maw-starter/bin/maw.php sync` and `lint` before you finish.
- Verify visual changes at `/styleguide` in light and dark mode, and verify agent output with `/<route>.md`.
