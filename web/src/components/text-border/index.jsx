import React from "react";
import "./index.less";

export default ({ children, title }) => {
    return (
        <div styleName="text-border" title={title}>
            <div styleName="text-border-inner">{children}</div>
        </div>
    );
};
