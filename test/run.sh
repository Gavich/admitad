#!/bin/bash

set -e

# Absolute path to this script.
scriptPath=$(pwd -P)/$0
# Absolute path this script dir.
scriptDir=`dirname ${scriptPath}`

if [ ! -f ${scriptDir}/config.sh ]; then
    echo "Error: config.sh not found in $scriptDir";
    exit;
fi

. ${scriptDir}/config.sh;

PHAR=${scriptDir}/tools/n98-magerun.phar

MAGENTO_ROOT=${scriptDir}/magento
if [ ! -d ${MAGENTO_ROOT} ]; then
    mkdir ${MAGENTO_ROOT}
fi;

cd ${MAGENTO_ROOT}

rsync -ax --exclude='test/' --exclude='.git/' ${scriptDir}/../ . -q
rm -rf ./app/etc/local.xml

#custom config for magerun
cp ${scriptDir}/n98-magerun.yaml ./app/etc/

# Setup magento
echo "Magento installation"

${PHAR} --root-dir="${MAGENTO_ROOT}" install --installationFolder="${MAGENTO_ROOT}" \
    --dbHost="${magentoDbHost}" --dbUser="${magentoDbUser}" --dbPass="${magentoDbPass}" --dbName="${magentoDbName}" \
    --installSampleData=no --useDefaultConfigParams=yes --magentoVersionByName="magento-ce-1.8.1.0" \
    --baseUrl="${magentoUrl}" --noDownload

${PHAR} cache:clean

# @TODO run tests here

# Remove files and database
${PHAR} uninstall --force
