define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

    return {
        init: function (quizId, courseId, cmId) {
            this.quizId = quizId;
            this.courseId = courseId;
            this.cmId = cmId;
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/documents.ajax.php';

            $.ajaxSetup({
              type: 'POST'
            });

            // @TODO check if any document already exists...

            // export data on click (will call API and generate documents)
            $('.amcquiz-btn-export').on('click', function() {
                $('.export-process-spiner').show();
                /*$.ajax({
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
                      console.log('next', data);
                }.bind(this)).done(function(data){
                    console.log('done', data);
                    $('.export-process-spiner').hide();
                }).fail(function(jqXHR, textStatus) {
                    console.log('errors', jqXHR, textStatus);
                    $('.export-process-spiner').hide();
                });*/
                this.exportAmcQuiz(this.quizId, this.courseId, this.actionurl)
                .then(
                    this.sendZipFile.bind(this)
                )/*.then(
                    this.deleteTempFolder.bind(this)
                )*/.done(function(data){
                    console.log('done', data);
                    $('.export-process-spiner').hide();
                }).fail(function(jqXHR, textStatus){
                    console.log('errors', jqXHR, textStatus);
                    $('.export-process-spiner').hide();
                });
            }.bind(this));
        },
        exportAmcQuiz(amcquizid, cid, url) {
            return $.ajax({
              url: url,
              data: {
                 action: 'export',
                 cid: cid,
                 amcquizid: amcquizid
              }
            });
        },
        sendZipFile(response) {
            console.log('call send zip file', this.quizId);
            console.log('response', response);
            var parsedData =  JSON.parse(response);
            var base64ZipFile = parsedData.data.zipfile;
            console.log('base64ZipFile', base64ZipFile);
            //var data = JSON.parse(response);
            //var zipFile = new File(data.zipfile);
            //console.log('zip file', data.zipFile);

            /*return $.ajax({
              method: 'POST',
              url: apiurl,
              data: {
                 action: 'export',
                 cid: cid,
                 amcquizid: amcquizid
              }
            });*/

            return $.Deferred().resolve(response);
        },
        deleteTempFolder(response) {
            console.log('delete folder', response);
            return $.Deferred().resolve(response);
        }
    }

});
