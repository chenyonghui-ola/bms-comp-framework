/**
 * 文件下载方法
 * @param {y} url
 * @param {*} filename
 */
 export default function fileDownload(url, filename) {
    let tempLink = document.createElement("a")
    tempLink.style.display = "none"
    tempLink.href = url
    tempLink.setAttribute("download", filename)
    if (typeof tempLink.download === "undefined") {
        tempLink.setAttribute("target", "_blank")
    }
    document.body.appendChild(tempLink)
    tempLink.click()
    setTimeout(function () {
        document.body.removeChild(tempLink)
    }, 200)
}