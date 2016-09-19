<?php

// enable user error handling
libxml_use_internal_errors(true);

// load the document
$doc = new DOMDocument();

if (!$doc->loadHTML(file_get_contents(__DIR__."/../curated/article_text/00001-0fps.net-2016-09-16-blog/page.html"))) {
    foreach (libxml_get_errors() as $error) {
        // handle errors here
    }

    libxml_clear_errors();
}

$elements = $doc->getElementsByTagName('script');
for ($i = $elements->length; --$i >= 0; ) {
    $href = $elements->item($i);
    $href->parentNode->removeChild($href);
}

$elements = $doc->getElementsByTagName('style');
for ($i = $elements->length; --$i >= 0; ) {
    $href = $elements->item($i);
    $href->parentNode->removeChild($href);
}

$elements = array_filter(iterator_to_array($doc->getElementsByTagName('*')), function ($element) {
    /** @var DOMNode $element */
    return $element->nodeName === "p" or $element->nodeName === "li";
});

$a = [];
$d = [];

foreach($elements as $n)
{
    /** @var DomNode $n */
    $path = $n->parentNode->getNodePath();
    $path = preg_replace('/\/(?:span|li|ul)\[?\d*\]?/', "", $path);

    $a[] = $path;
    $d[$path][] = $n->nodeValue;

    // TODO: score of shortest node path with number of elements?
}

$b = array_count_values($a);

asort($b);

//var_dump($b);
//var_dump($d);

//var_dump(array_map(function ($x) {
//    return $x[0];
//}, $d));

$s = array_map(function ($x) {
    return [substr_count(implode("\r\n", $x), "the"), $x[0], $x];
}, $d);

usort($s, function ($a, $b) {
    return $b[0] - $a[0]; // Sort reverse
});

file_put_contents(__DIR__."/../curated/article_text/00001-0fps.net-2016-09-16-blog/article.txt", implode("\r\n\r\n", $s[0][2])."\r\n");

//foreach($doc->childNodes as $n)
//{
//    /** @var DomNode $n */
//    var_dump($n->nodeName, $n->nodeValue);
//}
