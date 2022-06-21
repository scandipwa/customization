//This script fixes incorrect positioning in modal datepickers.
//This file should be removed after magento fixes the datepickers.

require(['jquery'], ($) => {
    $(window).on('load', () => {
        const show = $.datepicker._showDatepicker;
        if (show) {
            const datePicker = $($.datepicker.dpDiv[0]);

            $.extend($.datepicker, {
                _showDatepicker: (trigger) => {
                    show(trigger);

                    if ($(trigger).parents('.modal-slide').length > 0) {
                        datePicker.css('top', `${datePicker.height() - 25}px`);
                    }
                },
            });
        }
    });
});
