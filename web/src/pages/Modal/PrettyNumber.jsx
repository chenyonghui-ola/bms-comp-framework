import React, {useEffect, useState} from "react";
import {concat} from "lodash/array";
import {Modal, Form, Input, Select, DatePicker} from "antd";
import {http} from "src/library/ajax";
import moment from "moment";

const layout = {
    labelCol: {span: 6},
    wrapperCol: {span: 18}
};

const typeMap = [
    {label: '规则靓号', value: 1},
    {label: '自定义靓号', value: 2},
]

const levelMap = [
    {label: '初级', value: 20},
    {label: '中级', value: 30},
    {label: '高级', value: 40},
    {label: '顶级', value: 50},
]

const lenMap = [
    {label: '6位数', value: 6},
    {label: '9位数', value: 9},
]

const ruleTips = {
    '20': {
        '6': 'AAAAAB、AAAABB、AAABBB、AABBBB、ABBBBB',
        '9': 'AAAAAAAB、ABBBBBBB、AABBBBBBB、AAABBBBBB、AAAABBBBB、AAAAABBBB、AAAAAABBB'
    },
    '30': {
        '6': 'ABABAB、AABAAB、ABCABC',
        '9': 'ABCABCABC'
    },
    '40': {
        '6': 'ABCDEF、FEDCBA',
        '9': 'ABCDEFGHI、IHGFEDCBA'
    },
    '50': {
        '6': 'AAAAAA',
        '9': 'AAAAAAAAA'
    },
};

export default ({visible, onCancel, params, onSuccess}) => {
    const [form] = Form.useForm();
    const [disabled, setDisabled] = useState(false);
    const [rule, setRule] = useState(undefined);
    const [obj, setObj] = useState({});
    const [level, setLevel] = useState([]);
    const [length, setLength] = useState([]);
    const fetch = async () => {
        if (params.id) {
            setDisabled(true);
            getPrettyInfo();
        }
        setRule('自定义数字规则')
        const {data: {bigarea_id}} = await http.get('/api/common/enum/getList?type=bigarea_id')
        setObj({
            bigAreaMap: bigarea_id
        })

    };
    useEffect(() => {
        fetch();
    }, []);

    const getPrettyInfo = async () => {
        const {data} = await http.get("/api/operate/pretty/info", {
            id: params.id
        });
        initLevel(data.type)
        initLength(data.level, data.type);
        form.setFieldsValue(
            data
                ? {
                    ...data,
                    start_time: data.start_time && moment(data.start_time, "YYYY-MM-DD HH:mm:ss"),
                    end_time: data.start_time && moment(data.end_time, "YYYY-MM-DD HH:mm:ss"),
                    bigarea_id: data.bigarea_id + ''
                }
                : {}
        );
    }

    const handleOnFieldsChange = (value, allValues) => {
        if (value.type) {
            initLevel(value.type);
            if (params.id) {
                form.setFieldsValue({level: undefined})
            } else {
                form.setFieldsValue({level: undefined, length: undefined})
            }
        }
        if (value.level && !params.id) {
            initLength(value.level, allValues.type)
            form.setFieldsValue({length: undefined})
        }
        if (allValues.type
            && allValues.type == 1
            && allValues.level
            && allValues.length) {
            setRule(ruleTips[allValues.level][allValues.length]);
        } else {
            setRule('自定义数字规则');
        }

    }

    const initLevel = (type) => {
        if (type == 2) {
            const newLevel = concat({'label': '普通', 'value': 10}, levelMap)
            setLevel(newLevel)
        } else {
            setLevel(levelMap);
        }
    }

    const initLength = (level, type) => {
        if (level == 50 && type == 2) {
            const newLen = concat({'label': '3位数', 'value': 3}, {'label': '4位数', 'value': 4}, lenMap)
            setLength(newLen)
        } else {
            setLength(lenMap);
        }
    }

    const handleOnOk = async () => {
        const result = await form.validateFields();
        if (result.start_time) {
            result.start_time = result.start_time.format("YYYY-MM-DD HH:mm:ss");
        }
        if (result.end_time) {
            result.end_time = result.end_time.format("YYYY-MM-DD HH:mm:ss");
        }
        if (params.id) {
            const id = params.id;
            await http.post(`/api/operate/pretty/modify`, {
                ...result,
                id
            });
        } else {
            await http.post(`/api/operate/pretty/create`, {
                ...result,
            });
        }
        onSuccess();
    };

    return (
        <Modal
            title={params.title}
            visible={visible}
            onCancel={onCancel}
            width={640}
            onOk={handleOnOk}
        >
            <Form
                {...layout}
                form={form}
                preserve={false}
                onValuesChange={handleOnFieldsChange}
            >
                <Form.Item label="运营大区" name="bigarea_id" rules={[{required: true}]}>
                    <Select
                        disabled={disabled}
                        showSearch
                        allowClear={true}
                        placeholder="请选择"
                        options={obj.bigAreaMap}
                        optionFilterProp="children"
                        filterOption={(inputValue, option) =>
                            option.label
                                .toLowerCase()
                                .indexOf(inputValue.toLowerCase()) !== -1
                        }
                    />
                </Form.Item>
                <Form.Item label="靓号类型" name="type" rules={[{required: true}]}>
                    <Select
                        showSearch
                        allowClear={true}
                        placeholder="请选择"
                        options={typeMap}
                        optionFilterProp="children"
                        filterOption={(inputValue, option) =>
                            option.label
                                .toLowerCase()
                                .indexOf(inputValue.toLowerCase()) !== -1
                        }
                    />
                </Form.Item>
                <Form.Item label="靓号等级" name="level" rules={[{required: true}]}>
                    <Select
                        showSearch
                        allowClear={true}
                        placeholder="请选择"
                        options={level}
                        optionFilterProp="children"
                        filterOption={(inputValue, option) =>
                            option.label
                                .toLowerCase()
                                .indexOf(inputValue.toLowerCase()) !== -1
                        }
                    />
                </Form.Item>
                <Form.Item label="靓号位数" name="length" rules={[{required: true}]}>
                    <Select
                        disabled={disabled}
                        showSearch
                        allowClear={true}
                        placeholder="请选择"
                        options={length}
                        optionFilterProp="children"
                        filterOption={(inputValue, option) =>
                            option.label
                                .toLowerCase()
                                .indexOf(inputValue.toLowerCase()) !== -1
                        }
                    />
                </Form.Item>
                <Form.Item label="靓号">
                    <Form.Item name="number" rules={[{required: true}]}>
                        <Input placeholder="请输入靓号" disabled={disabled}/>
                    </Form.Item>
                    <span style={{color: "#ff0000"}}>输入规则：{rule}</span>
                </Form.Item>
                <Form.Item label="发放对象UID" name="uid" rules={[{required: true}]}>
                    <Input placeholder="请输入UID"/>
                </Form.Item>
                <Form.Item label="开始时间" name="start_time" rules={[{required: true}]}>
                    <DatePicker picker={"date"} showTime/>
                </Form.Item>
                <Form.Item label="结束时间" name="end_time" rules={[{required: true}]}>
                    <DatePicker picker={"date"} showTime/>
                </Form.Item>
                <Form.Item label="发放场景" name="send_scene" rules={[{required: true}]}>
                    <Input placeholder="请输入发放场景"/>
                </Form.Item>
            </Form>
        </Modal>
    );
};