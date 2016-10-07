<?php

namespace sekjun9878\Newsworthy;

class Newsworthy
{
    public static function extractText(\DOMDocument $document): string
    {
        return static::extractTextWithDebugInfo($document)->text;
    }

    public static function extractTextWithDebugInfo(\DOMDocument $document): \stdClass
    {
        $document = removeElementsByTagName($document, 'script');
        $document = removeElementsByTagName($document, 'style');

        // 1. First, get the leaf text of every node ignoring some textual elements
        //$textElements = (new \DOMXPath($document))->query("//div | //p | //li");

        $textElements = selectAllLeafNodesIgnoring($document, $ignore = ["a", "span", "strong", "i"]);

        // Filter elements without any textContent because why not
        $textElements = array_filter($textElements, function ($x) use ($ignore) {
            /** @var \DomElement $x */
            return (trim($x->textContent) !== "") and (!in_array($x->nodeName, $ignore));
        });

        // 2. Normalise the data so that details like "span" are ignored and treated as the parent node
        $pathNameIndexedTexts = [];

        /*
         * First, we strip tags like span from the xpath. That is because some of the article text we want
         * could be contained in <span> for example. We want to keep those.
         */
        foreach($textElements as $n)
        {
            /** @var \DomElement $n */
            $path = $n->parentNode->getNodePath();
            $path = preg_replace('/\/(?:span|li|ul|p|text\(\))\[?\d*\]?/', "", $path);

            $pathNameIndexedTexts[$path][] = $n->nodeValue;
        }

        /*
         * Then we count the number of "the" in each of the different Indexed XPaths.
         * This is a nice heuristic to determine which one is the likely article text.
         */
        $s = array_map(function ($x, $key) {
            return [substr_count(implode("\r\n", $x), "the"), $x, $key]; // here(1232213534)
        }, $pathNameIndexedTexts, array_keys($pathNameIndexedTexts));

        usort($s, function ($a, $b) {
            return $b[0] - $a[0]; // Sort reverse
        });

        // Get the element with the most amount of "the", and get its full node. defined here(1232213534) 1=full text
        $e = $s[0][1];

        // Trim each paragraph
        $e = array_map(function ($x) {
            return trim($x);
        }, $e);

        // Filter out the ones that are empty after trimming
        $e = array_filter($e);

        $output = new \stdClass();
        $output->text = implode("\r\n\r\n", $e)."\r\n";
        $output->rootPath = $s[0][2];

        return $output;
    }
}

function createDOMDocumentFromHTML(string $html): \DOMDocument
{
    // Enable user error handling, see http://php.net/manual/en/function.libxml-use-internal-errors.php
    $prev = libxml_use_internal_errors(true);

    $doc = new \DOMDocument();

    if (!$doc->loadHTML($html)) {
        foreach (libxml_get_errors() as $error) {
            /** @var \libXMLError $error */
            throw new \RuntimeException("libXML Error: {$error->level} {$error->column} {$error->message} {$error->line}");
        }

        libxml_clear_errors();
    }

    libxml_use_internal_errors($prev);

    return $doc;
}

/**
 * @param \DOMDocument|\DOMElement $doc
 * @param string $name
 *
 * @return \DOMDocument|\DOMElement
 */
function removeElementsByTagName($doc, string $name)
{
    if(!($doc instanceof \DOMDocument) and !($doc instanceof \DOMElement))
    {
        throw new \InvalidArgumentException("Argument 1 must be either DOMDocument or DOMElement");
    }

    $elements = $doc->getElementsByTagName($name);
    for ($i = $elements->length; --$i >= 0; ) {
        $href = $elements->item($i);
        $href->parentNode->removeChild($href);
    }

    return $doc;
}

/**
 * Returns all leaf nodes, ignoring the tag names specified in $ignore
 *
 * @param \DOMDocument|\DOMElement $doc
 * @param string[] $ignore
 *
 * @return \DOMDocument[]|\DOMElement[]
 */
function selectAllLeafNodesIgnoring($doc, array $ignore)
{
    if(!($doc instanceof \DOMDocument) and !($doc instanceof \DOMElement))
    {
        throw new \InvalidArgumentException("Argument 1 must be either DOMDocument or DOMElement");
    }

    if(count(array_filter($ignore, function($x) { return is_string($x); })) !== count($ignore))
    {
        throw new \InvalidArgumentException("Argument 2 must be an array of strings");
    }

    $output = [];

    foreach($doc->getElementsByTagName('*') as $node)
    {
        /** @var \DOMElement $node */

        // NOTE: Don't use ->childNodes here - that also includes DOMText
        $childNodesNotIgnored = array_filter(iterator_to_array($node->getElementsByTagName('*')), function ($x) use ($ignore) {
            /** @var \DOMElement $x */
            return !in_array($x->nodeName, $ignore);
        });

        $numChildNodesNotIgnored = count($childNodesNotIgnored);

        // If this node contains sub-nodes that are not in the ignore list
        if($numChildNodesNotIgnored)
        {
            // Skip processing this node - this is not an allowable leaf node
            continue;
        }

        $output[] = $node;
    }

    return $output;
}
