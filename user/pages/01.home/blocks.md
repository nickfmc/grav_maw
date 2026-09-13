---
title: Home
menu: Home
description: 'A fast, AI-ready Grav 2.1 starter: block-based pages, design tokens and Markdown output for agents.'
content_position: none
blocks:
  -
    type: hero
    hero:
      layout: centered
      eyebrow: 'Grav 2.1 starter'
      heading: 'Websites your team and your AI agents can build together'
      text: 'Block-based pages stored as plain YAML, a token-driven design system, and every page readable as Markdown at `/page.md`.'
      buttons:
        -
          label: 'Start a project'
          url: /contact
          style: primary
        -
          label: 'See every block'
          url: /styleguide
          style: secondary
  -
    type: logos
    logos:
      heading: 'Built on proven, open tools'
      items:
        -
          name: Grav
        -
          name: Twig
        -
          name: Markdown
        -
          name: YAML
        -
          name: MCP
  -
    type: features
    anchor: features
    features:
      eyebrow: 'Why this starter'
      heading: 'Scales from a one-pager to anything'
      intro: 'Every section is a block with a schema, a template and its own CSS. Add blocks without touching the rest.'
      columns: 3
      style: cards
      items:
        -
          icon: fa-cubes
          title: 'Block library'
          text: '16 ready blocks: hero, features, pricing, FAQ, collections, forms and more.'
        -
          icon: fa-robot
          title: 'AI friendly'
          text: "Predictable files, generated docs, and Grav's MCP server let agents build pages safely."
        -
          icon: fa-palette
          title: 'Design tokens'
          text: 'Re-skin a whole site from theme settings or a few CSS variables.'
        -
          icon: fa-bolt
          title: 'No build step'
          text: 'Vanilla CSS with cascade layers. Only the CSS for blocks on the page is loaded.'
        -
          icon: fa-universal-access
          title: Accessible
          text: 'Semantic landmarks, skip link, focus styles and reduced-motion support.'
        -
          icon: fa-magnifying-glass
          title: 'SEO built in'
          text: 'Open Graph, JSON-LD and FAQ schema come out of the box.'
  -
    type: media-text
    background: alt
    media-text:
      eyebrow: 'Content model'
      heading: 'Pages are just YAML lists'
      text: 'Edit blocks in Admin2, in your code editor, or let an agent write them through the API. It is all the same file.'
      checklist: "Git-friendly diffs for every change\nSame schema powers Admin2 forms and AI docs\nUnknown block types fail safely\n"
      image_side: right
      buttons:
        -
          label: 'Read the docs'
          url: /about
          style: secondary
  -
    type: stats
    align: center
    stats:
      items:
        -
          value: '16'
          label: 'Blocks included'
        -
          value: '0'
          label: 'Build steps'
        -
          value: 100%
          label: 'Markdown readable'
        -
          value: '2.1'
          label: 'Grav version'
  -
    type: testimonials
    testimonials:
      heading: 'What teams say'
      items:
        -
          quote: 'We launched a 20-page site in a week, and the AI agent wrote half the sections.'
          name: 'Jordan Lee'
          role: 'Agency owner'
        -
          quote: 'Clients edit blocks in the admin without breaking the design.'
          name: 'Sam Rivera'
          role: 'Web designer'
        -
          quote: 'Finally a CMS where content is just files we can review.'
          name: 'Priya Shah'
          role: Developer
  -
    type: collection
    collection:
      heading: 'From the blog'
      source: /blog
      limit: 3
      buttons:
        -
          label: 'All posts'
          url: /blog
          style: secondary
  -
    type: faq
    background: alt
    faq:
      heading: Questions
      items:
        -
          question: 'Do I need Node or a build step?'
          answer: 'No. CSS and JS are plain files. Enable the asset pipeline in `system.yaml` for production bundling.'
        -
          question: 'How do AI agents edit the site?'
          answer: 'Locally they edit files using `AGENTS.md`. Remotely they use the `grav-mcp` server through the API plugin.'
        -
          question: 'Can I still use modular pages?'
          answer: 'Yes. Every block also has a `modular/<type>` template and blueprint.'
  -
    type: cta
    cta:
      heading: 'Ready to build something?'
      text: 'Clone the starter, rename the theme tokens, and ship.'
      buttons:
        -
          label: 'Get in touch'
          url: /contact
---
