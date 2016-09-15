<?php

// enable user error handling
libxml_use_internal_errors(true);

// load the document
$doc = new DOMDocument();

if (!$doc->loadHTML(file_get_contents("test5.html"))) {
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

var_dump($d);

//foreach($doc->childNodes as $n)
//{
//    /** @var DomNode $n */
//    var_dump($n->nodeName, $n->nodeValue);
//}
