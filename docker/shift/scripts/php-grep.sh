#!/bin/sh

method="$1"
pattern="$2"

shift 2

grep -rIl $method --include \*.php -e "$pattern" "$@" | while read -r file ; do
    php -w "$file" | grep -q $method -e "$pattern"

    if [ $? -eq 0 ]; then
        echo $file
    fi
done
