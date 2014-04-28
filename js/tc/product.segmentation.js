var SegmentationBuilder = new Class.create();

SegmentationBuilder.prototype = {

    initialize: function () {
    },

    /**
     * Builds segmentation wizard window
     */
    build: function () {
        var wrapEl = document.getElementById(TC.segmentation.wrapHTMLId),
            el = document.getElementById(TC.segmentation.HTMLId),
            $hiddenValueEl = $$('input[name="general[segment_data]"]')[0];

        Dialog.info('', {
            draggable: true,
            resizable: true,
            closable: true,
            className: "magento",
            title: Translator.translate('Segmentation builder'),
            width: 700,
            height: 500,
            recenterAuto: true,
            hideEffect: Element.hide,
            showEffect: Element.show,
            closeCallback: function () {
                // move HTML elements back from window to wrapper (full DOM tree with events)
                wrapEl.appendChild(el);

                // update hidden value
                $hiddenValueEl.setValue($(TC.segmentation.formHTMLId).serialize());
                return true;
            }
        });

        // move HTML elements from wrapper to window (full DOM tree with events)
        document.getElementById('modal_dialog_message').appendChild(el);
        console.log(el.ruleObject);
        if (!el.ruleObject) {
            // create rules object once
            el.ruleObject = new VarienRulesForm(TC.segmentation.formHTMLId, TC.segmentation.rulesNewChildURL);
        }
    }
};
