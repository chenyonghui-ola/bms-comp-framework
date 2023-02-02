import moment from "moment";

export const pickerEnum = {
    "year": "YYYY",
    "quarter": "YYYY-QQ",
    "month": "YYYY-MM",
    "week": "YYYY-wo",
    "date": "YYYY-MM-DD",
    "time": "HH:mm:ss",
    "year_time": "YYYY-MM-DD HH:mm:ss"
};

export const getMomentFormatType = (field, fields = []) => {
    const picker = fields?.find(item => item.name == field)?.flag;
    return pickerEnum[picker] || "YYYY-MM-DD";
};

export const formatFilterData = (data, fields) => {
    Object.keys(data).forEach(item => {
        if (item.includes("_sdate") && Array.isArray(data[item])) {
            const [pre_field] = item.split("_sdate");
            const copyData = [...data[item]];
            data[item] = copyData && copyData[0] && copyData[0].format("YYYY-MM-DD");
            data[`${pre_field}_edate`] = copyData && copyData[1] && copyData[1].format("YYYY-MM-DD");
        } else if (item.includes("_sdate") && data[item] == undefined) {
            const [pre_field] = item.split("_sdate");
            data[`${pre_field}_edate`] = undefined;
        }
        if (item.includes("_edate") && Array.isArray(data[item])) {
            const [pre_field] = item.split("_edate");
            const copyData = [...data[item]];
            data[`${pre_field}_sdate`] = copyData && copyData[0] && copyData[0].format("YYYY-MM-DD");
            data[item] = copyData && copyData[1] && copyData[1].format("YYYY-MM-DD");
        }

        if (moment.isMoment(data[item])) {
            data[item] = data[item].format(getMomentFormatType(item, fields));
        }
    });
    return data;
};

export const generateFilterData = data => {
    Object.keys(data).forEach(item => {
        if (item.includes("_sdate")) {
            const [pre_field] = item.split("_sdate");
            data[item] = [data[item] && moment(data[item]), data[`${pre_field}_edate`] && moment(data[`${pre_field}_edate`])];
            data[`${pre_field}_edate`] = undefined;
        }
    });
    return data;
};

export const clipboardCopy = text => {
    // 或者使用 document.execCommand()
    // 把需要复制的文本放入 <span>
    const span = document.createElement("span");
    span.textContent = text;
    // 保留文本样式
    span.style.whiteSpace = "pre";
    // 把 <span> 放进页面
    document.body.appendChild(span);
    // 创建选择区域
    const selection = window.getSelection();
    const range = window.document.createRange();
    selection.removeAllRanges();
    range.selectNode(span);
    selection.addRange(range);
    let success = false;
    try {
        success = window.document.execCommand("copy");
    } catch (err) {
        console.log("error", err);
    }
    selection.removeAllRanges();
    window.document.body.removeChild(span);
    return success ? Promise.resolve() : Promise.reject(new DOMException("The request is not allowed", "NotAllowedError"));
};

export * from "./cache";
