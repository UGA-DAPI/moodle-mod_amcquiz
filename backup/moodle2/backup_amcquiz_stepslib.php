<?php

/*
 * Define all the backup steps that will be used by the backup_amcquiz_activity_task
 */

 class backup_amcquiz_activity_structure_step extends backup_activity_structure_step
 {
     protected function define_structure()
     {
         $amcquiz = new backup_nested_element(
           'amcquiz',
           ['id'],
           [
              'course',
              'author_id',
              'name',
              'uselatexfile',
              'latexfile',
              'locked',
              'anonymous',
              'timecreated',
              'timemodified',
              'studentcorrectionaccess',
              'studentannotatedaccess',
              'apikey',
          ]
         );

         $groups = new backup_nested_element('groups');
         $group = new backup_nested_element(
           'group',
            ['id'],
            [
               'amcquiz_id',
               'description_question_id',
               'name',
               'position',
            ]
         );

         $questions = new backup_nested_element('questions');
         $question = new backup_nested_element(
            'question',
            ['id'],
            [
              'group_id',
              'question_id',
              'score',
              'position',
            ]
         );

         $parameter = new backup_nested_element(
            'parameter',
            ['id'],
            [
              'amcquiz_id',
              'displaypoints',
              'versions',
              'separatesheet',
              'shuffleq',
              'shufflea',
              'randomseed',
              'qcolumns',
              'acolumns',
              'studentnumberinstructions',
              'studentnameinstructions',
              'scoringset',
              'globalinstructions',
              'globalinstructionsformat',
              'markmulti',
              'showscoringset',
              'minscore',
              'grademax',
              'gradegranularity',
              'graderounding',
              'customlayout',
            ]
         );

         $amcquiz->add_child($groups);
         $groups->add_child($group);
         $group->add_child($questions);
         $questions->add_child($question);
         $amcquiz->add_child($parameter);

         $amcquiz->set_source_table('amcquiz', ['id' => backup::VAR_ACTIVITYID]);
         $group->set_source_table('amcquiz_group', array('amcquiz_id' => backup::VAR_PARENTID));
         $question->set_source_table('amcquiz_group_question', array('group_id' => backup::VAR_PARENTID));
         $parameter->set_source_table('amcquiz_parameter', array('amcquiz_id' => backup::VAR_PARENTID));

         // Return the root element (amcquiz), wrapped into standard activity structure
         return $this->prepare_activity_structure($amcquiz);
     }
 }
