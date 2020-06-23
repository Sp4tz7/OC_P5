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

    return {
        init: function () {

            handleComments();

        }

    };

}();

jQuery(document).ready(function () {
    Admin.init();
});