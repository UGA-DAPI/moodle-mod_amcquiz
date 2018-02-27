define(['jquery', 'jqueryui', 'core/config', 'core/str'], function ($, jqui, mdlconfig, str) {

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

            console.log('mdlconfig', mdlconfig);

            // handle group sort
            $('.sortable-group').sortable({
              axis: 'y',
              handle: '.handle',
              connectWith: '.sortable-group',
              stop: function(event, ui) {
                  // what was dropped... a group row or a question row
                  console.log('a group was dropped');
                  var position = 1;
                  var data = [];
                  $('.group-row').each(function(){
                      var gid = $(this).data('id');
                      // update ui
                      $(this).find('.group-position').text(position);
                      var groupData = {
                        id: gid,
                        position: position
                      };
                      data.push(groupData);
                      position++;
                  });

                  $.ajax({
                      method: 'POST',
                      url: self.actionurl,
                      data: {
                         action: 'reorder-groups',
                         cid: self.courseId,
                         data: data
                      }
                  }).done(function(response) {
                      var requestData = JSON.parse(response);
                      var status = requestData.status;
                      var message = requestData.message;
                      console.log('done', message);
                  }.bind(this)).fail(function(jqXHR, textStatus) {
                      console.log(jqXHR, textStatus);
                  });
              }
            });

            // handle group question sort
            $('.group-question').sortable({
                axis: 'y',
                handle: '.handle',
                connectWith: '.group-question',
                stop:function(event, ui){
                  var data = [];
                  // get group row from where item come from
                  var $sourceGroupRow = $(event.target).closest('.group-row');
                  // get target group row (where the item has been dropped)
                  // no simplest way to get the "new" dropped item at the right place ? ie having the good container ?
                  var $targetGroupRow = $(this).data().uiSortable.currentItem.closest('.group-row');

                  if ($targetGroupRow.data('id') !== $sourceGroupRow.data('id')) {
                      // check if source group row have 0 question left, if yes happend "empty question row to it"
                      if ($sourceGroupRow.find('.question-row').length === 0) {
                          var noQuestionMessage = str.get_string('question_no_question_yet', 'mod_amcquiz');
                          $.when(noQuestionMessage).done(function(localizedNoQuestionMessage) {
                            var html = '<li class="empty-question-row list-group-item justify-content-between">';
                            html += '<h5>' + localizedNoQuestionMessage + '</h5>';
                            html += '</li>';
                            $sourceGroupRow.find('.group-question').append(html);
                          });
                      }
                      // remove empty question row in target group-row if any
                      $targetGroupRow.find('.empty-question-row').remove();
                  }

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
                      data.push(groupData);
                  });

                  $.ajax({
                      method: 'POST',
                      url: self.actionurl,
                      data: {
                         action: 'reorder-group-questions',
                         cid: self.courseId,
                         data: data
                      }
                  }).done(function(response) {
                      var requestData = JSON.parse(response);
                      var status = requestData.status;
                      var message = requestData.message;
                      console.log('done', message);
                  }.bind(this)).fail(function(jqXHR, textStatus) {
                      console.log(jqXHR, textStatus);
                  });
                }
            });

            // group name inputs (save changes on blur)
            $('.group-name').on('blur', function(e) {
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

            // question score inputs (save changes on blur)
            $('.question-score').on('blur', function(e) {
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

            // handle collapse show event in order to dynamically update btn icon
            $('.collapse').on('show.bs.collapse', function(e) {
                var $caller = null;
                if ($(e.target).hasClass('group-description-container')) {
                    $caller = $(e.target).closest( '.group-row').find('.btn-collapse').first();
                } else if ($(this).hasClass('question-details')) {
                    $caller = $(this).closest( '.question-row').find('.btn-collapse').first();
                }
                $caller.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
            });

            // handle collapse hide event in order to dynamically update btn icon
            $('.collapse').on('hide.bs.collapse', function(e){
                var $caller = null;
                if ($(this).hasClass('group-description-container')) {
                    console.log('hide description')
                    $caller = $(e.target).closest( '.group-row').find('.btn-collapse').first();
                }

                if ($(this).hasClass('question-details')) {
                    console.log('hide question')
                    $caller = $(this).closest( '.question-row').find('.btn-collapse').first();
                }
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

            // handle change event on modal categories select
            $('body').on('change', '#amc-qbank-categories-select', function(e) {
                $('#question-modal-form').find('input').remove();
                // value is a string with 2 values "catid, contextid"
                // I do not now why the context id is needed... only pass the catid ?
                this.loadQuestions(e.target.value);
            }.bind(this));

            // handle qBankModal shown event
            $('#qBankModal').on('shown.bs.modal', function (e) {
              this.groupid = $(e.relatedTarget).closest('.group-row').data('id');
              this.addto = e.relatedTarget.dataset.context;
              this.loadCategories();
            }.bind(this));

            // handle qBankModal hide event in order to reset some fields..
            // used when modal cancel button is pressed
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
                    $('#amc-qbank-questions').empty();
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
                var descriptionContent =  $(this).find('.group-description');
                // enable disable group buttons according to data
                var isEmpty = descriptionContent.html().trim().length === 0;
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
