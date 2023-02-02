import React from "react";
import { Select, Image } from "antd";

const { Option } = Select;

export const GaiaSelect = ({
    handleOnChange,
    label,
    data = [],
    width = 180,
    valueKey = "value",
    nameKey = "name"
}) => {
    return (
        <div style={{ display: "flex", alignItems: "center" }}>
            <span>{label}：</span>
            <Select style={{ width }} onChange={handleOnChange}>
                {data.map((item, index) => (
                    <Option value={item[valueKey]} key={index}>
                        {item[nameKey]}
                    </Option>
                ))}
            </Select>
        </div>
    );
};

export const MyImage = ({ src, width = 48, height = 48, previewSrc }) =>
    src ? (
        <Image src={src} width={width} height={height} preview={{ src: previewSrc }} />
    ) : null;

export const If = props => {
    return !!props.data ? props.children : null;
};
