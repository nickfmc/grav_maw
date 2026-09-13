---
title: Re-skinning a site with design tokens
date: 2026-09-01 09:00
author: Mountain Air Web
description: Change one accent color and a radius setting, and every block follows.
taxonomy:
  tag: [design, css]
---

All theme styles read from CSS custom properties such as `--maw-accent` and `--maw-radius`.

===

```css
:root {
  --maw-accent: #0f766e;
  --maw-radius: 1rem;
}
```

Put overrides in `css/custom.css` or set the accent in the theme settings.
