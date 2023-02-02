import React, { useState, useRef, useMemo } from "react";
import { Select, Spin } from "antd";
import debounce from "lodash.debounce";
import { http } from "src/library/ajax";
import { api_prefix } from "../Common";

export default ({ url, debounceTimeout = 800, ...props }) => {
    const [fetching, setFetching] = useState(false);
    const [options, setOptions] = useState([]);
    const fetchRef = useRef(0);

    const fetchOptions = async str => {
        if (!str) return;
        return http.get(`/${api_prefix}/${url}`, { str }).then(res => {
            return res?.data || [];
        });
    };

    const debounceFetcher = useMemo(() => {
        const loadOptions = value => {
            fetchRef.current += 1;
            const fetchId = fetchRef.current;
            setOptions([]);
            setFetching(true);
            fetchOptions(value).then(newOptions => {
                if (fetchId !== fetchRef.current) {
                    return;
                }
                setOptions(newOptions);
                setFetching(false);
            });
        };
        return debounce(loadOptions, debounceTimeout);
    }, [fetchOptions, debounceTimeout]);

    return (
        <Select
            // labelInValue
            filterOption={false}
            onSearch={debounceFetcher}
            notFoundContent={fetching ? <Spin size="small" /> : null}
            {...props}
            options={options}
        />
    );
};
