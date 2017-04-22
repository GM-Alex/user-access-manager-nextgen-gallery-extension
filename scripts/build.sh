#!/usr/bin/env bash
set -e
PLUGIN="user-access-manager-nextgen-gallery-extension"
PLUGIN_ROOT="$(cd "$( dirname "${BASH_SOURCE[0]}" )/.." && pwd)"
PLUGIN_BUILDS_PATH="${PLUGIN_ROOT}/builds/${PLUGIN}"

if [[ -d ${PLUGIN_BUILDS_PATH} ]]; then
    rm -R ${PLUGIN_BUILDS_PATH}
fi

mkdir -p ${PLUGIN_BUILDS_PATH}
GIT_IGNORE_FILE=$(cat ${PLUGIN_ROOT}/.gitignore)
EXCLUDES=${GIT_IGNORE_FILE//[[:cntrl:]]/,}

if [[ ${EXCLUDES} != '' ]]; then
    EXCLUDES="${EXCLUDES},"
fi

EXCLUDES="${EXCLUDES}README.md,scripts,.travis.yml"
eval "rsync -av ${PLUGIN_ROOT}/* ${PLUGIN_BUILDS_PATH} --exclude={${EXCLUDES}}"