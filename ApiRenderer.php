<?php
/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

namespace yii\apidoc\templates\bootstrap;

use Yii;
use yii\apidoc\helpers\ApiIndexer;
use yii\helpers\Console;
use yii\helpers\FileHelper;

/**
 *
 * @author Carsten Brandt <mail@cebe.cc>
 * @since 2.0
 */
class ApiRenderer extends \yii\apidoc\templates\html\ApiRenderer
{
    use RendererTrait;

    public $layout = '@yii/apidoc/templates/bootstrap/layouts/api.php';
    public $indexView = '@yii/apidoc/templates/bootstrap/views/index.php';


    /**
     * @inheritdoc
     */
    public function render($context, $targetDir)
    {
        $types = array_merge($context->classes, $context->interfaces, $context->traits);

        $extTypes = [];
        foreach ($this->extensions as $k => $ext) {
            $extType = $this->filterTypes($types, $ext);
            if (empty($extType)) {
                unset($this->extensions[$k]);
                continue;
            }
            $extTypes[$ext] = $extType;
        }

        // render view files
        parent::render($context, $targetDir);

        if ($this->controller !== null) {
            $this->controller->stdout('generating extension index files...');
        }

        foreach ($extTypes as $ext => $extType) {
            $readme = @file_get_contents("https://raw.github.com/yiisoft/yii2-$ext/master/README.md");
            $indexFileContent = $this->renderWithLayout($this->indexView, [
                'docContext' => $context,
                'types' => $extType,
                'readme' => $readme ?: null,
            ]);
            file_put_contents($targetDir . "/ext-{$ext}-index.html", $indexFileContent);
        }

        $indexFileContent = $this->renderWithLayout($this->indexView, [
            'docContext' => $context,
            'types' => $this->filterTypes($types, 'app'),
            'readme' => null,
        ]);
        file_put_contents($targetDir . '/index.html', $indexFileContent);

        if ($this->controller !== null) {
            $this->controller->stdout('done.' . PHP_EOL, Console::FG_GREEN);
            $this->controller->stdout('generating search index...');
        }

        $indexer = new ApiIndexer();
        $indexer->indexFiles(FileHelper::findFiles($targetDir, ['only' => ['*.html']]), $targetDir);
        $js = $indexer->exportJs();
        file_put_contents($targetDir . '/jssearch.index.js', $js);

        if ($this->controller !== null) {
            $this->controller->stdout('done.' . PHP_EOL, Console::FG_GREEN);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSourceUrl($type, $line = null)
    {
        if (is_string($type)) {
            $type = $this->apiContext->getType($type);
        }

        switch ($this->getTypeCategory($type)) {
            case 'yii':
                $baseUrl = 'https://github.com/yiisoft/yii2/blob/master';
                if ($type->name == 'Yii') {
                    $url = "$baseUrl/framework/Yii.php";
                } else {
                    $url = "$baseUrl/framework/" . str_replace('\\', '/', substr($type->name, 4)) . '.php';
                }
                break;
            default:
                $parts = explode('\\', substr($type->name, 4));
                $project = $parts[1];
                switch ($project) {
                    case "admin": 
                        $repoName = "luya-module-admin";
                        unset($parts[0], $parts[1]);
                        break;
                    case "cms": 
                        $repoName = "luya-module-cms";
                        unset($parts[0], $parts[1]);
                        break;
                    case "testsuite": 
                        $repoName = "luya-testsuite";
                        unset($parts[0], $parts[1]);
                        break;
                    case "yii": 
                        $repoName = "yii-helpers";
                        unset($parts[0], $parts[1]);
                        break;
                    default:
                        $repoName = "luya";
                        unset($parts[0]);
                        break;
                }
                $rootFolder = $repoName == 'luya' ? 'core' : 'src';
                $url = "https://github.com/luyadev/{$repoName}/blob/master/{$rootFolder}/" . implode('/', $parts) . '.php';
                break;
        }

        if ($line === null) {
            return $url;
        }
        return $url . '#L' . $line;
    }
}
