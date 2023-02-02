import React, { PureComponent } from "react";
import PropTypes from "prop-types";
import { Table, Space, Tooltip, Dropdown, Menu, Spin, Popover, Button, Checkbox } from "antd";
import ReactDragListView from "react-drag-listview";
import * as _ from "lodash";
import moment from "moment";
import deepCompare from "src/commons/deepCompare";
import { If } from "./index";
import { cacheColumns, getCacheColumns, getCacheInitColumns } from "../help";
import "./Table.less";

export default class CompGaiaTable extends PureComponent {
    static defaultProps = {
        columns: [],
        dataSource: []
    };
    static propTypes = {
        columns: PropTypes.array,
        dataSource: PropTypes.array
    };
    state = {
        columns: [],
        offsetHeader: 0,
        selectedRowKeys: [],
        showTitleTip: false
    };
    offsetHeaderRecord = 0;

    componentDidMount() {
        let columns = this.props?.columns;
        if (_.isArray(columns) && !_.isEmpty(columns)) this.initConfig();
        window.addEventListener("scroll", this.handleScroll);
    }

    handleScroll = () => {
        const tableWrapperDom = document.querySelector(".ant-table-wrapper");
        const top = tableWrapperDom && tableWrapperDom.getBoundingClientRect().top;
        if (top < 120) {
            this.offsetHeaderRecord != 94 &&
                requestAnimationFrame(() => {
                    this.setState({ offsetHeader: 94 });
                    this.offsetHeaderRecord = 94;
                });
        } else {
            this.offsetHeaderRecord != 0 &&
                requestAnimationFrame(() => {
                    this.setState({ offsetHeader: 0 });
                    this.offsetHeaderRecord = 0;
                });
        }
    };

    componentWillUnmount() {
        window.removeEventListener("scroll", this.handleScroll);
    }

    componentDidUpdate(prevProps) {
        if (
            !deepCompare(this.props.dataSource, prevProps.dataSource) ||
            this.props.loading != prevProps.loading ||
            !deepCompare(this.props.columns, prevProps.columns)
        ) {
            this.initConfig();
        }
    }

    handleColumns = param => {
        const FontFamilyDict = [
            "-apple-system",
            "BlinkMacSystemFont",
            "Segoe UI",
            "Roboto",
            "Oxygen",
            "Ubuntu",
            "Cantarell",
            "Fira Sans",
            "Droid Sans",
            "Helvetica Neue",
            "sans-serif"
        ];
        const fontFamily = _.find(FontFamilyDict, s => isSupportFontFamily(s));
        const canvas = document.createElement("canvas");
        const context = canvas.getContext("2d");
        context.font = "14px " + fontFamily;
        let columns = param || this.props.columns || [];
        let loading = this.props?.loading;
        let dataSource = this.props?.dataSource || [];
        _.forEach(columns, o => {
            const menu = (
                <Menu>
                    <Menu.Item>
                        <span onClick={() => this.handleHideColumn(o.dataIndex)}>隐藏此列</span>
                    </Menu.Item>
                </Menu>
            );
            if (typeof o.title == "string") {
                o.title = (
                    // <Dropdown placement="topLeft" overlay={menu} arrow>
                    <span>{o.title}</span>
                    // </Dropdown>
                );
            }
            if (o.formatDate) {
                o.render = text => (text > 0 ? moment.unix(text).format("YYYY-MM-DD HH:mm:ss") : "");
            }
            if (o.isTooltip) {
                o.render = text =>
                    text ? (
                        <Tooltip title={text} placement="topLeft">
                            {text}
                        </Tooltip>
                    ) : (
                        text
                    );
            } else {
                o.ellipsis = true;
            }
            if (o.style) {
                const { width } = o.style;
                o.render = text => <div style={{ width, whiteSpace: "pre-wrap", wordBreak: "break-all" }}>{text}</div>;
            }
            if (o.width) return;
            let textArr = [];
            _.forEach(dataSource, p => textArr.push(p[o.dataIndex]));
            let textWidthArr = [context.measureText(o.title).width];
            textWidthArr = _.concat(
                textWidthArr,
                _.map(textArr, s => {
                    return context.measureText(s).width + 26 + 4;
                })
            );
            o.width = _.min([_.ceil(_.max(textWidthArr)), 300]);
        });
        return [columns, loading];
    };

    initConfig = () => {
        const [columns, loading] = this.handleColumns();
        const newKeys = columns.map(item => item.dataIndex);
        let cacheColumns = getCacheColumns()?.filter(item => newKeys.includes(item));
        if (cacheColumns && cacheColumns.length > 0) {
            const newColumns = columns.slice(0).filter(item => cacheColumns.includes(item?.dataIndex));
            columns.length = 0;
            cacheColumns.map((fitem, findex) => {
                columns[findex] = newColumns.find(item => item.dataIndex == fitem);
            });
        }
        this.initColumns = getCacheInitColumns();
        this.setState({ columns, loading });
    };

