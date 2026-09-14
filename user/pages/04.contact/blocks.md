---
title: Contact
description: 'Get in touch about a project.'
cache_enable: false
content_position: none
forms:
    contact:
        action: /contact
        fields:
            name:
                label: Name
                type: text
                autocomplete: name
                validate:
                    required: true
            email:
                label: Email
                type: email
                autocomplete: email
                validate:
                    required: true
            message:
                label: Message
                type: textarea
                validate:
                    required: true
            honeypot:
                type: honeypot
        buttons:
            submit:
                type: submit
                value: 'Send message'
        process:
            save:
                fileprefix: contact-
                dateformat: Ymd-His-u
                extension: txt
                body: "{% include 'forms/data.txt.twig' %}"
            message: 'Thanks! We will be in touch shortly.'
            reset: true
blocks:
    -
        type: contact
        contact:
            eyebrow: Contact
            heading: "Let's talk about your project"
            text: 'Tell us what you are building. We reply within one'
            form: contact
            details:
                -
                    icon: fa-envelope
                    label: Email
                    value: hello@example.com
                    url: 'mailto:hello@example.com'
                -
                    icon: fa-location-dot
                    label: Studio
                    value: 'Mountain Air, Earth'
    -
        type: cards
        cards:
            heading: 'Our work asda'
            columns: 4
            items:
                -
                    title: 'Project one'
                    text: 'Short description.'
                    url: /work/one
                    meta: Branding
                    image: one.jpg
                    link_label: View
                -
                    title: 'Another one'
                    text: 'This is cool'
---

