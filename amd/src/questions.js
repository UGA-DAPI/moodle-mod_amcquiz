define(['jquery', 'jqueryui', 'core/config'], function ($, jqui, mdlconfig) {

    return {
        init: function (courseid, cmid) {
          console.log('yope ' + courseid);
            this.courseid = courseid;
            this.cmid = cmid;
            this.addto = 'question';

            $("#questions-selected").sortable();
            $('body').on('change', '.amcquestion-checkbox', function(e){
                console.log('row checked', e.target);
            });

            $('body').on('change', '#amc-qbank-categories-select', function(e){
                // value is a string with 2 values "catid, contextid"
                // I do not now why the context id is needed... only pass the catid ?
                this.loadQuestions(e.target.value);
            }.bind(this));

            $('#qBankModal').on('shown.bs.modal', function (e) {
              console.log('tototototo', e.relatedTarget.dataset.id);
              this.addto = e.relatedTarget.dataset.context;
              this.loadCategories();
            }.bind(this));

            $('#qBankModal').on('hidden.bs.modal', function (e) {
              console.log('closed', e);
            })

        },
        loadCategories(){
            var url = mdlconfig.wwwroot + '/mod/amcquiz/ajax/qbank.php?cid=' + this.courseid + '&cmid=' + this.cmid + '&target=' + this.addto;
            $.ajax({
                method: 'GET',
                url: url
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

            var url = mdlconfig.wwwroot + '/mod/amcquiz/ajax/qbank.php?cid=' + this.courseid + '&catid=' + paramsarray[0] + '&contextid=' + paramsarray[1] + '&target=' + this.addto;
            console.log('url', url);
            $.ajax({
                method: 'GET',
                url: url
            }).done(function(response) {
                var requestdata = JSON.parse(response);
                var status = requestdata.status;
                var questions = requestdata.questions;
                this.appendHtml(status, [], questions, catid);
            }.bind(this)).fail(function(jqXHR, textStatus) {
                console.log(jqXHR, textStatus);
            });
        },
        appendHtml(status, categories, questions, selected) {
            if(status === 200){
                if (selected) {
                    $('#amc-qbank-questions').empty();
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
        }
    }

});
