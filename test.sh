#!/bin/bash

command -v php >/dev/null 2>&1 || { echo >&2 "I require php but it's not installed.  Aborting."; exit 1; }
command -v colordiff >/dev/null 2>&1 || { echo >&2 "I require colordiff but it's not installed.  Aborting."; exit 1; }

echo "Running test suite curated/article_text ..."
echo "Note: For diffs, additions mean = additions in the output, so they should be removed from the output"

tempfile=$(mktemp)
#trap 'rm -f -- "$tempfile"' INT TERM HUP EXIT

for dir in curated/article_text/*; do
    if [[ ! -d "$dir" && ! -L "$dir" ]]; then continue
    elif [[ "$dir" == *"/"?????"ignore-"* ]]; then echo "IGNORING test $dir ... IGNORED"; continue
    elif [[ "$dir" == "curated/article_text/00000-domain-accessyyyy-mm-dd-articletype" ]]; then continue
    else
        echo -n "Running test $dir ... "

        cat "$dir/article.txt" | dos2unix > "$tempfile" # Use dos2unix as unix2dos makes diff output ^M for the \r
        diffout=$(php -r "
        require_once __DIR__.'/vendor/autoload.php';

        echo sekjun9878\Newsworthy\Newsworthy::extractText(
            sekjun9878\Newsworthy\createDOMDocumentFromHTML(
                file_get_contents('$dir/page.html')
            )
        );

        exit;
        " | dos2unix | diff -u "$tempfile" - | colordiff)

        if [[ "$diffout" ]]; then
            echo "FAIL"
            if [[ $* == *--diff* ]]; then
                echo "================================================================="
                echo "$diffout"
                echo "================================================================="
                echo
                echo
            fi
        else
            echo "OK"
        fi
    fi
done

#rm "$tempfile"
