/**
 * 在当前目录下，可以新建各业务的多语言配置文件，例如a.js,b.js....
 * 避免 所有的语言配置文件全在一个文件中，
 * 在某个js 文件中，key 的命名, 带上业务的前缀,避免被覆盖，或者覆盖别人
 */

let result = {};
let hasSameKey = false;
let sameKeyField = "";
const req = require.context("./", false, /\.js$/);

req.keys().forEach(key => {
    if (["./index.js"].includes(key)) return;
    const model = req(key);
    const fileName = getModelName(key);
    const obj = model.default;
    Object.keys(result).forEach(item => {
        hasSameKey = Object.keys(obj).includes(item);
        sameKeyField = item;
    });
    if (hasSameKey) {
        throw Error(`${fileName}.js文件中, ${sameKeyField}与已有的key重复，请修改`);
    } else {
        result = { ...result, ...model.default };
    }
});

export default result;

function getModelName(filePath) {
    const baseName = filePath.replace("./", "").replace(".js", "");
    return baseName.replace(/-(\w)/g, (a, b) => b.toUpperCase());
}
