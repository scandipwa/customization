//This script fixes incorrect positioning in modal datepickers.
//This file should be removed after magento fixes the datepickers.

require(['jquery', 'jquery/ui'], ($) => {
    $(window).on('load', () => {
        const show = $.datepicker._showDatepicker;
        if (show) {
            const datePicker = $.datepicker.dpDiv;

            $.extend($.datepicker, {
                _showDatepicker: (trigger) => {
                    show(trigger);

                    if ($(trigger).parents('.modal-slide').length > 0) {
                        const { top, height } = trigger.getBoundingClientRect();
                        datePicker.css('top', top + height);
                    }
                },
            });
        }
    });
});
