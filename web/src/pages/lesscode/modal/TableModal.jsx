import React, { useState, useEffect, useRef } from "react";
import { Modal } from "antd";
import CompGaiaTable from "src/components/gaia-table";
import CompGaiaFilter from "src/components/gaia-filter";
import { filterObj } from "../utils";
import { http } from "src/library/ajax";
import { api_prefix } from "../Common";
import { formatFilterData } from "../help";

export default ({ params, visible, onCancel }) => {
    const [columns, setColumns] = useState([]);
    const [fieldData, setFieldData] = useState([]);
    const [loadconfig, setLoadConfig] = useState(false);
    const [loadData, setloadData] = useState(false);
    const [data, setData] = useState([]);
    const [total, setTotal] = useState(0);
    const [current, setCurrent] = useState(1);
    const ref_filter = useRef(undefined);
    const ref_param = useRef({
        page: 1,
        limit: 15
    });

    const fetchConfig = async () => {
        setLoadConfig(false);
        const {
            data: {
                list: { fields },
                filter = []
            }
        } = await http.get(`/${api_prefix}/lesscode/index/listConfig`, params);
        ref_filter.current = filter;
        const newColumns = fields
            .filter(item => !item.hidden)
            .map((item, index) => {
                if (item.enum) {
                    return {
                        ...item,
                        title: item.comment,
                        dataIndex: item.name,
                        render: text => {
                            if (Array.isArray(text)) {
                                const filterArr = item.enum.filter(obj => text.includes(obj.value));
                                return filterArr?.map(item => item.label).join(",");
                            } else {
                                return item.enum.find(obj => obj.value == text)?.label;
                            }
                        }
                    };
                } else {
                    return {
                        ...item,
                        title: item.comment,
                        dataIndex: item.name
                    };
                }
            });
        setColumns(newColumns);
        handleFilter(filter);
        setLoadConfig(true);
        fetchData();
    };

    const handleFilter = data => {
        const specialComponent = ["multipleselect"];
        const newFieldData = data.map(item => {
            if (Array.isArray(item.name)) {
                return {
                    ...item,
                    label: item.label,
                    options: item.enum,
                    type: item.component,
                    name: item.name[0],
                    value: item.default,
                    name2: item.name[1]
                };
            } else {
                return {
                    ...item,
                    label: item.label,
                    options: item.enum,
                    value: specialComponent.includes(item.component) ? item.default || [] : item.default || handleOnExtraDefault(item.name),
                    type: item.component,
                    name: item.name
                };
            }
        });
        setFieldData(newFieldData);
    };

    const handleOnExtraDefault = field => {
        if (!params.default_filter) return;
        const obj = params.default_filter?.find(item => item[0] == field);
        return obj ? obj[1] : "";
    };
    const handleOnExtraParam = () => {
        let param = {};
        params.default_filter?.map(item => {
            let key = item[0];
            let val = item[1];
            if (!params.key) param[key] = val;
        });
        params.default_filter = undefined;
        return param;
    };

    const fetchData = async () => {
        setloadData(true);
        const { data, total } = await http.get(`/${api_prefix}/lesscode/index/list`, {
            ...handleOnExtraParam(),
            ...params,
            ...ref_param.current
        });
        setData(data);
        setTotal(total);
        setloadData(false);
    };

    useEffect(() => {
        fetchConfig();
    }, [visible]);

    const handleOnPageChange = (page, pageSize) => {
        ref_param.current.page = page;
        ref_param.current.limit = pageSize;
        setCurrent(page);
        fetchData();
    };

    const search = params => {
        const data = formatFilterData(params, ref_filter.current);
        ref_param.current = {
            ...filterObj({ ...ref_param.current, ...data }),
            page: 1
        };
        setCurrent(1);
        fetchData();
    };

    return (
        <Modal title={params.title} visible={visible} onCancel={onCancel} width={1100} footer={null}>
            <>
                {fieldData.length > 0 && <CompGaiaFilter fileds={fieldData} onSubmit={search} />}
                {loadconfig && (
                    <CompGaiaTable
                        columns={columns}
                        loading={loadData}
                        dataSource={data}
                        pagination={{
                            total,
                            current,
                            showTotal: total => ` 共 ${total} 条`,
                            onChange: handleOnPageChange
                        }}
                    />
                )}
            </>
        </Modal>
    );
};
