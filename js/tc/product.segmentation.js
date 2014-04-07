var SegmentationBuilder = new Class.create();

SegmentationBuilder.prototype = {
    _defaults: {
        dialogWindowId: 'segmentation-builder'
    },
    options: {},
    dialogWindow: null,
    initialize: function (options) {
        options = options || {};
        this.options = Object.extend(this._defaults, options);
    },

    /**
     * Builds segmentation wizard window
     */
    build: function () {
        this.dialogWindow = Dialog.info('', {
            draggable: true,
            resizable: true,
            closable: true,
            className: "magento",
            title: 'Segmentation builder',
            width: 700,
            //height:270,
            recenterAuto: true,
            hideEffect: Element.hide,
            showEffect: Element.show,
            id: this.options.dialogWindowId
        });
    }
};

/*
 @TODO
 - load data once
 - restore from hidden data
 */
