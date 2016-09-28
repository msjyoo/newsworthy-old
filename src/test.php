<?php

ini_set('xdebug.var_display_max_data', 99999);

require_once __DIR__."/../vendor/autoload.php";

function createDOMDocumentFromHTML(string $html): DOMDocument
{
    // Enable user error handling, see http://php.net/manual/en/function.libxml-use-internal-errors.php
    $prev = libxml_use_internal_errors(true);

    $doc = new DOMDocument();

    if (!$doc->loadHTML($html)) {
        foreach (libxml_get_errors() as $error) {
            /** @var libXMLError $error */
            throw new \RuntimeException("libXML Error: {$error->level} {$error->column} {$error->message} {$error->line}");
        }

        libxml_clear_errors();
    }

    libxml_use_internal_errors($prev);

    return $doc;
}

function removeElementsByTagName(DOMDocument $doc, string $name): DOMDocument
{
    $elements = $doc->getElementsByTagName($name);
    for ($i = $elements->length; --$i >= 0; ) {
        $href = $elements->item($i);
        $href->parentNode->removeChild($href);
    }

    return $doc;
}

function applyCssAllWithDownload(DOMDocument $doc): DOMDocument
{
    // name link, if rel=stylesheet, select href
    $cssToInlineStyles = new \TijsVerkoyen\CssToInlineStyles\CssToInlineStyles();
}

//$doc = createDOMDocumentFromHTML(file_get_contents($pa = glob(__DIR__."/../curated/article_text/00002-*/page.html")[0]));
$doc = createDOMDocumentFromHTML(file_get_contents($pa = glob(__DIR__."/test6.html")[0]));

$doc = removeElementsByTagName($doc, 'script');
$doc = removeElementsByTagName($doc, 'style');

$XPath = new DOMXPath($doc);

/*
 * Source for the XPath: http://stackoverflow.com/a/6399988/1349450
 *
 * Why do this? Because I want the text value of only the leaf nodes, and their values only
 * excluding anything inside their <span> descendants etc.
 */

$textNodes = $XPath->query('//div/text() | //p/text() | //li/text()');

$elements = array_filter(array_map(function ($x) {
    /** @var DomElement $x */
    return $x;
}, iterator_to_array($textNodes)), function($x) { return trim($x->textContent) !== ""; });

$pathNames = [];
$d = [];

foreach($elements as $n)
{
    /** @var DomElement $n */
    $path = $n->parentNode->getNodePath();
    $path = preg_replace('/\/(?:span|li|ul|text\(\))\[?\d*\]?/', "", $path);

    $pathNames[] = $path;
    $d[$path][] = $n->nodeValue;

    // TODO: score of shortest node path with number of elements?
}

$b = array_count_values($pathNames);

asort($b);

//var_dump($b);
//var_dump($d);exit;

//var_dump(array_map(function ($x) {
//    return $x[0];
//}, $d));

$s = array_map(function ($x) {
    return [substr_count(implode("\r\n", $x), "the"), $x[0], $x];
}, $d);

usort($s, function ($a, $b) {
    return $b[0] - $a[0]; // Sort reverse
});

$e = $s[0][2];

$e = array_map(function ($x) {
    return trim($x);
}, $e);

$e = array_filter($e);

//file_put_contents(substr($pa, 0, -10)."/article.txt", implode("\r\n\r\n", $e)."\r\n");
var_dump(implode("\r\n\r\n", $e)."\r\n");

//foreach($doc->childNodes as $n)
//{
//    /** @var DomNode $n */
//    var_dump($n->nodeName, $n->nodeValue);
//}
