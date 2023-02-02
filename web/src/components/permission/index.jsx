import React from 'react';
import PropTypes from 'prop-types';
import config from 'src/commons/configHoc';
import * as _ from "lodash";

/**
 * 根据hasPermission 和code 来判断children是否显示
 * 一般用于前端权限控制是否显示某个按钮等，一般的项目权限控制到菜单级别即可，很少会控制到功能级别
 */
@config({
    connect: state => {
        let modules = _.filter(state.user.permission, m => m['module_type'] === 'module');

        return {
            pModules: modules,
            pModuleKeys: _.map(modules, m => m['front_key'])
        }
    },
})
export default class Permission extends React.Component {
    static propTypes = {
        code: PropTypes.string.isRequired,
        useDisabled: PropTypes.bool,
    };

    static defaultProps = {
        useDisabled: false,
    };

    canDisplay = () => {
        const {pModuleKeys, code} = this.props;
        return _.includes(pModuleKeys, code)
    }
    canOperate = () => {
        const {pModules, code} = this.props;
        let module = _.find(pModules, m => m['front_key'] === code);
        return module.action == 'able';
    }

    render() {
        let {children} = this.props;

        if (!this.canDisplay()) return null;
        children = Array.isArray(children) ? children : [children];

        return children.map((item) => {
            const {key, ref} = item;
            return React.cloneElement(
                item,
                {
                    disabled: !this.canOperate(),
                    key,
                    ref,
                },
            );
        });
    }
}
