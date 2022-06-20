//This script fixes incorrect positioning in modal datepickers.
//This file should be removed after magento fixes the datepickers.

require(["jquery"], function ($) {
    $(window).on("load", function () {
        if (window.location.pathname.includes("/admin/catalog/product/edit/id")) {
            (($) => {
                const show = $.datepicker._showDatepicker;
                const datePicker = $($.datepicker.dpDiv[0]);
                $.extend($.datepicker, {
                    _showDatepicker: (trigger) => {
                        show(trigger);

                        if ($(trigger).parents(".modal-slide").length > 0) {
                            datePicker.css("top", `${datePicker.height() - 25}px`);
                        }
                    }
                });
            })(jQuery);
        }
    });
});
