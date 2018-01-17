define(['jquery', 'jqueryui', 'core/config'], function ($, jqui, mdlconfig) {

    return {
        init: function (quizid, courseid, cmid) {
            this.quizid = quizid;
            this.courseid = courseid;
            this.cmid = cmid;
            this.addto = 'question';
            this.groupid = null;
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/questions.ajax.php';


            // enable / disable elements according to data
            this.enableDisableElements();

            //$("#questions-selected").sortable();
            $('body').on('change', '.amcquestion-checkbox', function(e){
                console.log('row checked', e.target);
            });

            $('body').on('change', '#amc-qbank-categories-select', function(e){
                // value is a string with 2 values "catid, contextid"
                // I do not now why the context id is needed... only pass the catid ?
                this.loadQuestions(e.target.value);
            }.bind(this));

            $('#qBankModal').on('shown.bs.modal', function (e) {
              this.groupid = e.relatedTarget.dataset.id;
              this.addto = e.relatedTarget.dataset.context;
              this.loadCategories();
            }.bind(this));

            $('#qBankModal').on('hidden.bs.modal', function (e) {
                //console.log('closed', e);
                // always remove modal content
                $('#amc-qbank-questions').empty();
                $('#amc-qbank-categories-select').empty();
            });

        },
        loadCategories(){
            $.ajax({
                method: 'POST',
                url: this.actionurl,
                data: {
                   action: 'load-categories',
                   cid: this.courseid,
                   cmid: this.cmid,
                   target: this.addto
                }
            }).done(function(response) {
                var requestdata = JSON.parse(response);
                var status = requestdata.status;
                var categories = requestdata.categories;
                this.appendHtml(status, categories);
            }.bind(this)).fail(function(jqXHR, textStatus) {
                console.log(jqXHR, textStatus);
            });
        },
        loadQuestions(params){
            var paramsarray = params.split(',');
            $.ajax({
                method: 'POST',
                url: this.actionurl,
                data: {
                    action: 'load-questions',
                    cid: this.courseid,
                    catid:  paramsarray[0],
                    contextid: paramsarray[1],
                    target: this.addto
                }
            }).done(function(response) {
                var requestdata = JSON.parse(response);
                var status = requestdata.status;
                var questions = requestdata.questions;
                this.appendHtml(status, [], questions, paramsarray[0]);
            }.bind(this)).fail(function(jqXHR, textStatus) {
                console.log(jqXHR, textStatus);
            });
        },
        appendHtml(status, categories, questions, selected) {
            if(status === 200){
                if (selected) {
                    var questionsHtml = this.buildModalQuestionList(questions);
                    $('#amc-qbank-questions').append(questionsHtml);
                } else {
                    var categoriesHtml = this.buildCategoriesOptions(categories);
                    $('#amc-qbank-categories-select').append(categoriesHtml);
                }
            }
        },
        buildCategoriesOptions(categories) {
            var html = '';
            for(var key in categories) {
                html += '<optgroup label="'+ key +'">';
                for (var option in categories[key]) {
                    html += '<option value="' + option + '">';
                    html +=  categories[key][option];
                    html += '</option>';
                }
                html += '</optgroup>';
            }
            return html;
        },
        buildModalQuestionList(questions) {
            // @TODO get all questions row already in DOM so that we wont add them to the list of "selectable" questions
            var html = '';
            for(var i in questions) {

                html += '<tr class="amcquestion-row" id="' + questions[i].id + '">';
                html += ' <td><input class="amcquestion-checkbox" type="checkbox"></input></td>';
                html += ' <td>'+questions[i].icon+'</td>';
                html += ' <td>' + questions[i].name + '</td>';
                html += ' <td><a target="_blank" href="' + mdlconfig.wwwroot + '/question/preview.php?id=' + questions[i].id + '" title="Preview"><i class="icon fa fa-search-plus fa-fw"></i></a></td>';
                html += '</tr>';

            }
            return html;
        },
        enableDisableElements() {
            $('.group-delete').attr('disabled', $('.group-row').length < 2);
            $('.group-row').each(function(){
              console.log('héhé');
                // enable disable group buttons according to data

                var hasDescription = $(this).find('group-description-content').length != 0;
                $(this).find('.group-description-add').attr('disabled', hasDescription);
                $(this).find('.group-description-delete').attr('disabled', !hasDescription);
                $(this).find('.group-description-edit').attr('disabled', !hasDescription)

            })
        }
    }

});
