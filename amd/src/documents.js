define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function (quizId, courseId, cmId) {
            this.quizId = quizId;
            this.courseId = courseId;
            this.cmId = cmId;
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/documents.ajax.php';

            // export data on click (will call API and genrate documents)
            $('.amcquiz-btn-export').on('click', function() {

                $.ajax({
                    method: 'POST',
                    url: this.actionurl,
                    data: {
                       action: 'export',
                       cid: this.courseId,
                       cmid: this.cmId,
                       amcquizid: this.quizId
                    }
                }).done(function(response) {
                    var data = JSON.parse(response);
                    var status = requestData.status;
                    var message = requestData.message;
                    console.log('done', message);
                }.bind(this)).fail(function(jqXHR, textStatus) {
                    console.log(jqXHR, textStatus);
                });
            }.bind(this));
        }
    }

});
