define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function (quizId, courseId, cmId) {
            this.quizId = quizId;
            this.courseId = courseId;
            this.cmId = cmId;
            // @TODO point to the real API url
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/sheets.ajax.php';

            $.ajaxSetup({
              type: 'POST'
            });


            // @TODO everything

        }
    }

});
