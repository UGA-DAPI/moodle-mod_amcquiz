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

            this.toggleFormFields($('#id_uselatexfile').is(':checked'));

            $('#id_uselatexfile').on('click', function(){
                this.toggleFormFields($('#id_uselatexfile').is(':checked'));
            }.bind(this));
        },
        toggleFormFields(uselatexfile) {
          if (uselatexfile) {
              $('#id_parameters').hide();
              $('#id_instructions').hide();
          } else {
              $('#id_parameters').show();
              $('#id_instructions').show();
          }
        }
    };
});
