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

            var $btn = $(this);
            var metricType = $('#metric-type').val();
            var course = $('#filter-course').val();
            var chart = $('#chart-version').val();

            if (!metricType) {
                alert('Please select a metric type');
                return;
            }

            // Disable button during request
            $btn.prop('disabled', true).text('Saving...');

            // Prepare data for AJAX
            var data = {
                action: 'gld_save_member_kpi',
                nonce: gld_admin.nonce,
                title: metricType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase()),
                metric_type: metricType,
                filter_by_course: course,
                include_chart_version: chart
            };

            // Send AJAX request
            $.ajax({
                url: gld_admin.ajax_url,
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response.success) {
                        // Generate shortcode string
                        var shortcode = '[gld_membership metric="' + metricType + '"';
                        if (course && course !== 'all') shortcode += ' course="' + course + '"';
                        if (chart && chart !== 'no_chart') shortcode += ' chart="' + chart + '"';
                        shortcode += ']';

                        // Add to table
                        addShortcodeRow('Membership', data.title, course, shortcode);
                        
                        alert(response.data.message);
                    } else {
                        alert(response.data.message || 'Error occurred');
                    }
                },
                error: function (xhr, status, error) {
                    console.error(error);
                    alert('AJAX Error: ' + error);
                },
                complete: function () {
                    // Re-enable button
                    $btn.prop('disabled', false).text('Generate Shortcode');
                }
            });
        });

        // Other generators can be added here
        $('.gld-generate-btn').not('#generate-membership-shortcode').on('click', function (e) {
            e.preventDefault();
            alert('Shortcode generated! (Demo)');
        });

        // Initial load of Member KPIs
        if ($('#membership-shortcodes-list').length) {
            loadMemberKpis(1);
        }

        // Pagination click handler
        $(document).on('click', '.gld-pagination-link', function(e) {
            e.preventDefault();
            var page = $(this).data('page');
            loadMemberKpis(page);
        });

        // Copy button click handler
        $(document).on('click', '.copy-btn', function(e) {
            e.preventDefault();
            var $btn = $(this);
            var shortcode = $btn.closest('tr').find('code').text();
            
            navigator.clipboard.writeText(shortcode).then(function() {
                var originalText = $btn.text();
                $btn.text('Copied!');
                setTimeout(function() {
                    $btn.text(originalText);
                }, 2000);
            }, function(err) {
                console.error('Could not copy text: ', err);
                alert('Failed to copy to clipboard');
            });
        });

        // Delete button click handler
        $(document).on('click', '.delete-btn', function(e) {
            e.preventDefault();
            if (!confirm('Are you sure you want to delete this specific Member KPI metric?')) {
                return;
            }

            var $btn = $(this);
            var id = $btn.data('id');
            var $row = $btn.closest('tr');

            // Disable button
            $btn.prop('disabled', true);

            $.ajax({
                url: gld_admin.ajax_url,
                type: 'POST',
                data: {
                    action: 'gld_delete_member_kpi',
                    nonce: gld_admin.nonce,
                    id: id
                },
                success: function(response) {
                    if (response.success) {
                        // Reload data to stay consistent with pagination
                        var currentPage = $('.gld-pagination-link.disabled').text() || 1;
                        loadMemberKpis(currentPage);
                        // alert(response.data.message);
                    } else {
                        alert(response.data.message || 'Error occurred');
                        $btn.prop('disabled', false);
                    }
                },
                error: function(xhr, status, error) {
                    console.error(error);
                    alert('AJAX Error: ' + error);
                    $btn.prop('disabled', false);
                }
            });
        });
    }

    /**
     * Load Member KPIs via AJAX
     */
    function loadMemberKpis(page) {
        var $tbody = $('#membership-shortcodes-list');
        var $pagination = $('#gld-kpi-pagination');
        
        // Show loading state if needed (optional)
        $tbody.css('opacity', '0.5');

        $.ajax({
            url: gld_admin.ajax_url,
            type: 'POST',
            data: {
                action: 'gld_get_member_kpis',
                nonce: gld_admin.nonce,
                page: page
            },
            success: function(response) {
                $tbody.css('opacity', '1');
                if (response.success && response.data.items) {
                    $tbody.empty();
                    
                    if (response.data.items.length === 0) {
                        $tbody.html('<tr class="no-items"><td colspan="6" class="gld-no-items">No shortcodes found.</td></tr>');
                        $pagination.empty();
                        return;
                    }

                    $.each(response.data.items, function(index, item) {
                        var course = item.filter_by_course || 'all';
                        var chart = item.include_chart_version || 'no';
                        
                        // Reconstruct shortcode
                        var shortcode = '[gld_membership metric="' + item.metric_type + '"';
                        if (course && course !== 'all' && course !== '0') shortcode += ' course="' + course + '"';
                        if (chart && chart !== 'no' && chart !== '0' && chart !== 'no_chart') shortcode += ' chart="' + chart + '"';
                        shortcode += ']';

                        var created = new Date(item.created).toLocaleDateString();

                        var row = '<tr>' +
                            '<td>Membership</td>' +
                            '<td>' + item.title + '</td>' +
                            '<td>' + (course === '0' ? 'All' : course) + '</td>' +
                            '<td><code>' + shortcode + '</code></td>' +
                            '<td>' + created + '</td>' +
                            '<td>' +
                                '<button class="button button-small copy-btn" style="margin-right: 5px;">Copy</button>' +
                                '<button class="button button-small delete-btn" data-id="' + item.id + '"><span class="dashicons dashicons-trash" style="margin-top: 3px;"></span></button>' +
                            '</td>' +
                            '</tr>';
                        
                        $tbody.append(row);
                    });

                    renderPagination(response.data.pagination);
                }
            }
        });
    }

    /**
     * Render Pagination
     */
    function renderPagination(pagination) {
        var $pagination = $('#gld-kpi-pagination');
        $pagination.empty();

        if (pagination.total_pages <= 1) {
            return;
        }

        var html = '';
        var current = pagination.current_page;
        var total = pagination.total_pages;

        // Prev
        if (current > 1) {
            html += '<a href="#" class="button gld-pagination-link" data-page="' + (current - 1) + '">&laquo; Prev</a> ';
        }

        // Page numbers
        for (var i = 1; i <= total; i++) {
            if (i === current) {
                html += '<span class="button button-primary disabled">' + i + '</span> ';
            } else {
                html += '<a href="#" class="button gld-pagination-link" data-page="' + i + '">' + i + '</a> ';
            }
        }

        // Next
        if (current < total) {
            html += '<a href="#" class="button gld-pagination-link" data-page="' + (current + 1) + '">Next &raquo;</a>';
        }

        $pagination.html(html);
    }

    /**
     * Helper to add a row to the shortcodes table
     */
    function addShortcodeRow(type, title, course, shortcode) {
        // Reload list to respect pagination logic or prepend if on first page
        loadMemberKpis(1);
    }

})(jQuery);
