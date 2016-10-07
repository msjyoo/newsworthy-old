<?php

namespace sekjun9878\Newsworthy\Test;

use PHPUnit\Framework\TestCase;
use function sekjun9878\Newsworthy\selectAllLeafNodesIgnoring;
use function sekjun9878\Newsworthy\createDOMDocumentFromHTML;

class NewsworthyTest extends TestCase
{
    /**
     * @dataProvider provideTestSelectAllLeafNodesIgnoring
     *
     * @param string $html
     * @param string[] $ignore
     * @param $expectedLeafNodes
     */
    public function testSelectAllLeafNodesIgnoring($html, $ignore, $expectedLeafNodes)
    {
        $leaves = selectAllLeafNodesIgnoring(createDOMDocumentFromHTML($html), $ignore);

        $this->assertEquals($expectedLeafNodes, array_map(function ($node) {
            /** @var \DomElement $node */
            return $node->getNodePath();
        }, $leaves));
    }

    public function provideTestSelectAllLeafNodesIgnoring()
    {
        $ignoreDefault = ["a", "span", "strong", "i"];

        return [
            [ <<<'NEWSWORTHY_NOWDOC'
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <title>Title</title>
  </head>
  <body>
    <div class="news-main">
    <p>Today in the news</p>
    </div>
  </body>
</html>
NEWSWORTHY_NOWDOC
                , $ignoreDefault,
                [
                    "/html/head/meta",
                    "/html/head/title",
                    "/html/body/div/p"
                ]
            ],
        ];
    }
}