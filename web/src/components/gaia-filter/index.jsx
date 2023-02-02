import React, { PureComponent } from "react";
import PropTypes from "prop-types";
import {
    Button,
    Input,
    Select,
    Space,
    DatePicker,
    Row,
    Col,
    Switch,
    Cascader
} from "antd";
import { DownOutlined, UpOutlined } from "@ant-design/icons";
import * as _ from "lodash";
import { connect } from "src/models";
import deepCompare from "src/commons/deepCompare";
import { If } from "../index";
import "./style.less";

const { RangePicker } = DatePicker;
export default class CompGaiaFilter extends PureComponent {
    static defaultProps = { fileds: [] };
    static propTypes = {
        fileds: PropTypes.array
    };

    state = {
        filedData: {},
        displayType: "single",
        showMore: false,
        isExpand: false
    };
    initValues = {};
    current_value_record = {};

    // 删格化
    rowCol = 8;
    // showmore
    maxField = 6;

    componentDidMount() {
        if (_.isInteger(this.props.colnum)) this.initCol();
        if (_.isArray(this.props.fileds)) this.initFileds("init");
    }

    initCol = () => {
        const { colnum = [] } = this.props;
        const rowcol = 24 / colnum;
        this.rowCol = rowcol;
        this.maxField = colnum * 2;
    };

    componentDidUpdate(prevProps) {
        if (
            !deepCompare(this.props.fileds, prevProps.fileds) &&
            _.isArray(this.props.fileds)
        )
            this.initFileds();
    }

    initFileds = flag => {
        const { fileds = [] } = this.props;
        let _filedData = {};
        _.forEach(fileds, o => {
            _filedData[o.name] = o.value ?? undefined;
            this.current_value_record[o.name] = o.value ?? undefined;
            if (flag == "init") this.initValues[o.name] = o.value ?? undefined;
        });
        this.setState({
            filedData: _filedData,
            showMore: fileds.length > this.maxField,
            displayType: fileds.length > 2 ? "multiple" : "single"
        });
    };

    handleCollectInput = (name, value) => {
        const { filedData } = this.state;
        const { onFiledChange } = this.props;
        let _filedData = _.cloneDeep(filedData);
        _filedData[name] =
            value != undefined || value != null
                ? typeof value == "string"
                    ? value.trim()
                    : value
                : undefined;
        onFiledChange && onFiledChange(name, _filedData);
        this.current_value_record = _filedData;
        this.setState({ filedData: _filedData });
    };

    handleReset = () => {
        const { onReset } = this.props;
        this.setState({ filedData: this.initValues });
        this.current_value_record = this.initValues;
        _.isFunction(onReset) && onReset(this.initValues);
    };

    handleSubmit = () => {
        const { filedData } = this.state;
        const { onSubmit } = this.props;
        _.isFunction(onSubmit) && onSubmit(_.cloneDeep(filedData));
    };

    getCurrentValues = () => {
        return this.current_value_record;
    };

    handleExpand = () => {
        const { isExpand } = this.state;
        this.setState({ isExpand: !isExpand });
    };

