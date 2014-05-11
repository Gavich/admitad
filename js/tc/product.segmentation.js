var SegmentationBuilder = new Class.create();

SegmentationBuilder.prototype = {
    SEGMENT_FILTER_VALUE_NONE: 'none',

    definitionElSelector: 'input[name="general[segment_data]"]',
    segmentFilterElSelector: 'select[name="use_segment"]',

    initialize: function () {
    },

    /**
     * Builds segmentation wizard window
     */
    build: function () {
        var wrapEl = document.getElementById(TC.segmentation.wrapHTMLId),
            el = document.getElementById(TC.segmentation.HTMLId),
            $hiddenValueEl = $$(this.definitionElSelector)[0];

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
        if (!el.ruleObject) {
            // create rules object once, USE GLOBAL VARIABLE HERE FOR SELECT CHOOSERS
            el.ruleObject = rule_conditions_fieldset = new VarienRulesForm(TC.segmentation.formHTMLId, TC.segmentation.rulesNewChildURL);
        }
    },

    /**
     * Use this method instead of origin to reset segment fitler
     *
     * @param {object} gridObj
     */
    resetFilter: function (gridObj) {
        gridObj.reload(gridObj.addVarToUrl(gridObj.filterVar, ''));
        $$(this.segmentFilterElSelector)[0].setValue(this.SEGMENT_FILTER_VALUE_NONE);
    },

    /**
     * Use this method instead of origin to add segment definition to filters
     *
     * @param {object} gridObj
     */
    doFilter: function (gridObj) {
        var filters = $$(
            '#' + gridObj.containerId + ' .filter input',
            '#' + gridObj.containerId + ' .filter select',
            this.definitionElSelector,
            this.segmentFilterElSelector
        );
        var elements = [];
        for (var i in filters) {
            if (filters[i].value && filters[i].value.length) elements.push(filters[i]);
        }
        if (!gridObj.doFilterCallback || (gridObj.doFilterCallback && gridObj.doFilterCallback())) {
            gridObj.reload(gridObj.addVarToUrl(gridObj.filterVar, encode_base64(Form.serializeElements(elements))));
        }
    }
};
