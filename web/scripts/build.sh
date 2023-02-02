# baseUrl=`pwd`
# targetUrl="/src/pages/lesscode"
# folder=$baseUrl$targetUrl
# if [ -d $folder ]; then
#    echo "--------------------------------------------"
#    echo "已存在lesscode目录,正在获取最新代码...."
#    echo "--------------------------------------------"
#    npm run updatemodules-lesscode
# else
#   echo "--------------------------------------------"
#   echo "不存在lesscode目录,正在拉取lesscode代码库..."
#   echo "--------------------------------------------"
#   npm run getmodules-lesscode
# fi

if [ $1 == "prod" ]; then
    echo "--------------------------------------------"
    echo "线上环境打包中...."
    npm run build-prod
    echo "--------------------------------------------"
else
    echo "--------------------------------------------"
    echo "测试环境打包中...."
    npm run build-test
    echo "--------------------------------------------"
fi