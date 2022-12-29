# api.luya.io

The API Class Reference for LUYA.

## Installation

1. git clone
2. composer install
3. ./generate.sh
4. The index.html is located in /dist

## View on GitHub Buttons

In order to provide the LUYA specific view on GitHub buttons, we had to tweak the ApiRenderer.php file, so here is the diff if there are any upcoming changes:

```diff
   public function render($context, $targetDir)
   {
-       $yiiTypes = $this->filterTypes($types, 'yii');
-       if (empty($yiiTypes)) {
-           //$readme = @file_get_contents("https://raw.github.com/yiisoft/yii2-framework/master/README.md");
-           $indexFileContent = $this->renderWithLayout($this->indexView, [
-               'docContext' => $context,
-               'types' => $this->filterTypes($types, 'app'),
-               'readme' => null,
-           ]);
-       } else {
-           $readme = @file_get_contents("https://raw.github.com/yiisoft/yii2-framework/master/README.md");
-           $indexFileContent = $this->renderWithLayout($this->indexView, [
-               'docContext' => $context,
-               'types' => $yiiTypes,
-               'readme' => $readme ?: null,
-           ]);
-       }
+       $indexFileContent = $this->renderWithLayout($this->indexView, [
+           'docContext' => $context,
+           'types' => $this->filterTypes($types, 'app'),
+           'readme' => null,
+       ]);
   }
```

```diff
public function getSourceUrl($type, $line = null)
{
-    case 'app':
-        return null;
-    default:
-        $parts = explode('\\', substr($type->name, 4));
-        $ext = $parts[0];
-        unset($parts[0]);
-        $url = "https://github.com/yiisoft/yii2-$ext/blob/master/" . implode('/', $parts) . '.php';
+    default:
+        $parts = explode('\\', substr($type->name, 4));
+        $project = $parts[1];
+        switch ($project) {
+            case "admin": 
+                $repoName = "luya-module-admin";
+                unset($parts[0], $parts[1]);
+                break;
+            case "cms": 
+                $repoName = "luya-module-cms";
+                unset($parts[0], $parts[1]);
+                break;
+            case "testsuite": 
+                $repoName = "luya-testsuite";
+                unset($parts[0], $parts[1]);
+                break;
+            case "yii": 
+                $repoName = "yii-helpers";
+                unset($parts[0], $parts[1]);
+                break;
+            default:
+                $repoName = "luya";
+                unset($parts[0]);
+                break;
+        }
+        $rootFolder = $repoName == 'luya' ? 'core' : 'src';
+        $url = "https://github.com/luyadev/{$repoName}/blob/master/{$rootFolder}/" . implode('/', $parts) . '.php';
+        break;
}
```