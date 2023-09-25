#!/bin/bash

if [[ -z "${1}" ]]; then
    echo "A version number must be supplied"
    exit 1;
fi

./setversion.sh ${1}
git commit -a -m "Set version to ${1}"
git tag v${1}
git push --tags
