import React from "react";
import { createFromIconfontCN } from "@ant-design/icons";
import { Tooltip } from "antd";

const MyIconFont = createFromIconfontCN({
    scriptUrl: "//at.alicdn.com/t/c/font_2631586_ibvhto23hvi.js"
});

const IconFont = props => {
    const { title, ...other } = props;
    return (
        <Tooltip title={props.title}>
            <div style={{ fontSize: 18, cursor: "pointer", display: "inline-block" }}>
                <MyIconFont {...other} />
            </div>
        </Tooltip>
    );
};

export default IconFont;
