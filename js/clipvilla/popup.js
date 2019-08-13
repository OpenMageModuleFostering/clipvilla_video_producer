function openPopup(url, name) {
    var dialogWindow = Dialog.info(null, {
        id: 'browser_window',
        className: 'magento',
        windowClassName: 'popup-window',
        title: name,
        url: url,
        width: 808,
        height: 457,
        zIndex: 1000,
        closable: true,
        resizable: false,
        draggable: false,
        minimizable: false,
        maximizable: false,
        recenterAuto: false,
        hideEffect: Element.hide,
        showEffect: Element.show,
        destroyOnClose: true
    });
}

function closePopup() {
    Windows.close('browser_window');
}