    handleOnSelectRowsChange = selectedRowKeys => {
        this.props.rowSelection(selectedRowKeys);
    };

    dragProps = {
        onDragEnd: (fromIndex, toIndex) => {
            let newFromIndex = fromIndex;
            let newToIndex = toIndex;
            if (this.props.rowSelection) {
                newFromIndex = newFromIndex - 1;
                newToIndex = newToIndex - 1;
            }
            const copyColumns = this.state.columns.slice(0);
            const item = copyColumns.splice(newFromIndex, 1)[0];
            copyColumns.splice(newToIndex, 0, item);
            cacheColumns(copyColumns.map(item => item.dataIndex));
            this.setState({
                columns: copyColumns
            });
        },
        nodeSelector: "th"
    };

    handleOnPageChange = (page, pageSize) => {
        this.setState({ offsetHeader: 0 });
        this.props.pagination.onChange && this.props.pagination.onChange(page, pageSize);
    };

    handleHideColumn = dataIndex => {
        setTimeout(() => {
            const copyColumns = this.state.columns.slice(0);
            const index = copyColumns.findIndex(item => item.dataIndex == dataIndex);
            copyColumns.splice(index, 1);
            this.setState({ columns: copyColumns });
        }, 0);
    };

    componentWillUnmount() {
        // localStorage.removeItem("currentColumns");
    }

    handleOnColumnsChange = (value = []) => {
        const new_columns = this.initColumns.slice(0).filter(item => value.includes(item.dataIndex));
        this.props.columnsCallBack && this.props.columnsCallBack(new_columns);
        cacheColumns(new_columns.map(item => item.dataIndex));
    };

    render() {
        const { columns, loading, selectedRowKeys, offsetHeader } = this.state;
        let tableProps = { ...this.props };
        delete tableProps["children"];
        tableProps["columns"] = columns;
        tableProps["loading"] = loading;
        if (this.props.scrollY) {
            tableProps["scroll"] = { x: "max-content", y: this.props.scrollY };
        } else {
            tableProps["scroll"] = { x: "max-content" };
        }
        if (this.props.rowSelection) {
            tableProps["rowSelection"] = {
                onChange: this.handleOnSelectRowsChange
            };
        }
        if (this.props.pagination) {
            tableProps["pagination"] = _.merge(
                {
                    showTotal: total => `共 ${total} 条`
                },
                {
                    showSizeChanger: true,
                    showQuickJumper: true,
                    pageSize: this.props.pagination.defaultPageSize || 15,
                    pageSizeOptions: this.props.pagination.pageSizeOptions || [15, 30, 50]
                },
                this.props.pagination,
                {
                    onChange: this.handleOnPageChange
                }
            );
        }
        if (_.some(this.props.dataSource, o => !o.key)) {
            tableProps["dataSource"] = _.map(this.props.dataSource, (o, idx) => ((o.key = idx + ""), o));
        }
        tableProps["rowKey"] = record => record[this.props.pk] || record.key || record.id;
        const content = (
            <div style={{ width: 400 }}>
                <Checkbox.Group
                    options={this.initColumns?.map(item => ({
                        label: item.title,
                        value: item.dataIndex
                    }))}
                    value={this.state.columns?.map(item => item.dataIndex)}
                    onChange={this.handleOnColumnsChange}
                />
            </div>
        );

        return (
            <section styleName="comp-gaia-table">
                <div styleName="cga-tools">
                    <Space>{this.props.children}</Space>
                    <div styleName="empty" />
                    <If data={this.props.selectColumns}>
                        <Popover content={content} placement="topRight">
                            <Button type="primary">动态选择列</Button>
                        </Popover>
                    </If>
                </div>
                <ReactDragListView.DragColumn {...this.dragProps}>
                    <Table {...tableProps} sticky={{ offsetHeader }} size="small" />
                </ReactDragListView.DragColumn>
            </section>
        );
    }
}

//----------------------------------- helps --------------------------------
function isSupportFontFamily(f) {
    if (typeof f != "string") {
        return false;
    }
    var h = "Arial";
    if (f.toLowerCase() == h.toLowerCase()) {
        return true;
    }
    var e = "a";
    var d = 100;
    var a = 100,
        i = 100;
    var c = document.createElement("canvas");
    var b = c.getContext("2d");
    c.width = a;
    c.height = i;
    b.textAlign = "center";
    b.fillStyle = "black";
    b.textBaseline = "middle";
    var g = function (j) {
        b.clearRect(0, 0, a, i);
        b.font = d + "px " + j + ", " + h;
        b.fillText(e, a / 2, i / 2);
        var k = b.getImageData(0, 0, a, i).data;
        return [].slice.call(k).filter(function (l) {
            return l != 0;
        });
    };
    return g(h).join("") !== g(f).join("");
}
