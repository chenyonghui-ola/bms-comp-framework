import React from "react";
import { createFromIconfontCN } from "@ant-design/icons";
import "./iconfont.less";
import { Tooltip } from "antd";

const MyIconFont = createFromIconfontCN({
    scriptUrl: "//at.alicdn.com/t/font_2631586_nshe86s3xjc.js"
});

const IconFont = props => {
    const { title, ...other } = props;
    return (
        <Tooltip title={$I18N.t(title)}>
            <div styleName="iconfont-class">
                <MyIconFont {...other} />
            </div>
        </Tooltip>
    );
};

export default IconFont;
