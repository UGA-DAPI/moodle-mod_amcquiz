define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function () {

            $('#btn-file-upload').attr('disabled', true);

            $('#inputSheetsFile').on('change', function(evt){
                  if (evt.target.files.length > 0) {
                    $('#btn-file-upload').attr('disabled', false);
                  }
            });
        }
    }

});
