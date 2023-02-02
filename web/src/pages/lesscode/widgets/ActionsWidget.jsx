import React, { useEffect } from "react";
import { Space, Button } from "antd";
import { GithubOutlined } from "@ant-design/icons";
import { useDesigner, TextWidget } from "@designable/react";
import { GlobalRegistry } from "@designable/core";
import { observer } from "@formily/react";
import { loadInitialSchema, saveSchema } from "../service";

export const ActionsWidget = observer(({ action, schema }) => {
    const designer = useDesigner();
    useEffect(() => {
        loadInitialSchema(designer, schema);
    }, [schema]);
    return (
        <Space style={{ marginRight: 10 }}>
            <Button
                type="primary"
                onClick={() => {
                    saveSchema(designer, action);
                }}
            >
                <TextWidget>Publish</TextWidget>
            </Button>
        </Space>
    );
});
