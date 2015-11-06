---
layout: notepad
title: ARIA
---

### Key resources:

- [http://www.w3.org/TR/wai-aria/usage](http://www.w3.org/TR/wai-aria/usage)
- [http://www.w3.org/TR/wai-aria/roles](http://www.w3.org/TR/wai-aria/roles)
- [http://www.w3.org/TR/wai-aria/states\_and\_properties](http://www.w3.org/TR/wai-aria/states_and_properties)

<hr>

### Rules of building with ARIA:

1. Start by architecting the “landmark” roles.
2. Then build the “document structure” roles.
3. Finally create individual “widget” roles.

\* Do NOT use “abstract roles” as they’re reserved for ontology and do nothing within the accessibility tree.

*Role definitions*: [http://www.w3.org/TR/wai-aria/roles#roles_categorization](http://www.w3.org/TR/wai-aria/roles#roles_categorization)

<hr>

### Rules of employing ARIA:

- When a native HTML element/attribute exists, use that instead of ARIA roles, states, or properties.
- Do not change semantics of native elements: eg `<h1 role=button>`.
- Interactive controls, widgets, etc. that are controlled by click/tap/drag/drop/slide/scroll must also be equivalently controllable via the keyboard.
- The “space” key should alias the action of the “enter” key.
- To pass ARIA validation, the document type is required to be HTML5. It works with other types as well, but to pass validation, make sure to do this.

*Source*: [http://blog.paciellogroup.com/2012/06/html5-accessibility-chops-using-aria-notes/](http://blog.paciellogroup.com/2012/06/html5-accessibility-chops-using-aria-notes/)

<hr>

### Things to keep in mind:

- Not all elements need ARIA roles or attributes attached to them: [http://rawgithub.com/w3c/aria-in-html/master/index.html#rec](http://rawgithub.com/w3c/aria-in-html/master/index.html#rec).
- The “presentation” role removes semantics from element: eg `<h1 role=presentation>` becomes  `<>`. Descendants retain their semantics – unless if there are specific expected children of the element, such as with `table`, `ul`, etc.
- Tabbing order for widgets within widgets goes from parent widget, cascades through descendants until final descendant is reached and then moves on to next parent widget. More on keyboard behavior: [http://www.w3.org/WAI/PF/aria-practices/#aria_ex](http://www.w3.org/WAI/PF/aria-practices/#aria_ex).
- Be careful when [using `role=application`](http://rawgithub.com/w3c/aria-in-html/master/index.html#using-aria-role-application) – unless if you really understand [its purpose and mode of use](http://www.marcozehe.de/2012/02/06/if-you-use-the-wai-aria-role-application-please-do-so-wisely/). If you must use “focus” mode, the counter part is “browse” mode, which is triggered by `role=document`. A good example is Yahoo Mail, which uses `application` for browsing through mail and `document` for reading mail.
