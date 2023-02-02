import React, { Component } from "react";
import config from "src/commons/configHoc";
import PageContent from "src/components/page-content";
import "./style.less";
@config({
    path: "/home",
    title: { text: "首页", icon: "home" },
    breadcrumbs: [{ key: "home", text: "首页", icon: "home" }],
    noAuth: true,
    noFrame: false
})
export default class Home extends Component {
    state = {};
    render() {
        return (
            <PageContent>
                <div styleName="homeContainer">
                    <i />
                    <h4>祝您工作愉快</h4>
                </div>
            </PageContent>
        );
    }
}
