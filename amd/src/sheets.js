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


            // @TODO check if some sheets have already been uploaded for the quiz
            // @TODO in any case show an upload field and send button that allow the user to upload a PDF containing scanned sheets
            // @TODO if some sheets have already been uploaded show a button  that allow the user to delete uploaded sheets (will also delete notes)
            // @TODO if some sheets have already been uploaded display summary informations (number of sheets scanned @date ...)
            // @TODO after upload show a message with upload results {x} pages newly scanned, {y} extracted, {z} pages with marks...

            // delete sheets
            $('.btn-delete-sheets').on('click', function(){
                $.ajax({
                  url: this.actionurl,
                  data: {
                     action: 'delete-sheets',
                     cid: cid,
                     amcquizid: amcquizid
                  }
                }).done(function(data){
                    console.log('done', data);
                }).fail(function(jqXHR, textStatus){
                    console.log('errors', jqXHR, textStatus);
                });
            });

        }
    }

});
