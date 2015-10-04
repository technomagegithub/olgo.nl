function previewAction(formId, formObj, url) {
    var formElem = $(formId);
    formElem.writeAttribute('target', '_blank');
    formObj.submit(url);
    formElem.writeAttribute('target', '');
}