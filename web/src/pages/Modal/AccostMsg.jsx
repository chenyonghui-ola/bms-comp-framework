import React, {useEffect, useRef, useState} from 'react'
import ModalForm from "src/components/modal-form";
import {http} from 'src/library/ajax'
import {message} from "antd";

const options = [
    {label: '大区', name: "bigarea_id", type: 'select'},
    {label: '语言', name: "language", type: 'select', required: false},
    {
        label: '消息类型', name: "msg_type", type: 'select', options: [
            {label: "文本", value: "1"},
            {label: "音频", value: "2"},
            {label: "图片", value: "3"},
            {label: "视频", value: "4"}
        ]
    },
    {label: '消息内容', name: "msg_content", type: 'input'},
];

export default ({visible, params, onCancel, onSuccess}) => {

    console.log(params)

    const [myOptions, setMyOptions] = useState(options)
    const [initValues, setInitValues] = useState(undefined);

    const fetchConfig = async () => {
        const {data: {bigarea_id}} = await http.get('/api/common/enum/getList?type=bigarea_id')
        const {data: {language}} = await http.get('/api/common/enum/getList?type=language')
        const newOptions = options.map(item => ({...item, options: item.options || {bigarea_id, language}[item.name]}))
        if (params.id) {
            changeOptions(params,newOptions)
            setInitValues(params)
        }else{
            setMyOptions(newOptions);
        }
    }

    useEffect(() => {
        if (visible) {
            fetchConfig()
        }
    }, [visible])

    const handleOnModalOk = async (data) => {
        if (params.id) await http.post('/api/operate/accostmsg/modify', {...data, id: params.id})
        else await http.post('/api/operate/accostmsg/create', data)
        message.success('操作成功')
        onSuccess();
    }

    const changeOptions=(obj,myOptions)=>{
        const msg_type = obj.msg_type;
        const targetValues = ['2', '3', '4']
        const uploadUrls = [
            '/api/common/upload/voice?type=getDuration',
            '/api/common/upload/image?filesize=2048&ext=jpg,png,gif',
            '/api/common/upload/video?type=getDuration'
        ]
        if (msg_type) {
            if (targetValues.includes(msg_type)) {
                setMyOptions(myOptions?.map(item => ({
                    ...item,
                    uploadUrl: uploadUrls[msg_type - 2],
                    type: {msg_content: 'upload'}[item.name] || item.type
                })))
            } else {
                setMyOptions(myOptions?.map(item => ({...item, type: {msg_content: 'input'}[item.name] || item.type})))
            }
        }
    }

    const handleOnFieldsChange = (value, allValue) => {
       changeOptions(value,myOptions)
    }

    return <ModalForm
        title={params.title}
        onCancel={onCancel}
        visible={visible}
        onOk={handleOnModalOk}
        options={myOptions}
        initialValues={initValues}
        onFieldsChange={handleOnFieldsChange}
    />
}