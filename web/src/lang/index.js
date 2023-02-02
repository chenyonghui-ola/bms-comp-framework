import i18n from "i18next";
import { useTranslation, initReactI18next } from "react-i18next";
import zhCn from "./zh/index";
import en from "./en/index";

const lng = localStorage.getItem("lang") || "zh";
i18n.use(initReactI18next).init({
    resources: {
        en: {
            translation: en
        },
        zh: {
            translation: zhCn
        }
    },
    lng,
    fallbackLng: "zh",

    interpolation: {
        escapeValue: false
    }
});

export default i18n;
