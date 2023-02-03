<?php
/**
 * 更新组件库
 * Date: 2022-02-02
 * Version: 1.0.0
 */

$config = "./comp.ini";
$configData = parse_ini_file($config,true);

//支持新增

//支持删除

//支持全量更新 判断当前版本号是否一致一样的不更新

//支持按模块名称更新 判断当前版本号是否一致一样的不更新

//如果模块有autoload_file配置需要更新到comp_autoload.php文件里

//根据pull_path下载指定目录，如果pull_path为空，就全部下载

//根据version下载指定tag或者分支代码，为空就从master下载

//更新完成后生成一个version文件记录当前版本号，如果版本号为空的始终允许更新


