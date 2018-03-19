define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function (quizId, courseId, cmId, apiUrl, apiKey) {
            this.quizId = quizId;
            this.courseId = courseId;
            this.cmId = cmId;
            this.apiUrl = apiUrl;
            this.apiKey = apiKey;
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/sheets.ajax.php';

            $.ajaxSetup({
              type: 'POST'
            });

            $('#btn-file-upload').attr('disabled', true);

            $('#inputSheetsFile').on('change', function(evt){
                  if (evt.target.files.length > 0) {
                    $('#btn-file-upload').attr('disabled', false);
                  }
            });
        }
    }

});
