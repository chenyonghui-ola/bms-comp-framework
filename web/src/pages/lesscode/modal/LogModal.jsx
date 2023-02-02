import { Modal, Table } from "antd";
import React, { useState, useEffect } from "react";

export default ({ visible, onCancel, params }) => {
    const [data, setData] = useState([]);

    const handleObject = data => {
        //todo 此处 可能类型判断不全
        return typeof data == "object" ? JSON.stringify(data) : data;
    };

    const isSame=(pre,current)=>{
        //todo 此处 可能类型判断不全
        if(pre && current){
            if(typeof current == "object" ){
                return JSON.stringify(pre) == JSON.stringify(current)
            }else{
                return  pre == current
            }
        }else{
            return true;
        }
    }

    useEffect(() => {
        const temData = [];
        const { after_json = {}, before_json = {} } = params;
        Object.keys(after_json).forEach((item, index) => {
            temData.push({
                id: index,
                column: item,
                before_json: handleObject(before_json[item]),
                after_json: handleObject(after_json[item])
            });
        });
        setData(temData);
    }, [params]);
    const columns = [
        {
            title: "字段名称",
            dataIndex: "column",
            width: 150
        },
        {
            title: "修改前",
            dataIndex: "before_json",
            render:(text,record)=><div style={{width:350, color: isSame(record.before_json,record.after_json)?'':'red',whiteSpace:'pre-wrap',wordBreak:"break-all",overflowX:"hidden" }}> {text}</div>
        },
        {
            title: "修改后",
            dataIndex: "after_json",
            render:(text,record)=><div style={{width:350, color: isSame(record.before_json,record.after_json)?'':'red' ,whiteSpace:'pre-wrap',wordBreak:"break-all",overflowX:"hidden" }}>{text} </div>
        }
    ];

    return (
        <Modal title="修改明细" visible={visible} onCancel={onCancel} width={1000}>
            <Table rowKey="id"  dataSource={data} bordered={true} columns={columns} pagination={false} />
        </Modal>
    );
};
