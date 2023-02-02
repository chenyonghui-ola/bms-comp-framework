import React, { Component } from "react";
import "./style.less";
import logo from "src/style/image/logo.png";
import veekaSvg from "src/style/image/veeka_logo.svg";

export default () => {
    return (
        <div styleName="root active">
            <div styleName="logo">
                <img src={veekaSvg} alt="" style={{ width: 180, height: 56 }} />
            </div>
        </div>
    );
};
