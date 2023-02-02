import React from "react";
import "./index.less";

export default ({ children, title }) => {
    return (
        <div styleName="group-box" title={title}>
            <div styleName="group-box-inner">{children}</div>
        </div>
    );
};
