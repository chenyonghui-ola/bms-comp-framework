import React from "react";
import ModalForm from "src/components/modal-form";

const options = [
    { label: "名称", name: "name", type: "input" },
    { label: "关键字", name: "code", type: "input" },
    { label: "控制器", name: "controller", type: "input" },
    { label: "方法", name: "action", type: "input" },
    { label: "描述", name: "description", type: "textarea", required: false }
];

export default ({ visible, onOk, onCancel, initialValues }) => {
    return (
        <ModalForm
            visible={visible}
            title={initialValues ? "编辑功能点" : "新增功能点"}
            onOk={onOk}
            onCancel={onCancel}
            initialValues={initialValues}
            options={options}
        />
    );
};
