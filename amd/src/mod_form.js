define(['jquery'], function ($) {
    return {
        init: function () {
            $("#id_anonymous").on("click", function () {
                if ($("#id_anonymous").is(':checked')) {
                    $("#id_parameters_studentnumberinstructions").attr('disabled', 'disabled');
                    $("#id_parameters_studentnameinstructions").val($("#id_parameters_studentnameinstructions").data('anon'));
                } else {
                    $("#id_parameters_studentnumberinstructions").removeAttr('disabled');
                    $("#id_parameters_studentnameinstructions").val($("#id_parameters_studentnameinstructions").data('std'));
                }
            });
        }
    };
});
