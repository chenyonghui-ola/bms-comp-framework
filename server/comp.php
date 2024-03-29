<?php
/**
 * 更新组件库
 * Date: 2022-02-02
 * Version: 1.0.0
 */

$autoloadFile = "./comp/autoload.php";
$config = "./comp.ini";
$configData = parse_ini_file($config, true);

if (empty($argv[1]) || !in_array($argv[1], ['install', 'update', 'delete'])) {
    echo '参数缺失,传参：install 安装 update 更新 delete 删除' . PHP_EOL;
    exit;
}

$action = $argv[1];
$module = $argv[2] ?? 'all';
if ($module == 'all') {
    $handleData = $configData;
} else {
    if (empty($configData[$module])) {
        echo '该模块未配置：' . $module . PHP_EOL;
    }
    $handleData = [$module => $configData[$module]];
}

$action($handleData);

///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//支持删除
function delete($handleData)
{
    foreach ($handleData as $module => $item) {
        echo 'delete start:' . $module . PHP_EOL;

        //删除模块目录
        $dir = dirname(__FILE__) . '/' . $item['save_path'];
        passthru("rm -rf $dir");
        //如果有加载文件
        //清除autoload.php里该文件的加载
        autoloadCancel($item);
        //提交git
        gitCommit('del 模块' . $module);

        echo 'delete done:' . $module . PHP_EOL;
    }

    exit;
}

//支持新增
function install($handleData)
{
    foreach ($handleData as $module => $item) {
        echo 'install start:' . $module . PHP_EOL;

        syncVersion($item['save_path'], $item['version']);
        echo 'install done:' . $module . PHP_EOL;
    }

    exit;
}

//支持全量更新 判断当前版本号是否一致一样的不更新
//支持按模块名称更新 判断当前版本号是否一致一样的不更新
//如果模块有autoload_file配置需要更新到comp_autoload.php文件里
//根据pull_path下载指定目录，如果pull_path为空，就全部下载
//根据version下载指定tag或者分支代码，为空就从master下载
//更新完成后生成一个version文件记录当前版本号，如果版本号为空的始终允许更新
function update($handleData)
{

}

function syncVersion($savePath, $version)
{
    $file = dirname(__FILE__) . '/' . $savePath . '/version';
    file_put_contents($file, $version);
}

function autoloadCancel($item)
{
    global $autoloadFile;

    //如果有加载文件
    //清除autoload.php里该文件的加载
    if (!empty($item['autoload_file'])) {
        $file = $item['save_path'] . '/' . $item['autoload_file'];
        $file = "require_once(ROOT . DS . '{$file}');";
        $content = file_get_contents($autoloadFile);
        $content = str_replace($file, '', $content);
        file_put_contents($autoloadFile, $content);
    }
}

function autoloadAdd($item)
{
    global $autoloadFile;

    //如果有加载文件
    //添加autoload.php里该文件的加载
    if (!empty($item['autoload_file'])) {
        $file = $item['save_path'] . '/' . $item['autoload_file'];
        $file = "require_once(ROOT . DS . '{$file}');";
        file_put_contents($autoloadFile, $file . PHP_EOL, FILE_APPEND);
    }
}

function gitCommit($msg)
{
    $dir = dirname(__FILE__) . '/comp';
    passthru("cd $dir");
    passthru("git add .");
    passthru("git commit -m '" . $msg . "'");
    //passthru("git push");
}