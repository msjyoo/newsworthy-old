#!/bin/bash

command -v php >/dev/null 2>&1 || { echo >&2 "I require php but it's not installed.  Aborting."; exit 1; }

echo "Running test suite curated/article_text ..."

#tempfile=$(mktemp)
#trap 'rm -f -- "$tempfile"' INT TERM HUP EXIT

for dir in curated/article_text/*; do
    if [[ -d "$dir" && ! -L "$dir" && "$dir" != "curated/article_text/00000-domain-accessyyyy-mm-dd-articletype" ]]; then
        echo -n "Running test $dir ... "

        #cat "$dir/article.txt" | unix2dos >> "$tempfile"
        diffout=$(php -r "
        require_once __DIR__.'/vendor/autoload.php';

        echo sekjun9878\Newsworthy\Newsworthy::extractText(
            sekjun9878\Newsworthy\createDOMDocumentFromHTML(
                file_get_contents('$dir/page.html')
            )
        );

        exit;
        " | dos2unix | diff "$dir/article.txt" -)

        if [[ "$diffout" ]]; then
            echo "FAIL"
            echo "================================================================="
            echo "$diffout"
            echo "================================================================="
            echo
            echo
        else
            echo "OK"
        fi
    fi
done

#rm "$tempfile"