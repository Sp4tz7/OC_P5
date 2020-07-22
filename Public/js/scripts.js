var Login = function () {

    var handleLogin = function () {

        $(".login-form input").keypress(function (e) {
            if (e.which == 13) {
                if ($(".login-form").validate().form()) {
                    $(".login-form").submit(); //form validation success, call ajax form submit
                }
                return false;
            }
        });
    }

    var handleForgetPassword = function () {

        $(".forget-form input").keypress(function (e) {
            if (e.which == 13) {
                if ($(".forget-form").validate().form()) {
                    $(".forget-form").submit();
                }
                return false;
            }
        });

        jQuery("#forget-password").click(function () {
            jQuery(".login-form").addClass("d-none");
            jQuery(".forget-form").removeClass("d-none");
        });

        jQuery("#back-btn").click(function () {
            jQuery(".login-form").removeClass("d-none");
            jQuery(".forget-form").addClass("d-none");
        });

    }

    var handleRegister = function () {

        function format(state) {
            if (!state.id) {
                return state.text;
            }
            var $state = $(
                '<span><img src="../assets/global/img/flags/" + state.element.value.toLowerCase() + ".png" class="img-flag" /> " + state.text + "</span>'
            );

            return $state;
        }

        if (jQuery().select2 && $("#country_list").size() > 0) {
            $("#country_list").select2({
                placeholder: '<i class="fa fa-map-marker"></i>&nbsp;Select a Country',
                templateResult: format,
                templateSelection: format,
                width: "auto",
                escapeMarkup: function (m) {
                    return m;
                }
            });


            $("#country_list").change(function () {
                $(".register-form").validate().element($(this)); //revalidate the chosen dropdown value and show error or success message for the input
            });
        }

        $(".register-form input").keypress(function (e) {
            if (e.which == 13) {
                if ($(".register-form").validate().form()) {
                    $(".register-form").submit();
                }
                return false;
            }
        });

        jQuery("#register-btn").click(function () {
            jQuery(".login-form").addClass("d-none");
            jQuery(".register-form").removeClass("d-none");
        });

        jQuery("#register-back-btn").click(function () {
            jQuery(".login-form").removeClass("d-none");
            jQuery(".register-form").addClass("d-none");
        });
    }

    return {
        init: function () {

            handleLogin();
            handleForgetPassword();
            handleRegister();

        }

    };

}();

jQuery(document).ready(function () {
    Login.init();
});