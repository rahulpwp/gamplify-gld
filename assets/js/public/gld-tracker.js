/**
 * Frontend Tracker JavaScript
 */
(function ($) {
    'use strict';

    // Track custom events
    window.gldTrackEvent = function (eventType, eventName, eventData) {
        if (typeof gld_public === 'undefined') {
            return;
        }

        $.ajax({
            url: gld_public.ajax_url,
            type: 'POST',
            data: {
                action: 'gld_track_event',
                nonce: gld_public.nonce,
                event_type: eventType,
                event_name: eventName,
                event_data: eventData || {}
            }
        });
    };

    // Track clicks (if enabled)
    $(document).on('click', 'a[data-gld-track]', function () {
        var $this = $(this);
        var eventName = $this.data('gld-track') || 'Link Click';
        var eventData = {
            url: $this.attr('href'),
            text: $this.text()
        };

        window.gldTrackEvent('click', eventName, eventData);
    });

    // Track form submissions (if enabled)
    $(document).on('submit', 'form[data-gld-track]', function () {
        var $this = $(this);
        var eventName = $this.data('gld-track') || 'Form Submit';
        var eventData = {
            form_id: $this.attr('id') || '',
            form_action: $this.attr('action') || ''
        };

        window.gldTrackEvent('form_submit', eventName, eventData);
    });

})(jQuery);
