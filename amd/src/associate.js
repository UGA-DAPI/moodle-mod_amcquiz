define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function (quizId, courseId, cmId, apiUrl, apiKey) {
            this.quizId = quizId;
            this.courseId = courseId;
            this.cmId = cmId;
            this.apiUrl = apiUrl;
            this.apiKey = apiKey;
            // @TODO point to the real API url
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/sheets.ajax.php';

            $.ajaxSetup({
              type: 'POST'
            });


            $(".submit-on-change").on('change', function(){
                $(this).closest('form').submit();
            });

        }
    }

});
