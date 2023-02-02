import React, { useState, useEffect } from "react";
import { Modal } from "antd";
import axios from "axios";

export default () => {
    const [visible, setVisible] = useState(true);
    const [html, setHtml] = useState(undefined);
    const fetch = async () => {
        const { data: html } = await axios.get(window.EXPORT_URL);
        setHtml(html);
    };
    useEffect(() => {
        if (visible) fetch();
    }, [visible]);
    useEffect(() => {
        setVisible(true);
    }, []);
    return (
        <>
            <Modal
                title="导出信息"
                visible={visible}
                footer={null}
                onCancel={() => setVisible(false)}
                destroyOnClose
                maskClosable={false}
            >
                <div dangerouslySetInnerHTML={{ __html: html }} />
            </Modal>
        </>
    );
};
