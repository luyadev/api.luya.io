#!/bin/sh

rm -rf dist/

LUYA=vendor/luyadev/luya-core
ADMIN=vendor/luyadev/luya-module-admin/src
CMS=vendor/luyadev/luya-module-cms/src
SUITE=vendor/luyadev/luya-testsuite
YII=vendor/yiisoft/yii2
HELPERS=vendor/luyadev/yii-helpers/src
OUTPUT=dist

./vendor/bin/apidoc api $LUYA,$ADMIN,$CMS,$YII,$SUITE,$HELPERS $OUTPUT --interactive=0 --exclude="./vendor/yiisoft" --page-title="LUYA"

rm -rf dist/index.html
cp dist/luya-boot.html dist/index.html