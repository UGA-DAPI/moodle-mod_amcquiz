define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function (quizId, courseId, cmId) {
            this.quizId = quizId;
            this.courseId = courseId;
            this.cmId = cmId;
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/documents.ajax.php';

            // export data on click (will call API and genrate documents)
            $('.amcquiz-btn-export').on('click', function() {
                $('.export-process-spiner').show();
                $.ajax({
                    method: 'POST',
                    url: this.actionurl,
                    data: {
                       action: 'export',
                       cid: this.courseId,
                       amcquizid: this.quizId
                    }
                }).then(function(data) {
                    var response = JSON.parse(data);
                    var status = response.status;
                    var message = response.message;
                    if(response.data.warnings.length > 0) {
                      console.log('warning - something happend while processing', response.data.warnings);
                    }
                    if(response.data.errors.length > 0) {
                      console.log('errors - something critical happend while processing', response.data.errors);
                      return $.Deferred().reject(response.data.errors);
                    } else {
                      return response.data;
                    }
                }.bind(this)).then(function(data){
                      //var data = JSON.parse(data);
                      console.log('next', data);
                }.bind(this)).fail(function(jqXHR, textStatus) {
                    console.log('errors', jqXHR, textStatus);
                    $('.export-process-spiner').hide();
                });
            }.bind(this));
        }
    }

});
