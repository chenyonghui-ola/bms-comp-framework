import { Engine } from "@designable/core";
import { transformToSchema, transformToTreeNode } from "@designable/formily-transformer";
import { message } from "antd";
import { http } from "src/library/ajax";
import { getUrlParams } from "../utils";
import { api_prefix } from "../Common";

export const saveSchema = async (designer, action) => {
    const guid = getUrlParams("guid");
    const data = JSON.parse(localStorage.getItem("formDesignData") || "{}");
    const formily_schema = JSON.stringify(transformToSchema(designer.getCurrentTree()));
    if (guid) {
        await http.post(`/${api_prefix}/lesscode/form/update`, { guid, formily_schema });
    } else {
        await http.post(`/${api_prefix}/lesscode/form/create`, {
            ...data,
            formily_schema
        });
        const { data: userData } = await http.get(`/${api_prefix}/auth/staff/menu`);
        const { menu, system, user } = action;
        user.setUserInfo(userData);
        menu.getMenus({
            params: { userId: "" }
        });
    }
    message.success("Save Success");
};

export const loadInitialSchema = (designer, schema) => {
    try {
        if (schema) designer.setCurrentTree(transformToTreeNode(schema));
    } catch {}
};
