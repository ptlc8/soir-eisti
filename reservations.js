function popupQR(code, pseudo) {
    var qr;
    popup([
        qr = createElement("div", {className:"qr"}),
        createElement("span", {className:"infos"}, [
            createElement("b", {}, code),
            createElement("span", {}, " par "),
            createElement("b", {}, pseudo)
        ])
    ]);
    new QRCode(qr, code);
}