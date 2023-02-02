import React from "react";
import { Dropdown, Menu } from "antd";
import langImg from "./img/lang.png";
import langImgWhit from "./img/white_lang.png";
const langDict = [
    { key: "en", label: "英语" },
    { key: "zh_cn", label: "中文" }
];

export default ({ dark }) => {
    const handleLangMenuClick = ({ key }) => {
        localStorage.setItem("lang", key);
        window.location.reload();
    };
    const langMenu = (
        <Menu onClick={handleLangMenuClick}>
            {langDict.map(item => (
                <Menu.Item key={item.key}>{item.label}</Menu.Item>
            ))}
        </Menu>
    );
    return (
        <Dropdown overlay={langMenu}>
            <img
                src={dark ? langImgWhit : langImg}
                alt=""
                style={{ width: 32, height: 32, cursor: "pointer" }}
            />
        </Dropdown>
    );
};
