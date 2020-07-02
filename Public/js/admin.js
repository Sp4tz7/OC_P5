var Admin = function () {

    var handleComments = function () {

        $('.quick-edit').on('click', function () {
            $(this).closest('.comment-body').addClass('d-none');
            $(this).closest('.comment-body').next('.comment-body').removeClass('d-none');
        });

        $('.cancel').on('click', function () {
            $(this).closest('.comment-body').addClass('d-none');
            $(this).closest('.comment-body').prev('.comment-body.d-none').removeClass('d-none');
        });

        $('.save').on('click', function () {
            $(this).closest('.comment-body').addClass('d-none');
            var text = $(this).data('id');
            $(this).closest('.comment-body').prev('.comment-body.d-none').find('.comment-text').html($('#' + text).val());
            $(this).closest('.comment-body').prev('.comment-body.d-none').removeClass('d-none');
        });
    }

    var initAdmin = function () {

        (function($) {
            "use strict";

            // Add active state to sidbar nav links
            var path = window.location.href; // because the 'href' property of the DOM element is the absolute path
            $("#layoutSidenav_nav .sb-sidenav a.nav-link").each(function() {
                if (this.href === path) {
                    $(this).addClass("active");
                }
            });

            // Toggle the side navigation
            $("#sidebarToggle").on("click", function(e) {
                e.preventDefault();
                $("body").toggleClass("sb-sidenav-toggled");
            });
        })(jQuery);

    }

    return {
        init: function () {
            initAdmin();
            handleComments();

        }

    };

}();

jQuery(document).ready(function () {
    Admin.init();
});