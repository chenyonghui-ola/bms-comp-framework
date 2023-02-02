import React, { useState, useRef, useEffect } from "react";
import { Modal, Button } from "antd";
import cfg from "src/config";
import { http } from "src/library/ajax";

const { ajaxPrefix } = cfg;

/**
 * @param   额外的参数
 * @title   名字
 * @exportUrl  导出接口 /api/xxxx
 * @pollingUrl  轮询接口 /api/xxxx
 */
export default ({ title = "导出", ajax, param = {}, exportUrl, pollingUrl }) => {
    const [loading, setLoading] = useState(false);
    const timeref = useRef(null);
    const countRef = useRef(0);
    const ref_myParam = useRef(param);
    useEffect(() => {
        ref_myParam.current = param;
    }, [param]);
    const exportSome = async () => {
        Modal.confirm({
            content: "确定要导出吗？",
            onOk: async () => {
                setLoading(true);
                const {
                    data: { url }
                } = await http.post(exportUrl, ref_myParam.current);
                if (url) {
                    window.location.href = `http://${window.location.host}${url}`;
                    setLoading(false);
                    return;
                }
                timeref.current = setInterval(async () => {
                    if (countRef.current >= 180) {
                        timeref.current && clearInterval(timeref.current);
                        setLoading(false);
                        return;
                    }
                    const {
                        data: { is_ok, url }
                    } = await http.post(pollingUrl + "&polling=1", ref_myParam.current);
                    if (is_ok) {
                        timeref.current && clearInterval(timeref.current);
                        setLoading(false);
                        window.location.href = `${ajaxPrefix}${url}`;
                        return;
                    }
                    countRef.current++;
                }, 3000);
            }
        });
    };
    
    useEffect(() => {
        return timeref.current && clearInterval(timeref.current);
    }, []);
    return (
        <Button type="primary" onClick={exportSome} loading={loading}>
            {title}
        </Button>
    );
};
