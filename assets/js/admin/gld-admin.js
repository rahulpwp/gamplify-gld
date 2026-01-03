/**
 * Admin JavaScript
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        initTabs();
        initSubTabs();
        initShortcodeGenerators();
    });

    /**
     * Initialize main tabs switching
     */
    function initTabs() {
        $('.gld-tab-navigation .gld-tab').on('click', function () {
            var $tab = $(this);
            var tabId = $tab.data('tab');

            // Toggle active class on tabs
            $('.gld-tab-navigation .gld-tab').removeClass('active');
            $tab.addClass('active');

            // Toggle content visibility
            $('.gld-main-tab-content').hide();
            $('#tab-' + tabId).fadeIn(200);
        });
    }

    /**
     * Initialize sub-tabs interaction (like in Membership tab)
     */
    function initSubTabs() {
        $('.gld-sub-tab').on('click', function (e) {
            e.preventDefault();

            var $btn = $(this);
            var targetId = $btn.data('subtab');
            var $container = $btn.closest('.gld-tab-panel');

            // Toggle buttons
            $btn.siblings().removeClass('active');
            $btn.addClass('active');

            // Toggle content
            $container.find('.gld-sub-content').hide();
            $container.find('#subtab-' + targetId).fadeIn(200);
        });
    }

    /**
     * Initialize shortcode generation buttons
     */
    function initShortcodeGenerators() {
        // Membership Shortcode Generator
        $('#generate-membership-shortcode').on('click', function (e) {
            e.preventDefault();

            var metricType = $('#metric-type').val();
            var course = $('#filter-course').val();
            var chart = $('#chart-version').val();

            if (!metricType) {
                alert('Please select a metric type');
                return;
            }

            // Generate dummy shortcode for preview
            var shortcode = '[gld_membership metric="' + metricType + '"';
            if (course && course !== 'all') shortcode += ' course="' + course + '"';
            if (chart && chart !== 'no_chart') shortcode += ' chart="' + chart + '"';
            shortcode += ']';

            addShortcodeRow('Membership', metricType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()), course, shortcode);
        });

        // Other generators can be added here
        $('.gld-generate-btn').not('#generate-membership-shortcode').on('click', function (e) {
            e.preventDefault();
            alert('Shortcode generated! (Demo)');
        });
    }

    /**
     * Helper to add a row to the shortcodes table
     */
    function addShortcodeRow(type, title, course, shortcode) {
        var $tbody = $('#membership-shortcodes-list');
        var now = new Date().toLocaleDateString();

        // Remove "no items" row if present
        $tbody.find('.no-items').remove();

        var row = '<tr>' +
            '<td>' + type + '</td>' +
            '<td>' + title + '</td>' +
            '<td>' + (course || 'All') + '</td>' +
            '<td><code>' + shortcode + '</code></td>' +
            '<td>' + now + '</td>' +
            '<td><button class="button button-small copy-btn">Copy</button></td>' +
            '</tr>';

        $tbody.prepend(row);
    }

})(jQuery);
