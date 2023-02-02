import React, { Component } from "react";
import PropTypes from "prop-types";
import logo from "src/style/image/logo.png";
import veekaSvg from "src/style/image/veeka_logo.svg";
import { If } from "src/components";
import "./style.less";

export default class Logo extends Component {
    static propTypes = {
        min: PropTypes.bool
    };
    static defaultProps = {
        logo: logo,
        title: "Web",
        min: false
    };

    render() {
        const { min, title, ...others } = this.props;
        return (
            <div
                styleName={
                    min ? "comp-header-logo comp-header-logo-min" : "comp-header-logo"
                }
            >
                <If data={min}>
                    <img src={logo} alt="logo" />
                </If>
                <If data={!min}>
                    <img src={veekaSvg} alt="logo" style={{ width: 140, height: 48 }} />
                </If>
            </div>
        );
    }
}
