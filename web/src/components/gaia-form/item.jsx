import React from "react";
import "./style.less";
import PropTypes from "prop-types";
import * as _ from "lodash";

class GaiaFormItem extends React.Component {
    static defaultProps = {
        title: "",
        verifying: "false"
    };
    static propTypes = {
        title: PropTypes.string,
        rules: PropTypes.object
    };

    render() {
        let { label: labelTxt, children, key, verifying } = this.props;
        let required = _.get(this.props, "rules.required");
        let message = _.get(this.props, "rules.message");
        let value = _.get(this.props, "children.props.value");

        return (
            <div
                styleName="comp-gaia-form-item"
                key={key}
                className={this.props.className ? this.props.className : ""}
            >
                <div style={{ display: "flex", alignItems: "center" }}>
                    <label styleName="cgfi-label">
                        <span styleName={required ? "cgfi-required-star" : ""}>*</span>
                        {labelTxt}：
                    </label>
                    {children}
                </div>

                <div styleName="cgfi-message">
                    {required && verifying && message && !_.toString(value) ? message : ""}
                </div>
            </div>
        );
    }
}

export default GaiaFormItem;
