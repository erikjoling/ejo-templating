Gedachte experiment
---

Waarom kan ik niet doen: 
- register_component('site', [element], [content]);
- update_component_element('site site-header', [tag => a] )
- remove_component('site site-header')
- add_component('site site-header')

API
---
Het gaat erom dat de API betrouwbaar is. Hoe het er _under the hood_ aan toe gaat doet er niet toe.

1. At start
// Register component without being context aware (no template or parent)
register_component( 'site', element, content );

2. Change component based on template and parents
// Change component based on context (template or parent)
filter_component( 'site', [element, content])

3. During template output
// Render component with the registered and changed components
render_component( 'site' );

Het doel
---
Ik heb eigenlijk niet de ambitie om een fullscale templating system te maken. Want het WordPress eco-system is aan veranderingen onderhevig momenteel. Wel wil ik standaarden voor mezelf hanteren. En waar mogelijk wil ik die standaarden technisch voor mezelf afdwingen. Denk aan BEM classes. Denk aan een eenvoudige mogelijkheid om volgordes aan te passen. Het liefst met een eenduidige API, maar een secundaire mogelijkheid via filters is ook prima. 

Maar hoe zorg ik dat ik de volgorde van plural-posts aanpasbaar maak? En wil ik de loop ook in de components opnemen?

Component composition
---

one logical stack with multiple templates.

Container Component
Specialized Component

Component definition
---
There is no exact definition of a component in our system. Maybe we should call it component-template, because the same component can have different content. Think of plural-posts in the post-loop. Also the element of the component can differ depending on the context. 

Structure
---

[
    name: site
    content: [
        [
            name: site-header,
            element: [...],
            content: [
                [
                    name: site-branding,
                    content: function
                ],
                [
                    name: site-nav,
                    content: function
                ]

            ]
        ],
        [
            name: site-main,
            element: [...],
            content: [
                [
                    name: page,
                    content: [...]
                ]
            ]
        ],
        [
            name: site-footer,
            content: false
        ]
    ]
]
