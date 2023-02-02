import { Modal, Table, Form, Button, Space, Select, Input, DatePicker, message } from "antd";
import React, { useState, useEffect } from "react";
import { PlusOutlined, MinusCircleOutlined } from "@ant-design/icons";
import moment from "moment";
import { http } from "src/library/ajax";
import { getMomentFormatType } from "../help";
import { api_prefix } from "../Common";

const layout = {
    labelCol: { span: 1 },
    wrapperCol: { span: 23 }
};

const commonStyle = { flex: 1, marginRight: 6 };

export default ({ visible, onCancel, params, onSuccess }) => {
    console.log(params);
    const [form] = Form.useForm();
    const [options, setOptions] = useState([]);
    useEffect(() => {
        const { fields } = params;
        if (fields) {
            setOptions(
                fields
                    .filter(item => item.component)
                    .map(item => ({
                        ...item,
                        label: item.comment,
                        type: item.component,
                        required: item.required || false,
                        options: item.enum,
                        content: item.component == "text" ? item.default : ""
                    }))
            );
        }
    }, []);

    const handleOnOk = async () => {
        const result = await form.validateFields();
        const newResult = result?.data?.map(item => {
            const extra = {};
            Object.keys(item).forEach(field => {
                if (moment.isMoment(item[field])) {
                    const timeStr = item[field].format(getMomentFormatType(field, params?.fields));
                    extra[field] = timeStr;
                }
            });
            return { ...item, ...extra };
        });
        await http.post(`/${api_prefix}/${params.url}`, { data: newResult });
        message.success("操作成功");
        onSuccess();
    };

    return (
        <Modal visible={visible} onCancel={onCancel} title={params.title} width={1000} onOk={handleOnOk}>
            <Form {...layout} form={form} preserve={false}>
                <Form.List name="data">
                    {(fields, { add, remove }) => (
                        <>
                            <Form.Item label="">
                                {fields.map(({ key, ...rest }, index) => (
                                    <div key={key} align="baseline" style={{ display: "flex", alignItems: "center" }}>
                                        {options?.map((item, index) => {
                                            const { required = true } = item;
                                            if (item.type === "input") {
                                                let rules = [{ required: required, message: "请输入" }];
                                                if (item.pattern) {
                                                    rules = [...rules, { pattern: item.pattern, message: "格式不正确" }];
                                                }
                                                return (
                                                    <Form.Item
                                                        {...rest}
                                                        style={commonStyle}
                                                        key={index}
                                                        label={item.label}
                                                        name={[rest.name, item.name]}
                                                        rules={rules}
                                                    >
                                                        <Input
                                                            disabled={item.disabled}
                                                            maxLength={item.maxLength}
                                                            placeholder={item.placeholder}
                                                        />
                                                    </Form.Item>
                                                );
                                            }

                                            if (item.type === "select") {
                                                return (
                                                    <Form.Item
                                                        {...rest}
                                                        style={commonStyle}
                                                        key={index}
                                                        label={item.label}
                                                        name={[rest.name, item.name]}
                                                        rules={[{ required: required, message: "请选择" }]}
                                                    >
                                                        <Select
                                                            showSearch
                                                            allowClear={true}
                                                            disabled={item.disabled}
                                                            placeholder="请选择"
                                                            mode={item.mode || ""}
                                                            options={item.options || []}
                                                            optionFilterProp="children"
                                                            filterOption={(inputValue, option) =>
                                                                option.label.toLowerCase().indexOf(inputValue.toLowerCase()) !== -1
                                                            }
                                                        />
                                                    </Form.Item>
                                                );
                                            }
                                            if (item.type === "datepicker") {
                                                return (
                                                    <Form.Item
                                                        {...rest}
                                                        style={commonStyle}
                                                        key={index}
                                                        label={item.label}
                                                        name={[rest.name, item.name]}
                                                        rules={[{ required: required, message: "请选择" }]}
                                                    >
                                                        <DatePicker
                                                            picker={item.flag == "year_time" ? "date" : item.flag || "date"}
                                                            showTime={item.flag == "year_time"}
                                                            disabled={item.disabled}
                                                        />
                                                    </Form.Item>
                                                );
                                            }
                                        })}
                                        <MinusCircleOutlined style={{ marginBottom: 24 }} onClick={() => remove(rest.name)} />
                                    </div>
                                ))}
                                <Form.Item>
                                    <Button type="dashed" onClick={() => add()} block icon={<PlusOutlined />}>
                                        Add
                                    </Button>
                                </Form.Item>
                            </Form.Item>
                        </>
                    )}
                </Form.List>
            </Form>
        </Modal>
    );
};
