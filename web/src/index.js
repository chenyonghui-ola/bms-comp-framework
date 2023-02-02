import "./public-path";
import React from "react";
import ReactDOM from "react-dom";
import { Provider } from "react-redux";
import App from "./App";
import * as serviceWorker from "./serviceWorker";
import { store } from "./models";
import "./style/css/global.css";
import "./style/css/antd-global.less";

function render() {
    ReactDOM.render(
        <Provider store={store}>
            <App />
        </Provider>,
        document.querySelector("#root")
    );
}
render();
serviceWorker.unregister();
