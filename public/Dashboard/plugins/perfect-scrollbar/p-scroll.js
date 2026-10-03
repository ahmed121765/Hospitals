(function ($) {
    "use strict";

    //P-scrolling
    if ($(".chat-scroll").length) {
        new PerfectScrollbar(".chat-scroll", {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }
    if ($(".Notification-scroll").length) {
        new PerfectScrollbar(".Notification-scroll", {
            useBothWheelAxes: true,
            suppressScrollX: true,
        });
    }
})(jQuery);
