newsworthy
==========

Project in Development!

This is a library to extract structured data from web news articles.

# Features (All TODO)

- Extract plain text from news articles
- Extract structured markup from news articles (including headings, etc.)
- Extraction excludes fluff (inline advertisements, etc.)
- Extraction uses visual cues from CSS where structure cannot be determined using HTML tags alone
- Extraction uses heuristics and NLP to determine article boundaries instead of excluding hard-coded strings
- Fully tested against many curated cases

# Comparision with other libraries

## Newspaper

# Goals

Use heuristics only - no hardcoded strings for sanitation.
All extraction techniques should use properties intrinsic to how documents
are laid out, not based on hardcoded values.

E.g. instead of filtering out all 'div's with the 'comment' class, use
heuristics to determine which section would be the article (for example,
by counting the words and measuring them)

The technique should be applicable to most pages without modification.
What's the point of using a library if it requires human intervention anyway?

# License

The MIT License
