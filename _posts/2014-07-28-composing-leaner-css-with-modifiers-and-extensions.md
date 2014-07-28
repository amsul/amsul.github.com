---
layout: post
title: Composing leaner CSS with modifiers and extensions
meta: CSS files have a much smaller footprint when multiple classes are created for component modifiers and extensions are used across components.
---

When following [BEM naming conventions](http://bem.info/method/definitions/), people find it odd how verbose the markup can become with class definitions. In an effort to “fix” this, some have suggested using the `extend` method of preprocessors to copy selectors over to the styles they extend.

However, a problem arises when extending styles *within* components versus *across*. To accentuate the difference, I created two demos of basic components that look exactly the same, but use varying approaches.


### Single classes + extend *within* components ([full example](http://jsbin.com/firipu/1/edit?html,css,output))

```css
.input {
    border: 1px solid #ccc;
}
.input_large {
    &:extend(.input all);
    font-size: 20px;
}
.input:disabled {
    background: #eee;
}
.textarea {
    &:extend(.input all);
    min-height: 2em;
}
.textarea_large {
    &:extend(.textarea all);
    &:extend(.input_large all);
}
```

```html
<input class="input">
<input class="input_large">
<textarea class="textarea"></textarea>
<textarea class="textarea_large"></textarea>
```

The markup requires only one class per element since each modifier extends the base styles and each related component extends it’s relative components’ styles.

It seems simple enough so far and the markup is succinct.


### Multiple classes + extend *across* components ([full example](http://jsbin.com/gubile/1/edit?html,css,output))

```css
.input {
    border: 1px solid #ccc;
}
.input_large {
    font-size: 20px;
}
.input:disabled {
    background: #eee;
}
.textarea {
    &:extend(.input all);
    min-height: 2em;
}
.textarea_large {
    &:extend(.input_large all);
}
```

```html
<input class="input">
<input class="input input_large">
<textarea class="textarea"></textarea>
<textarea class="textarea textarea_large"></textarea>
```

Now, we’re only extending styles when we want to share specific properties across components. By passing the responsibility of variation styling over to the markup, we avoid getting caught up in a slew of extensions. As a result, the styles are easier to read and the markup is much more flexible.


### “&hellip;but my markup gets cluttered with too many classes!”

Yes, potentially. But what about your stylesheets?

When using preprocessors, people tend to ignore the output completely; forgetting that it’s the CSS that’s actually served to and parsed by the browser.

So how much does the output size between the two approaches vary?

#### The HTML

Single classes: `6.69 kB`
Multiple classes: `7.06 kB`

This is obviously expected – there are more classes in the markup so it’s about 0.05 times larger when using multiple classes.

#### The CSS

Single classes: `6.43 kB`
Multiple classes: `1.89 kB`

<big>:open_mouth:</big>&hellip; yeah, almost 3.5 times larger when using single classes!


### So why’re the resulting CSS sizes so drastically different?

Well, this is what the CSS for __single classes__ looks like ([full result](http://jsbin.com/firipu/2/edit?css)):

```css
.input,
.input_large,
.textarea,
.textarea_large {
    border: 1px solid #ccc;
}
.input_large,
.textarea_large {
    font-size: 20px;
}
.textarea,
.textarea_large {
    min-height: 2em;
}
.input:disabled,
.input_large:disabled,
.textarea:disabled,
.textarea_large:disabled {
    background: #eee;
}
```

And this is what the CSS for __multiple classes__ looks like ([full result](http://jsbin.com/gubile/3/edit?css)):

```css
.input,
.textarea {
    border: 1px solid #ccc;
}
.input_large,
.textarea_large {
    font-size: 20px;
}
.textarea {
    min-height: 2em;
}
.input:disabled,
.textarea:disabled {
    background: #eee;
}
```

See the problem?

When using a single class, all variations (both within the component and across components) need all the styles of that component applied to them – in addition to the variation’s own styles. For each new declaration, the CSS becomes *exponentially* larger than using the multiple classes approach.

The additional overhead in the markup is definitely worth the trade-off of having styles with a significantly smaller footprint. If you attempt chasing the single classes rule, your styles are likely to degrade into a barrage of extensions.

However, there *are* scenarios where the same collection of modifier classes are repeatedly used together. That’s when “aliases” are extremely beneficial.


### Multiple classes aided with aliases^[*](#think_twice)

Aliases, or convenience classes, are compositions of a collection of modifiers within a component. They’re basically shorthands and do not have any styles of their own.

Buttons are an example of a component that tends to have many variations. And we don’t want to do this for each button composition:

```css
.button {}
.button_rounded {}
.button_pointing {}
.button_green {}
.button_blue {}
```

```html
<button class="button button_rounded button_pointing button_green">Primary button</button>
<button class="button button_rounded button_pointing button_blue">Secondary button</button>
```

To introduce an “alias”, we need to explicitly mark the class as one. It should strictly behave as an alias to a composition of modifier classes and never have any styles of it’s own.

Here, I’m using two leading leading dashes (`--`) to denote a class as an alias:

```css
.button {}
.button_rounded {}
.button_pointing {}
.button_green {}
.button_blue {}
.--button_primary {
    &:extend(.button_rounded);
    &:extend(.button_pointing);
    &:extend(.button_green);
}
.--button_secondary {
    &:extend(.button_rounded);
    &:extend(.button_pointing);
    &:extend(.button_blue);
}
```

```html
<button class="button --button_primary">Primary button</button>
<button class="button --button_secondary">Secondary button</button>
```

This approach gives us the benefit of both grounds: concise class declarations and a smaller CSS footprint.

[Ben Smithett](https://twitter.com/bensmithett/) recently [wrote about this](http://bensmithett.com/bem-modifiers-multiple-classes-vs-extend/), suggesting a “one modifier, one state” rule. I wouldn’t go as far as to say one modifier all the time since that can end up in too many `extend`s and bloat your stylesheets (keep reading below). But the general concept of leveraging multiple classes holds true.


<a name="think_twice"></a>
### * Think twice about crafting an alias

Don’t go crazy with aliasing all possible combinations of modifiers. And don’t alias an alias either. That’s the whole point of this post.

This would be extremely bad:

```css
.button {}
.button_large {}
.button_small {}
.button_rounded {}
.button_pointing {}
.button_green {}
.--button_primary {
    &:extend(.button_rounded);
    &:extend(.button_pointing);
    &:extend(.button_green);
}
.--button_primaryLarge {
    &:extend(.--button_primary);
    &:extend(.button_large);
}
.--button_primarySmall {
    &:extend(.--button_primary);
    &:extend(.button_small);
}
```

```html
<button class="button --button_primary">Primary button</button>
<button class="button --button_primaryLarge">Primary button</button>
<button class="button --button_primarySmall">Primary button</button>
```

Instead, leverage mutiple classes in your markup:

```css
.button {}
.button_large {}
.button_small {}
.button_rounded {}
.button_pointing {}
.button_green {}
.--button_primary {
    &:extend(.button_rounded);
    &:extend(.button_pointing);
    &:extend(.button_green);
}
```

```html
<button class="button --button_primary">Primary button</button>
<button class="button --button_primary button_large">Primary button</button>
<button class="button --button_primary button_small">Primary button</button>
```

It’s much more flexible to handle variation styling at the markup level, and future you will thank you for it.
