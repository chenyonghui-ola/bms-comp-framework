import React from "react";
import { ConfigProvider } from "antd";
import zhCN from "antd/lib/locale/zh_CN";
import en from "antd/lib/locale/en_US";
import AppRouter from "./router/AppRouter";
import moment from "moment";
import "moment/locale/zh-cn";
import PreLoading from "src/components/pre-loading";
import config from "src/commons/configHoc";
import cfg from "src/config";

const currentLang = localStorage.getItem("lang") || "zh_cn";

moment.locale("zh-cn");

@config({
    ajax: true,
    pubSub: true
})
export default class App extends React.Component {
    state = {
        canMountComp: false
    };
    componentDidMount() {
        this.fetchUserInfo("self");
        this.props.subscribe("refreshUserInfo", () => {
            this.fetchUserInfo();
        });
    }

    fetchUserInfo = flag => {
        const {
            ajax,
            action: { user }
        } = this.props;
        ajax.get("/api/auth/staff/menu", null, { closeErrorTip: true })
            .then(res => {
                user.setUserInfo(res.data);
            })
            .finally(() => {
                if (flag) setTimeout(() => this.setState({ canMountComp: true }), 100);
            });
    };

    render() {
        return this.state.canMountComp ? (
            <ConfigProvider locale={currentLang == "en" ? en : zhCN}>
                <AppRouter />
            </ConfigProvider>
        ) : (
            <PreLoading />
        );
    }
}
