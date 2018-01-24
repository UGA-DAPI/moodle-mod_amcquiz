define(['jquery', 'jqueryui', 'core/config'], function ($, jqui, mdlconfig) {

    return {
        init: function (quizId, courseId, cmId) {
            this.quizId = quizId;
            this.courseId = courseId;
            this.cmId = cmId;
            this.addto = 'question';
            this.groupid = null;
            this.actionurl = mdlconfig.wwwroot + '/mod/amcquiz/ajax/questions.ajax.php';
            this.selectedIds = [];
            this.descriptionSelectdId = null;

            // enable / disable elements according to data
            this.enableDisableElements();
            var self = this;
            //$('.group-row').sortable();
            $('.group-question').sortable({
                axis: 'y',
                //containment: '.sortable-containment',
                connectWith: '.group-question',
                start:function(event, ui){
                  //console.log('dragstart', event, ui);
                },
                stop:function(event, ui){
                  //console.log('dragstop', event, ui);
                  var questionOrderData = [];

                  //var $originGroupRow = $(event.target).closest('.group-row');
                  // no simplest way to get the "new" dropped item at the right place ? ie having the good container ?
                  //var $targetGroupRow = $(this).data().uiSortable.currentItem.closest('.group-row');
                  //console.log('originGroupRow', $targetGroupRow.data('id'));
                  //console.log('targetgrouprow', $(this).data().uiSortable.currentItem.closest('.group-row'));
                  $('.group-row').each(function(){
                      var gid = $(this).data('id');
                      var groupData = {
                        id: gid,
                        questions: []
                      };
                      var position = 1;
                      $(this).find('.question-row').each(function(){
                          var qid = $(this).data('id');
                          // update ui
                          $(this).find('.question-position').text(position);
                          groupData.questions.push({
                              id:qid,
                              position: position
                          });
                          position++;
                      });
                      questionOrderData.push(groupData);
                  });

                  console.log('questionOrderData', questionOrderData);

                  $.ajax({
                      method: 'POST',
                      url: self.actionurl,
                      data: {
                         action: 'reorder-group-questions',
                         cid: self.courseId,
                         data: questionOrderData
                      }
                  }).done(function(response) {
                      var requestData = JSON.parse(response);
                      var status = requestData.status;
                      var message = requestData.message;
                      console.log('done', message);
                  }.bind(this)).fail(function(jqXHR, textStatus) {
                      console.log(jqXHR, textStatus);
                  });

                  // update ui question order and save usefull info for db update
                  /*$groupRow.find('.question-row').each(function(){
                      $(this).find('.question-position').text(position);
                      var questionData = {
                        position: position,
                        id: $(this).data('id')
                      }
                      questionOrderData.push(questionData);
                      position++;
                  });

                  console.log('questionOrderData', questionOrderData);
                  console.log('this.actionurl',self.actionurl);
                  // update all question order via ajax
                  $.ajax({
                      method: 'POST',
                      url: self.actionurl,
                      data: {
                         action: 'reorder-questions',
                         cid: self.courseId,
                         gid: gid,
                         data: questionOrderData
                      }
                  }).done(function(response) {
                      var requestData = JSON.parse(response);
                      var status = requestData.status;
                      var message = requestData.message;
                      console.log('done', message);
                  }.bind(this)).fail(function(jqXHR, textStatus) {
                      console.log(jqXHR, textStatus);
                  });*/
                }
            });

            // ou $( ".selector" ).on( "sortstart", function( event, ui ) {} );

            // group name inputs
            $('.group-name').on('blur', function(e){
                var name =  e.target.value;
                this.groupid = $(e.target).closest('.group-row').data('id');
                $.ajax({
                    method: 'POST',
                    url: this.actionurl,
                    data: {
                       action: 'update-group-name',
                       cid: this.courseId,
                       gid: this.groupid,
                       name: name
                    }
                }).done(function(response) {
                    var requestData = JSON.parse(response);
                    var status = requestData.status;
                    var message = requestData.message;
                    console.log('done', message);
                }.bind(this)).fail(function(jqXHR, textStatus) {
                    console.log(jqXHR, textStatus);
                });
            }.bind(this));

            // question score inputs
            $('.question-score').on('blur', function(e){
                var score =  e.target.value;
                console.log('score', score);
                var qid = $(e.target).closest('.question-row').data('id');
                $.ajax({
                    method: 'POST',
                    url: this.actionurl,
                    data: {
                       action: 'update-question-score',
                       cid: this.courseId,
                       qid: qid,
                       score: score
                    }
                }).done(function(response) {
                    var requestData = JSON.parse(response);
                    var status = requestData.status;
                    var message = requestData.message;
                    console.log('done', message);
                    // update score total shown on top of page
                    $('#scoresum').text(this.computeScoreSum());
                }.bind(this)).fail(function(jqXHR, textStatus) {
                    console.log(jqXHR, textStatus);
                });
            }.bind(this));

            $('.collapse').on('show.bs.collapse', function(e){
                // e.originalEvent is not yet implemented in V4 https://github.com/twbs/bootstrap/pull/17021 and search for
                // "Have Bootstrap's custom events include the (e.g. click, keyboard) event that caused them as an originalEvent property"
                // until it's done do it manually....
                // will work only if ONE collapse button per container ie group-row && question-row
                // consider that the source is ALWAYS above the collapsible container
                // get button who called the collapse action
                var $caller = $(e.target).closest( '.' + e.target.dataset.from + '-row').find('.btn-collapse').first();
                $caller.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            });

            $('.collapse').on('hide.bs.collapse', function(e){
                var $caller = $(e.target).closest( '.' + e.target.dataset.from + '-row').find('.btn-collapse').first();
                $caller.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
            });


            // handle change event on modal question row checkbox
            $('body').on('change', '.amcquestion-checkbox', function(e){
                var $row = $(e.target).closest('.amcquestion-row');
                var elemIndex = this.selectedIds.indexOf($row.attr('id'));
                // check if id is already in array
                if(elemIndex > -1) {
                  this.selectedIds.splice(elemIndex, 1);
                } else {
                  this.selectedIds.push($row.attr('id'));
                }
                // clear form input...
                $('#question-modal-form').find('input').remove();
                var html = '<input type="hidden" name="action" value="add-questions">';
                html += '<input type="hidden" name="current" value="questions">';
                html += '<input type="hidden" name="group-id" value="' + this.groupid + '">';
                for(var i in this.selectedIds) {
                  html += '<input type="hidden" name="question-ids[]" value="' + this.selectedIds[i] + '">';
                }
                // append inputs to modal form
                $('#question-modal-form').append(html);
            }.bind(this));

            // handle change event on modal question row radio
            $('body').on('change', '.amcquestion-radio', function(e){
                var $row = $(e.target).closest('.amcquestion-row');
                this.descriptionSelectdId = $row.attr('id');
                $('#question-modal-form').find('input').remove();
                var html = '<input type="hidden" name="action" value="add-description">';
                html += '<input type="hidden" name="current" value="questions">';
                html += '<input type="hidden" name="group-id" value="' + this.groupid + '">';
                html += '<input type="hidden" name="question-description-id" value="' + this.descriptionSelectdId + '">';
                // append inputs to modal form
                $('#question-modal-form').append(html);
            }.bind(this));

            $('body').on('change', '#amc-qbank-categories-select', function(e) {
                $('#question-modal-form').find('input').remove();
                // value is a string with 2 values "catid, contextid"
                // I do not now why the context id is needed... only pass the catid ?
                this.loadQuestions(e.target.value);
            }.bind(this));

            $('#qBankModal').on('shown.bs.modal', function (e) {
              this.groupid = $(e.relatedTarget).closest('.group-row').data('id');
              this.addto = e.relatedTarget.dataset.context;
              this.loadCategories();
            }.bind(this));

            // reset some field data.. since we are posting from modal I think it's no more usefull
            $('#qBankModal').on('hidden.bs.modal', function (e) {
                // always remove modal content
                $('#amc-qbank-questions').empty();
                $('#amc-qbank-categories-select').empty();
                this.selectedIds = [];
                this.descriptionSelectdId = null;
                $('#question-modal-form').find('input').remove();
            }.bind(this));

        },
        loadCategories(){
            var usedIds = [];
            // questions to exclude
            $('.question-row').each(function(){
                var id = $(this).data('id');
                usedIds.push(id);
            });
            $.ajax({
                method: 'POST',
                url: this.actionurl,
                data: {
                   action: 'load-categories',
                   cid: this.courseId,
                   cmid: this.cmId,
                   target: this.addto,
                   usedids: usedIds
                }
            }).done(function(response) {
                var requestData = JSON.parse(response);
                var status = requestData.status;
                var categories = requestData.categories;
                this.appendHtml(status, categories);
            }.bind(this)).fail(function(jqXHR, textStatus) {
                console.log(jqXHR, textStatus);
            });
        },
        loadQuestions(params){
            var paramsArray = params.split(',');
            var usedIds = [];
            // questions to exclude
            $('.question-row').each(function(){
                var id = $(this).data('id');
                usedIds.push(id);
            });
            $.ajax({
                method: 'POST',
                url: this.actionurl,
                data: {
                    action: 'load-questions',
                    cid: this.courseId,
                    catid:  paramsArray[0],
                    contextid: paramsArray[1],
                    target: this.addto,
                    usedids: usedIds
                }
            }).done(function(response) {
                var requestData = JSON.parse(response);
                var status = requestData.status;
                var questions = requestData.questions;
                this.appendHtml(status, [], questions, paramsArray[0], this.addto);
            }.bind(this)).fail(function(jqXHR, textStatus) {
                console.log(jqXHR, textStatus);
            });
        },
        appendHtml(status, categories, questions, selected, target) {
            if(status === 200){
                if (selected) {
                    var questionsHtml = this.buildModalQuestionList(questions, target);
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
        buildModalQuestionList(questions, target) {
            // @TODO get all questions row already in DOM so that we wont add them to the list of "selectable" questions
            var html = '';
            for(var i in questions) {
                html += '<tr class="amcquestion-row" id="' + questions[i].id + '">';
                if (target === 'question') {
                  html += ' <td><input class="amcquestion-checkbox" type="checkbox"></input></td>';
                } else {
                  html += ' <td><input class="amcquestion-radio" name="description" type="radio"></input></td>';
                }
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
                var descriptionContent =  $(this).find('.group-description-content');
                // enable disable group buttons according to data
                var isEmpty = $(this).find('.group-description-content').html().trim().length === 0;
                $(this).find('.group-description-add').attr('disabled', !isEmpty);
                $(this).find('.group-description-delete').attr('disabled', isEmpty);
                $(this).find('.group-description-edit').attr('disabled', isEmpty);
            });
        },
        computeScoreSum() {
            var sum = 0;
            $('.question-score').each(function(){
                sum += parseFloat($(this).val());
            });
            return sum.toFixed(2);
        }
    }

});
