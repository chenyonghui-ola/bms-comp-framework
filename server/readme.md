# Banban 后台系统
## 安装步骤
 ----
 1. git clone 代码至本地；
 2. 在server目录下新建cache目录，将cache目录及其子目录权限设置为777
 3. 修改env.php中ENV常量设置为dev;
 4. 配置app/config_dev.php
 5. 修改config_dev.php文件中的数据库配置为个人开发环境对应的参数
 6. 修改config_define.php里常量配置
 7. 路由设置在route.php内
 8. 需要主动抛错误信息需调用ReportException
 9. 所有controler里的action请对应以下规则
    * index 列表
    * info 详情
    * create 创建
    * modify 修改