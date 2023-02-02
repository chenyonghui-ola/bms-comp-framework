import React from "react";
import ModalForm from "src/components/modal-form";

const options = [
    { label: "名称", name: "module_name", type: "input" },
    { label: "图标", name: "icon", type: "input", required: false }
];

export default ({ visible, onOk, onCancel, initialValues }) => {
    return (
        <ModalForm
            visible={visible}
            title={initialValues ? " 编辑导航" : "增加导航"}
            onOk={onOk}
            onCancel={onCancel}
            initialValues={initialValues}
            options={options}
        />
    );
};
