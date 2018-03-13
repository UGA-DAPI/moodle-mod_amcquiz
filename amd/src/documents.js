define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function (quizId, courseId, cmId, apiUrl, apiKey) {
            $('.amcquiz-btn-export').on('click', function(){
                $(this).prop('disabled', true)
            });
        }
    }

});
