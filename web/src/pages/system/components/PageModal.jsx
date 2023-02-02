import React, { useState, useEffect } from "react";
import { Input, Form, Modal, Button, Select, Checkbox } from "antd";
import { MinusCircleOutlined, PlusCircleOutlined } from "@ant-design/icons";
import { If } from "src/components";
import ModalForm from "src/components/modal-form";
import "../less/BasicConfigure.less";

const layout = {
    labelCol: { span: 6 },
    wrapperCol: { span: 18 }
};

const options = [
    { label: "名称", name: "name", type: "input" },
    { label: "关键字", name: "code", type: "input" },
    { label: "路径", name: "path", type: "input" }
];

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

export default ({ visible, onOk, onCancel, initialValues, data, type }) => {
    const [form] = Form.useForm();
    const [pageData, setPageData] = useState(data);
    const [pointsData, setPointsData] = useState([]);

    useEffect(() => {
        setPageData(data);
        const obj = data.find(item => item.path === initialValues?.module_name);
        setPointsData(obj?.points || []);
    }, [data, initialValues]);

    useEffect(() => {
        if (visible) {
            form.setFieldsValue(initialValues);
        }
    }, [initialValues, visible]);

    const handleOk = async () => {
        const result = await form.validateFields();
        const obj = pageData.find(item => result.module_name == item.value);
        const points = pointsData.map(item => ({ ...item, module_name: item.name }));
        onOk({ ...obj, module_name: obj.name, points });
    };

    const handleOnPageChange = value => {
        const points = pageData.find(item => item.value == value)?.points;
        console.log(points);
        setPointsData(points);
    };

    const afterClose = () => {
        setPointsData([]);
    };

    return (
        <Modal
            title={initialValues ? " 编辑页面" : "增加页面"}
            width={640}
            visible={visible}
            onCancel={onCancel}
            onOk={handleOk}
            afterClose={afterClose}
            destroyOnClose
        >
            <Form {...layout} form={form} preserve={false}>
                <Form.Item
                    label="名称"
                    name="module_name"
                    rules={[{ required: true, message: "请输入" }]}
                >
                    <Select
                        showSearch
                        allowClear={true}
                        placeholder="请选择"
                        options={pageData}
                        onChange={handleOnPageChange}
                        optionFilterProp="children"
                        filterOption={(inputValue, option) =>
                            option.label.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1
                        }
                    />
                </Form.Item>
                <If data={pointsData.length > 0}>
                    <Form.Item label="功能" rules={[{ required: false, message: "请选择" }]}>
                        {pointsData.map((item, index) => (
                            <Checkbox checked disabled value={item.path} key={index}>
                                {item.name}
                            </Checkbox>
                        ))}
                    </Form.Item>
                </If>
            </Form>
        </Modal>
    );
};
