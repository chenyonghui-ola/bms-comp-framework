import React, { PureComponent } from "react";
import PropTypes from "prop-types";
import { Space } from "antd";
import "./style.less";
import * as _ from "lodash";
import Item from "./item";

class GaiaForm extends PureComponent {
    static Item = Item;
    static defaultProps = {
        title: "",
        hideBoxShadow: false
    };
    static propTypes = {
        title: PropTypes.string,
        hideBoxShadow: PropTypes.bool
    };
    state = {
        verifying: false
    };
    validateFields = () => {
        this.setState({ verifying: true });

        let isVerify = true;
        let errMsg = "";

        React.Children.map(this.props.children, child => {
            let _required = _.get(child, "props.rules.required");
            let _value = _.get(child, "props.children.props.value");
            if (_required && !_.toString(_value) && isVerify) {
                isVerify = false;
                errMsg = "Check failed";
            }
        });
        return isVerify ? Promise.resolve(true) : Promise.reject({ message: errMsg });
    };

    render() {
        let index = 0;
        let _children = React.Children.toArray(this.props.children);
        let children = [];

        while (index < 34) {
            let subArrForChild = _.map(_.slice(_children, index, (index += 3)), child =>
                React.isValidElement(child)
                    ? React.cloneElement(child, { verifying: this.state.verifying })
                    : child
            );
            children.push(
                <Space size={20} styleName="cgf-row" key={index}>
                    {subArrForChild}
                </Space>
            );
        }

        return (
            <section
                styleName="comp-gaia-form"
                style={{
                    boxShadow: this.props.hideBoxShadow
                        ? "none"
                        : "0 1px 3px 0 rgba(0, 0, 0, 0.04)"
                }}
            >
                {this.props.title && (
                    <header styleName="cgf-header">{this.props.title}</header>
                )}
                <div styleName="cgf-body">{children}</div>
            </section>
        );
    }
}

export default GaiaForm;
