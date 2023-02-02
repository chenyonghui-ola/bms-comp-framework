import React, { useState, useEffect } from "react";
import { Modal, Input, Button } from "antd";
import { MinusCircleOutlined } from "@ant-design/icons";
import GaiaForm from "src/components/gaia-form";
import _ from "lodash";
import "../less/BasicConfigure.less";

export const actionType = {
    ADDMENUTOP: "add-menu-top",
    ADDMENUBOTTOM: "add-menu-bottom",
    ADDPAGETOP: "add-page-top",
    ADDPAGEBOTTOM: "add-page-bottom",
    ADDCHILDRENPAGE: "add-childrenPage",
    TOP: "top",
    BOTTOM: "bottom",
    DELETE: "delete",
    UPDATE: "update",
    ADDCHILDMENU: "add-child-menu"
};

export const If = props => {
    return !!props.data ? props.children : null;
};

const LabelItem = ({ label, children }) => (
    <div
        style={{
            display: "flex",
            flexDirection: "column",
            marginLeft: 24
        }}
    >
        <span>{label}</span>
        {children}
    </div>
);


