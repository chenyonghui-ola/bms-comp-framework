import {Image} from "antd";
import React from "react";

export const formatMsg = (obj = {}) => {
    const { object_name, content } = obj;
    const contentObj = content ? JSON.parse(content) : {};
    const { content: innerContent, imageUri, extra = "{}" } = contentObj;
    const extraObj = JSON.parse(extra);
    switch (object_name) {
        case "RC:VcMsg":
            return <audio src={innerContent} controls />;
        case "RC:ImgMsg":
            return <Image width={48} height={48} src={imageUri} />;
        case "RC:TxtMsg":
            if (extraObj.icon) {
                const { width, height, icon, type } = extraObj;
                let file;
                if (type == 'emoji') {
                    const [one, two, three] = icon.split(".");
                    file =
                        require(`../../style/emote/${one}/${two}.${three}`).default ||
                        require(`../../style/emote/${one}/${two}.${three}`);
                } else {
                    file = process.env.VEEKA_IMAGE_URL + '/' + icon;
                }

                return (
                    <img
                        src={file}
                        alt=""
                        style={{ width: `${width}px`, height: `${height}px` }}
                    />
                );

            } else {
                return <span>{innerContent}</span>;
            }
        default:
            break;
    }
};