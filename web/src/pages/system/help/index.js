import React from "react";
import moment from "moment";

export const fieldData = [
    { label: "有效", type: "select", name: "user_status" },
    { label: "二次验证", type: "select", name: "is_salt" },
    { label: "用户名", type: "input", name: "user_name" },
    { label: "用户ID", type: "input", name: "user_id" }
];

export const columns = [
    { title: "ID", dataIndex: "user_id", fixed: "left" },
    { title: "邮箱", dataIndex: "user_email", fixed: "left" },
    { title: "中文名", dataIndex: "user_name" },
    {
        title: "最后登录时间",
        dataIndex: "last_login_time"
    },
    {
        title: "有效",
        dataIndex: "display_user_status"
    },
    {
        title: "二次验证",
        dataIndex: "display_is_salt"
    },

    {
        title: "角色",
        dataIndex: "display_roles",
        render: (text, record) => text?.toString()
    },

    {
        title: "语言",
        dataIndex: "display_language",
        render: (text, record) => text?.toString()
    },
    {
        title: "大区",
        dataIndex: "display_bigarea",
        render: (text, record) => text?.toString()
    }
];
