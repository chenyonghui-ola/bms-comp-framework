/**
 * 当前登录用户信息
 */

import { ajax } from "src/library/ajax";
// import { setLoginUser } from "src/commons/loginFn"

const _ = require("lodash");

export default {
    initialState: {
        loading: false,
        user_id: void 0, // 用户ID
        user_name: void 0, // 用户名
        user_email: void 0,
        menus: [],
        pages: [],
        points: [],
        permission: [],
        hiddenFundDot: localStorage.getItem("hiddenDot") == "0" ? false : true
    },

    setTodoCount: todoCount => ({ todoCount }),
    setPermissions: permission => ({ permission }),
    setHiddenFundDot: hiddenFundDot => ({ hiddenFundDot }),
    setUserInfo: data => {
        return _.pick(data, ["user_id", "user_name", "user_email", "menus", "pages", "points"]);
    },

    // fetchUser: {
    //     payload: () => ajax.get("/api/user/info"),
    //     reducer: {
    //         pending: () => ({ loading: true }),
    //         resolve(state, { payload = {} }) {
    //             console.log(payload,11111)
    //             let data = _.get(payload, "data", {})
    //             setLoginUser(payload?.data)
    //             return _.pick(data, ["user_id", "user_name", "user_email", "menus", "pages", "points"])
    //         },
    //         complete: () => ({ loading: false })
    //     }
    // },
    fetchTodoCount: {
        payload: () => ajax.get("/api/user/info"),
        reducer: {
            pending: () => ({ loading: true }),
            resolve(state, { payload = {} }) {
                return {
                    todoCount: payload.data?.todo_count
                };
            },
            complete: () => ({ loading: false })
        }
    }
};
