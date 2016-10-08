# How to make DOMText as leaf nodes work?
2016-10-09 (3)

So here's a problem. Some older news websites don't use leaf nodes
to differentiate their paragraphs. Instead, I've found one that does
something like this:

```
<div id="printable-area">
ADELAIDE, Some very important news about the biggest storm.
<br></br>
<br></br>
Caused many damages blah blah.
<br></br>
<br></br>
Third paragraph and text.
<span id="useless-element"></span>
</div>
```

Now obviously, the "select-leaf-nodes" method won't work here because
there are no leaf nodes that contain the relevant text. Instead, what
we'll have to do is treat the text between <br> and another <br> as
its own leaf node.

```
<div id="printable-area">
ADELAIDE, Some very important news about the biggest storm.
<br></br>

=> ADELAIDE, Some very important news about the biggest storm.

// Ignoring child elements also works with this new method
<br></br>
Caused many damages blah blah <a>link here</a>.
<br></br>

=> Caused many damages blah blah link here.

// But remember that other websites are structured
<p>Second paragraph</p>
=> Second paragraph
```

How to go about achieving this?

# Why use fastText?
2016-10-09 (2)

Well, it's CPU bound so it will work well on traditional servers.
Its also simple to use and I don't know much about machine learning.

# I shoud really describe how this works
2016-10-09 (1)

So, here's how this algorithm is supposed to work because I keep on
forgetting. Most news articles are like this:

```html
<div class="printableArea">
    <p>First paragraph <a>with link</a>.</p>
    <p>Second paragraph</p>
    <div class="advertisement"> READ MORE: Subscribe now! </div>
    <p>Third paragraph <i>italic</i> here</p>
</div>
<div class="comment-section">
    <div class="comment-container">
        <p>Nice article!</p>
    </div>
    <div class="comment-container">
        <p>Bad article!</p>
    </div>
</div>
```

So how do we extract the text? First, we get all the leaf nodes - nodes
that do not have any child nodes in them. So for example, these would
be selected as leaf nodes:

```
<p>Second paragraph</p>
<p>Bad article!</p>
<div class="advertisement"> READ MORE: Subscribe now! </div>
```

We also ignore some elements as child nodes while selecting leaf nodes.
For example, links a used a lot within news articles but we don't
want to ignore paragraphs just because they have links. So, These leaf
nodes would be selected:

```
<p>First paragraph <a>with link</a>.</p>
<p>Third paragraph <i>italic</i> here</p>
```

But not this one because it's not a leaf node, and doesn't have a child
node that is allowable (as an example; of course this is configurable):

```
<p>Imaginary paragraph <div>with link</div>.</p>
```

So, now we have an array of leaf nodes with paragraphs and we want to
select those that are part of the article. How can we do this? First,
we get the XPath of the parent node of those paragraphs because the
article text is usually always grouped under a single parent node.

So, we have these XPaths:
```
/.../div[1] Leading text: First paragraph with link. (2nd text: Second paragraph, etc.)
/.../div[2]/div[1] Leading text: Nice article! (2nd text: None)
```

Then, we just count the number of "the" in the paragraphs under those 
XPaths to determine which one is the actual news article.

Then, we use machine learning to determine what text belongs to the
article and what is just fluff (e.g. inline advertisements).

One thing that's left to be desired with this technique is that it
doesn't work on blog posts or other more complex pages, but as said
from the last post they would require structured extraction.

# Plaintext is not suitable for extracting web articles
2016-10-07 (1)

In trying to extract both blog posts and news articles to plaintext,
I've come across a problem - blog posts can't be extracted to text.
They just contain too much 'not-plaintext-content' like images with
labels and dot points and headings.

With regular news articles, its entirely possible to make meaning
out of them without the images. But with blog posts, they are a critical
part of the information which can't be represented in plaintext.

Not even a human can meaningfully reduce blog posts to plaintext soo..

I think the solution would be to restrict plaintext extraction to news
articles only, however that can be done.
