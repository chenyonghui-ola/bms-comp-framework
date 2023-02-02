import React, { useState, useEffect } from "react";
import {
    Form,
    Row,
    Col,
    notification,
    Input,
    Select,
    Checkbox,
    Radio,
    AutoComplete,
    Card,
    Upload,
    Button,
    DatePicker,
    Space
} from "antd";
import { http } from "src/library/ajax";

const { TextArea } = Input;
const CheckboxGroup = Checkbox.Group;

const layout = {
    labelCol: { span: 6 },
    wrapperCol: { span: 18 }
};

export default ({
    initialValues,
    onOk,
    options = [],
    onValuesChange,
    onCancle,
    resetValues
}) => {
    const [form] = Form.useForm();

    useEffect(() => {
        form.setFieldsValue(initialValues);
    }, [initialValues]);

    useEffect(() => {
        form.setFieldsValue(resetValues);
    }, [resetValues]);

    const handleOk = async () => {
        const result = await form.validateFields();
        onOk(result);
    };

    const handleBeforeUpload = async (file, name, url) => {
        const formData = new FormData();
        formData.append("file", file);
        const { data } = await http.post(url, formData, {
            headers: {
                "Content-Type": "multipart/form-data "
            }
        });
        form.setFieldsValue({
            [name]: data?.name
        });
        return false;
    };

    const renderItem = (item, index, required = true) => {
        if (item.type === "input") {
            let rules = [{ required: required, message: "请输入" }];
            if (item.pattern) {
                rules = [...rules, { pattern: item.pattern, message: "格式不正确" }];
            }
            return (
                <Form.Item
                    label={item.label}
                    name={item.name}
                    rules={rules}
                    hidden={item.hidden}
                >
                    <Input
                        disabled={item.disabled}
                        placeholder={item.placeholder}
                        maxLength={item.maxLength}
                    />
                </Form.Item>
            );
        }
        if (item.type === "textarea") {
            return (
                <Form.Item
                    label={item.label}
                    name={item.name}
                    hidden={item.hidden}
                    rules={[{ required: required, message: "请输入" }]}
                >
                    <TextArea
                        placeholder={item.placeholder}
                        autoSize={{ minRows: 4 }}
                        disabled={item.disabled}
                    />
                </Form.Item>
            );
        }
        if (item.type === "select") {
            return (
                <Form.Item
                    hidden={item.hidden}
                    label={item.label}
                    name={item.name}
                    rules={[{ required: required, message: "请选择" }]}
                >
                    <Select
                        allowClear={true}
                        showSearch
                        mode={item.mode}
                        disabled={item.disabled}
                        placeholder="请选择"
                        filterOption={(input, option) =>
                            option.label.toLowerCase().includes(input)
                        }
                        options={item.options || []}
                    />
                </Form.Item>
            );
        }
        if (item.type === "autocomplete") {
            return (
                <Form.Item
                    hidden={item.hidden}
                    allowClear={true}
                    label={item.label}
                    name={item.name}
                    rules={[{ required: required, message: "请选择" }]}
                >
                    <AutoComplete placeholder="请选择" options={item.options || []} />
                </Form.Item>
            );
        }

        if (item.type === "upload") {
            return (
                <Form.Item label={item.label} required={required} hidden={item.hidden}>
                    <div style={{ display: "flex", flexWrap: "nowrap" }}>
                        <Form.Item
                            name={item.name}
                            rules={[
                                {
                                    required: required,
                                    message: "请上传"
                                }
                            ]}
                        >
                            <Input style={{ marginRight: 16, flex: 1 }} disabled />
                        </Form.Item>
                        <Upload
                            fileList={[]}
                            customRequest={({ file }) =>
                                handleBeforeUpload(file, item.name, item.uploadUrl)
                            }
                        >
                            <Button type="primary" style={{ marginLeft: 8 }}>
                                上传
                            </Button>
                        </Upload>
                    </div>
                </Form.Item>
            );
        }

        if (item.type === "checkbox") {
            return (
                <Form.Item
                    label={item.label}
                    name={item.name}
                    hidden={item.hidden}
                    rules={[{ required: required, message: "请选择" }]}
                >
                    <CheckboxGroup
                        disabled={item.disabled}
                        options={item.options || []}
                    />
                </Form.Item>
            );
        }

        if (item.type === "radio") {
            return (
                <Form.Item
                    hidden={item.hidden}
                    label={item.label}
                    name={item.name}
                    rules={[{ required: required, message: "请选择" }]}
                >
                    <Radio.Group disabled={item.disabled} options={item.options || []} />
                </Form.Item>
            );
        }

        if (item.type === "datepicker") {
            return (
                <Form.Item
                    hidden={item.hidden}
                    label={item.label}
                    name={item.name}
                    rules={[{ required: required, message: "请选择" }]}
                >
                    <DatePicker showTime />
                </Form.Item>
            );
        }

        if (item.type === "desc") {
            return (
                <Form.Item
                    hidden={item.hidden}
                    label={item.label}
                    name={item.name}
                    rules={[{ required: false }]}
                >
                    <span>{item.content}</span>
                </Form.Item>
            );
        }
        return null;
    };
    return (
        <div style={{ backgroundColor: "#f5f5f5" }}>
            <Form
                {...layout}
                form={form}
                preserve={false}
                onValuesChange={onValuesChange}
            >
                {options.map((item, index) => {
                    return (
                        <Card
                            key={index}
                            title={item.title}
                            bordered={false}
                            style={{
                                width: "100%",
                                marginBottom: index == options.length - 1 ? 0 : 8
                            }}
                        >
                            <Row>
                                {item.options.map(
                                    (oitem, oindex) =>
                                        !oitem.hidden && (
                                            <Col
                                                span={8}
                                                key={`${index}-${oindex}`}
                                                style={{
                                                    paddingRight: 16
                                                }}
                                            >
                                                {renderItem(
                                                    oitem,
                                                    oindex,
                                                    oitem.required
                                                )}
                                            </Col>
                                        )
                                )}
                            </Row>
                        </Card>
                    );
                })}
            </Form>
            <Space
                size="large"
                style={{
                    display: "flex",
                    justifyContent: "center",
                    backgroundColor: "white",
                    // marginTop: 32,
                    marginBottom: 64
                }}
            >
                <Button style={{ width: 120, height: 40 }} onClick={onCancle}>
                    取消
                </Button>
                <Button
                    type="primary"
                    style={{ width: 120, height: 40 }}
                    onClick={handleOk}
                >
                    提交
                </Button>
            </Space>
        </div>
    );
};
