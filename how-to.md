# Types of Web Articles

There are types of news articles, depending on their difficulty to extract content from.

## 1. Fully Structured + Metadata

This is the easiest kind of web article to extract content from.
HTML5 tags are properly used, headers are properly marked,
and extra contents (such as ads) are marked with class / id properties.

These type of articles also have metadata embedded within them,
usually as an SEO measure to allow search engines to index their content.

## 2. Fully Structured

This is the same as above, except metadata is not provided and must be
manually determined from the HTML5 content. Metadata may still be
provided by an RSS feed.

## 3. Semi-Structured

This is where things get tricky. While data is still in hierarchy using
HTML tags, there are unnecessary information included in the article text
that is indistinguishable from the rest of the article just by looking
at the HTML tags.

For example, advertisement that is included as part of the article text,
or author information (e.g. _The author XXX YYY is a graduate of ZZZ university..._)

This type of articles require natural language processing
to properly determine which texts belong to the article.

## 4. Unstructured

This the same as throwing text to the algorithm and telling it to figure out
what text belongs to the article. Very hard to get right even for a
human.

## 5. Obfuscated

This is when the page is intentionally obfuscated to prevent scraping, or
the author has given no consideration to scraping algorithms.

Flash websites / pure AJAX websites are included in this category.

# Filtering Unnecessary Content

Many articles include unnecessary content. Some web pages include text
such as "Read More" or "Subscribe" in the same tag as their regular content
making them hard to distinguish from the article text. These content need 
to be filtered out.

# Using Visual Data

While some unstructured / semi-structured web articles lack headings and
proper tags to distinguish the article from unnecessary content, most
of the times CSS is used to style them in a different way than the rest of the article.

Same goes for headers - even if the same tag is used, they are often bold
or font size is increased to indicate to a visual viewer that the text is a
heading.

While fully rendering a page visually just to extract text like a human 
would is a bit excessive, a similar effect can be mimic'd by just seeing 
which CSS apply to each text.
