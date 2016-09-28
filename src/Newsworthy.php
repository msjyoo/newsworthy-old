<?php

namespace sekjun9878\Newsworthy;

class Newsworthy
{
    public static function extractText(\DOMDocument $document): string
    {
        $document = removeElementsByTagName($document, 'script');
        $document = removeElementsByTagName($document, 'style');

        /*
         * Source for the XPath: http://stackoverflow.com/a/6399988/1349450
         *
         * Why do this? Because I want the text value of only the leaf nodes, and their values only
         * excluding anything inside their <span> descendants etc.
         */

        $XPath = new \DOMXPath($document);

        // 1. First, get the leaf text of every node
        $textElements = $XPath->query('//*/text()'); // TODO: Fix it so that it includes <a>link</a> etc.

        // Filter elements without any textContent because why not
        $textElements = array_filter(array_map(function ($x) {
            /** @var \DomElement $x */
            return $x;
        }, iterator_to_array($textElements)), function($x) { return trim($x->textContent) !== ""; });

        //var_dump(array_filter(array_map(function ($x) {
        //    /** @var DomElement $x */
        //    return [$x->getNodePath(), trim($x->textContent)];
        //}, iterator_to_array($textNodes)), function ($x) { return $x[1] !== ""; }));exit;

        // 2.

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
        $s = array_map(function ($x) {
            return [substr_count(implode("\r\n", $x), "the"), $x];
        }, $pathNameIndexedTexts);

        usort($s, function ($a, $b) {
            return $b[0] - $a[0]; // Sort reverse
        });

        // Get the element with the most amount of "the", and get its full node. (0 = number of "the", 1 = full text)
        $e = $s[0][1];

        // Trim each paragraph
        $e = array_map(function ($x) {
            return trim($x);
        }, $e);

        // Filter out the ones that are empty after trimming
        $e = array_filter($e);

        return implode("\r\n\r\n", $e)."\r\n";
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

function removeElementsByTagName(\DOMDocument $doc, string $name): \DOMDocument
{
    $elements = $doc->getElementsByTagName($name);
    for ($i = $elements->length; --$i >= 0; ) {
        $href = $elements->item($i);
        $href->parentNode->removeChild($href);
    }

    return $doc;
}
