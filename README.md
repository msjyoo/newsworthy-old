# some-kind-of-news-extraction-algorithm
Project in development!

Goals:

Use heuristics only - no hardcoded strings for sanitation.
All extraction techniques should use properties intrinsic to how documents
are laid out, not based on hardcoded values.

E.g. instead of filtering out all 'div's with the 'comment' class, use
heuristics to determine which section would be the article (for example,
by counting the words and measuring them)

The technique should be applicable to most pages without modification.
What's the point of using a library if it requires human intervention anyway?
