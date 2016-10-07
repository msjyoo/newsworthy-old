#!/usr/bin/env bash

command -v php >/dev/null 2>&1 || { echo >&2 "I require php but it's not installed.  Aborting."; exit 1; }

echo "Debug script by Michael Yoo <michael@yoo.id.au> (c) 2016"
echo "./debug.sh <test_path>"
echo "e.g. ./debug.sh curated/article_text/00002-reuters.com-2016-09-19-newsarticle"
echo

if [[ ! -f "$1/page.html" ]]; then
    echo "Test '$1' not found!"
    exit 1
fi

echo "Running test '$1' with detailed info output..."
echo

php -r "
ini_set('xdebug.var_display_max_data', 99999);

require_once __DIR__.'/vendor/autoload.php';

var_dump(sekjun9878\Newsworthy\Newsworthy::extractTextWithDebugInfo(
    sekjun9878\Newsworthy\createDOMDocumentFromHTML(
        file_get_contents('$1/page.html')
    )
));

exit;
"
