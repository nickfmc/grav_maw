---
title: About
subtitle: 'How this starter is put together'
description: 'How the MAW Starter theme organises templates, blocks, tokens and AI tooling.'
blocks:
    -
        type: hero
        hero:
            layout: split
            eyebrow: wersdf
            heading: 'Build faster with Msfasda'
            text: 'A flat-file CMS your team and your AI agents can both work with.awdasdasd'
            image: about-hero.png
            image_alt: 'Product screenshot'
            buttons:
                -
                    label: 'Get started'
                    url: /contact
                    style: primary
                -
                    label: 'Learn more'
                    url: '#features'
                    style: secondary
    -
        type: faq
        background: soft
        faq:
            eyebrow: FAQ
            heading: 'FAQ Section Here ok here now!'
            intro: 'This is the intro this is cool'
            items:
                -
                    question: Questionasd
                    answer: 'Answeasd asd r'
                -
                    question: 'This is Q 2'
                    answer: 'Answer two. answer here'
                -
                    question: 'Another One'
                    answer: 'Can type while its updating.'
            schema: true
            open_first: true
---

This is a regular **default** page: a title, Markdown content, and optional blocks below it.

## The three layers

1. **Content** lives in `user/pages` as Markdown with YAML frontmatter.
2. **Blocks** are sections defined by a schema (`blueprints/blocks/<type>.yaml`) and a template (`templates/blocks/<type>.html.twig`).
3. **Design tokens** in `css/tokens.css` drive every colour, space and radius.

## For AI agents

Every page is available as Markdown. Try [this page as Markdown](/about.md). The repository root has an `AGENTS.md` with the rules and recipes an agent needs.

> [!TIP]
> GitHub-style alerts work too, thanks to the bundled plugin.