    render() {
        const { displayType, filedData, showMore, isExpand } = this.state;
        const { fileds = [] } = this.props;
        const rowCol = this.rowCol;
        return (
            <section
                styleName="comp-gaia-filter"
                style={{ display: "flex", flexDirection: "column" }}
            >
                <Row gutter={[32, 16]}>
                    {_.map(
                        showMore ? (isExpand ? fileds : fileds.slice(0, 6)) : fileds,
                        (o, idx) => {
                            let formVDom;
                            if (o.type === "input") {
                                formVDom = (
                                    <Input
                                        disabled={o.disabled}
                                        placeholder={o.placeholder}
                                        value={filedData[o.name]}
                                        onChange={ev => {
                                            this.handleCollectInput(
                                                o.name,
                                                ev.target.value
                                            );
                                        }}
                                    />
                                );
                            }

                            if (o.type === "rangeinput") {
                                formVDom = (
                                    <div style={{ display: "flex" }}>
                                        <div style={{ flex: 1 }}>
                                            <Input
                                                disabled={o.disabled}
                                                placeholder={o.placeholder}
                                                value={filedData[`${o.name}_start`]}
                                                onChange={ev => {
                                                    this.handleCollectInput(
                                                        `${o.name}_start`,
                                                        ev.target.value
                                                    );
                                                }}
                                            />
                                        </div>
                                        <span style={{ margin: "4px 10px 10px 10px" }}>
                                            -
                                        </span>
                                        <div style={{ flex: 1 }}>
                                            <Input
                                                disabled={o.disabled}
                                                placeholder={o.placeholder}
                                                value={filedData[`${o.name}_end`]}
                                                onChange={ev => {
                                                    this.handleCollectInput(
                                                        `${o.name}_end`,
                                                        ev.target.value
                                                    );
                                                }}
                                            />
                                        </div>
                                    </div>
                                );
                            }

                            if (o.type === "cascader") {
                                formVDom = (
                                    <Cascader
                                        placeholder="请选择"
                                        disabled={o.disabled}
                                        value={filedData[o.name]}
                                        options={o.options}
                                        onChange={val => {
                                            this.handleCollectInput(o.name, val);
                                        }}
                                    />
                                );
                            }
                            if (o.type === "select") {
                                formVDom = (
                                    <Select
                                        disabled={o.disabled}
                                        allowClear
                                        showSearch
                                        value={filedData[o.name]}
                                        options={o.options}
                                        mode={o.mode || ""}
                                        onChange={val => {
                                            this.handleCollectInput(o.name, val);
                                        }}
                                        optionFilterProp="children"
                                        filterOption={(inputValue, option) =>
                                            option.label
                                                .toLowerCase()
                                                .indexOf(inputValue.toLowerCase()) !== -1
                                        }
                                    />
                                );
                            }

                            if (o.type === "datepicker") {
                                if (o.single) {
                                    formVDom = (
                                        <DatePicker
                                            disabled={o.disabled}
                                            picker={o.picker}
                                            value={filedData[o.name]}
                                            onChange={val => {
                                                this.handleCollectInput(o.name, val);
                                            }}
                                            disabledDate={o.disabledDate}
                                        />
                                    );
                                } else {
                                    formVDom = (
                                        <RangePicker
                                            disabled={o.disabled}
                                            picker={o.picker}
                                            showTime={
                                                o.showTime
                                                    ? { format: "HH:mm:ss" }
                                                    : false
                                            }
                                            format={
                                                o.showTime
                                                    ? "YYYY-MM-DD HH:mm:ss"
                                                    : o.picker
                                                    ? "YYYY-MM"
                                                    : "YYYY-MM-DD"
                                            }
                                            value={filedData[o.name]}
                                            onChange={val => {
                                                this.handleCollectInput(o.name, val);
                                            }}
                                        />
                                    );
                                }
                            }

                            return (
                                <Col span={rowCol} key={idx}>
                                    <div
                                        style={{ display: "flex", alignItems: "center" }}
                                    >
                                        <div styleName="cgt-label">
                                            {o.notTranslate ? o.label : $I18N.t(o.label)}
                                            ：
                                        </div>
                                        {formVDom}
                                    </div>
                                </Col>
                            );
                        }
                    )}
                    <If data={fileds.length == 1 || fileds.length == 2}>
                        <Col span={8}>
                            <Space size="middle">
                                <Button type="primary" onClick={this.handleSubmit}>
                                    {$I18N.t("查询")}
                                </Button>
                                <Button onClick={this.handleReset}>
                                    {$I18N.t("重置")}
                                </Button>
                            </Space>
                        </Col>
                    </If>
                </Row>
                <If data={fileds.length > 2}>
                    <div style={{ display: "flex", alignItems: "center", marginTop:10 }}>
                        <Space size="middle">
                            <Button type="primary" onClick={this.handleSubmit}>
                                {$I18N.t("查询")}
                            </Button>
                            <Button onClick={this.handleReset}>{$I18N.t("重置")}</Button>
                        </Space>
                        <div style={{ flex: 1 }} />
                        <If data={showMore}>
                            <div
                                style={{
                                    display: "flex",
                                    alignItems: "center",
                                    cursor: "pointer"
                                }}
                                onClick={this.handleExpand}
                            >
                                <If data={!isExpand}>
                                    <DownOutlined
                                        style={{ color: "#0076FF", marginRight: 4 }}
                                    />
                                </If>
                                <If data={isExpand}>
                                    <UpOutlined
                                        style={{ color: "#0076FF", marginRight: 4 }}
                                    />
                                </If>
                                <span style={{ color: "#0076FF" }}>
                                    {isExpand ? "收起" : "展开"}
                                </span>
                            </div>
                        </If>
                    </div>
                </If>
            </section>
        );
    }
}
