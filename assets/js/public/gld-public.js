/**
 * Public JavaScript for Gamplify GLD
 */
(function ($) {
    'use strict';

    $(document).ready(function () {
        initLearningTables();
    });

    /**
     * Initialize Learning Tables (Pagination & Modal)
     */
    function initLearningTables() {
        // Pagination
        $(document).on('click', '.gld-page-link', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $container = $btn.closest('.gld-table-container');
            var page = $btn.data('page');
            var id = $container.data('id');
            var nonce = $container.data('nonce');

            if ($btn.hasClass('active')) return;

            loadTableData($container, id, page, nonce);
        });

        // View Users Modal
        $(document).on('click', '.gld-view-users-btn', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var $container = $btn.closest('.gld-table-container');
            var courseId = $btn.data('course-id');
            var nonce = $container.data('nonce');
            var $modal = $('#gld-user-modal');

            if ($modal.length === 0) {
                // If modal is not in current container scope (though it should be in shortcode)
                // Fallback to searching globally
                $modal = $('#gld-user-modal');
            }

            openUserModal($modal, courseId, nonce);
        });

        // Close Modal
        $(document).on('click', '.gld-modal-close, .gld-modal-overlay', function (e) {
            if ($(e.target).hasClass('gld-modal-overlay') || $(e.target).hasClass('gld-modal-close')) {
                $('#gld-user-modal').fadeOut(200, function() {
                    $(this).addClass('gld-hidden');
                });
            }
        });

        // Tab Switching in Modal
        $(document).on('click', '.gld-tab-btn', function (e) {
            e.preventDefault();
            var $btn = $(this);
            var tabId = $btn.data('tab');
            
            $btn.addClass('active').siblings().removeClass('active');
            $btn.closest('.gld-modal-drilldown').find('.gld-tab-content').removeClass('active');
            $('#gld-tab-' + tabId).addClass('active');
        });
    }

    /**
     * Load table data via AJAX
     */
    function loadTableData($container, id, page, nonce) {
        var $tbody = $container.find('.gld-table-body');
        var $pagination = $container.find('.gld-table-pagination');

        $tbody.css('opacity', '0.5');

        $.ajax({
            url: gld_public.ajax_url,
            type: 'POST',
            data: {
                action: 'gld_get_public_learning_table',
                id: id,
                page: page,
                nonce: nonce
            },
            success: function (response) {
                if (response.success) {
                    $tbody.html(response.data.html);
                    updatePagination($pagination, response.data.pages, response.data.current_page);
                }
            },
            complete: function () {
                $tbody.css('opacity', '1');
            }
        });
    }

    /**
     * Update pagination buttons
     */
    function updatePagination($container, totalPages, currentPage) {
        $container.empty();
        for (var i = 1; i <= totalPages; i++) {
            var activeClass = i === currentPage ? 'active' : '';
            $container.append('<button class="gld-page-link ' + activeClass + '" data-page="' + i + '">' + i + '</button>');
        }
    }

    /**
     * Open User Modal and load content
     */
    function openUserModal($modal, courseId, nonce) {
        var $content = $modal.find('#gld-modal-user-list-content');
        var $placeholder = $content.find('.gld-modal-placeholder');
        var $spinner = $placeholder.find('.spin');
        var $msg = $placeholder.find('.gld-modal-msg');
        var $title = $modal.find('.gld-modal-title');

        $modal.removeClass('gld-hidden').fadeIn(200);
        $content.find('.gld-modal-drilldown').remove();
        
        $spinner.removeClass('gld-hidden');
        $msg.text('Loading course details...');
        $title.text('Course Details');

        $.ajax({
            url: gld_public.ajax_url,
            type: 'POST',
            data: {
                action: 'gld_get_course_drilldown',
                course_id: courseId,
                nonce: nonce
            },
            success: function (response) {
                if (response.success) {
                    $placeholder.append(response.data.html);
                    $spinner.addClass('gld-hidden');
                    $msg.text('');
                    $title.text(response.data.title);
                } else {
                    $msg.text('Error loading details.');
                    $spinner.addClass('gld-hidden');
                }
            }
        });
    }

})(jQuery);
