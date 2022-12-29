#!/bin/sh

rm -rf dist/

## copy the custom ApiRendered into the vendor
cp ApiRenderer.php vendor/yiisoft/yii2-apidoc/templates/bootstrap/ApiRenderer.php

LUYA=vendor/luyadev/luya-core
ADMIN=vendor/luyadev/luya-module-admin/src
CMS=vendor/luyadev/luya-module-cms/src
SUITE=vendor/luyadev/luya-testsuite
YII=vendor/yiisoft/yii2
HELPERS=vendor/luyadev/yii-helpers/src
OUTPUT=dist

./vendor/bin/apidoc api $LUYA,$ADMIN,$CMS,$YII,$SUITE,$HELPERS $OUTPUT --interactive=0 --exclude="./vendor/yiisoft" --page-title="api.luya.io"