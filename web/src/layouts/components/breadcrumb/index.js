import React, { Component } from 'react'
import PropTypes from 'prop-types'
import Icon from "src/components/icon"
import { Breadcrumb } from 'antd'
import Link from 'src/components/page-link'
import './style.less'


export default class CompBreadcrumb extends Component {
    static propTypes = {
        dataSource: PropTypes.array, // 数据源
    };

    static defaultProps = {
        dataSource: [],
    };

    renderItems() {
        const { dataSource } = this.props;
        const iconStyle = { marginRight: 4 };

        if (dataSource && dataSource.length) {
            return dataSource.map(({ key, icon, text, path }) => path ? (
                <Breadcrumb.Item key={key}>
                    <Link to={path}>{icon ? <Icon type={icon} style={iconStyle} /> : null} {text}</Link>
                </Breadcrumb.Item>
            ) : (
                <Breadcrumb.Item key={key}>
                    {icon ? <Icon type={icon} style={iconStyle} /> : null} {text}
                </Breadcrumb.Item>
            ));
        } else {
            return null;
        }
    }

    render() {
        const { theme } = this.props;
        return (
            <div styleName="breadcrumb" className={`system-breadcrumb-${theme}`}>
                <Breadcrumb>
                    {this.renderItems()}
                </Breadcrumb>
            </div>
        );
    }
}
