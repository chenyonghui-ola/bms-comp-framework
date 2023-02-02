import React, { Component, Suspense } from "react";
import { BrowserRouter, Route, Switch } from "react-router-dom";
import { CacheSwitch } from "react-router-cache-route";
import PageFrame from "src/layouts/frame";
import config from "src/commons/configHoc";
import PageLoading from "src/components/page-loading";
import KeepAuthRoute from "./KeepAuthRoute";
import routes, { noFrameRoutes, commonPaths } from "./routes";
import cfg from "src/config";
import * as _ from "lodash";

@config({
    query: true,
    connect: state => ({
        systemNoFrame: state.system.noFrame,
        pages: state.user.pages
    })
})
export default class AppRouter extends Component {
    /**
     * commonPaths 为所有人都可以访问的路径 在 routes 中定义
     * @returns {{path: *, component: *}[]}
     */
    getCommonRoutes = () => {
        let _routes = _.filter(routes, item => _.includes(commonPaths, item.path));
        return _routes;
    };

    render() {
        const { noFrame: queryNoFrame } = this.props.query;
        const { systemNoFrame } = this.props;
        const commonRoutes = this.getCommonRoutes();
        return (
            <BrowserRouter basename={cfg.baseName}>
                <Route
                    path="/"
                    render={props => {
                        // 框架组件单独渲染，与其他页面成为兄弟节点，框架组件和具体页面组件渲染互不影响
                        if (systemNoFrame) return null;
                        // 通过配置，筛选那些页面不需要框架
                        if (noFrameRoutes.includes(props.location.pathname)) return null;
                        // 如果浏览器url中携带了noFrame=true参数，不显示框架
                        if (queryNoFrame === "true") return null;
                        return <PageFrame {...props} />;
                    }}
                />
                <Suspense fallback={<PageLoading />}>
                    {/* <Switch> */}
                    <CacheSwitch which={el => el.type != KeepAuthRoute}>
                        {commonRoutes.map(item => (
                            <Route
                                key={item.path}
                                path={item.path}
                                component={item.component}
                            />
                        ))}
                        {routes.map(item => {
                            const { path, component } = item;
                            return (
                                <KeepAuthRoute
                                    key={path}
                                    exact
                                    path={path}
                                    noAuth={true}
                                    component={component}
                                />
                            );
                        })}
                    </CacheSwitch>
                    {/* </Switch> */}
                </Suspense>
            </BrowserRouter>
        );
    }
}
