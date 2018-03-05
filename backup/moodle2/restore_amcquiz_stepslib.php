<?php

/*
 * Structure step to restore one amcquiz activity
 */

 class restore_amcquiz_activity_structure_step extends restore_activity_structure_step
 {
     protected function define_structure()
     {
         $paths = array();

         $paths[] = new restore_path_element('amcquiz', '/activity/amcquiz');
         $paths[] = new restore_path_element('group', '/activity/amcquiz/groups/group');
         $paths[] = new restore_path_element('question', '/activity/amcquiz/groups/group/questions/question');
         $paths[] = new restore_path_element('parameter', '/activity/amcquiz/parameter');

         // Return the root element (amcquiz), wrapped into standard activity structure
         return $this->prepare_activity_structure($paths);
     }

     protected function process_amcquiz($data)
     {
         global $DB;

         $data = (object) $data;
         $oldid = $data->id;
         $data->course = $this->get_courseid();

         $data->timecreated = $this->apply_date_offset($data->timecreated);
         $data->timemodified = $this->apply_date_offset($data->timemodified);
         $data->documents_created_at = null;
         $data->sheets_uploaded_at = null;
         $data->graded_at = null;
         $data->annotated_at = null;

         // keep old api key
         $oldapikey = $data->apikey;
         // generate new key
         $data->apikey = implode('-', str_split(substr(strtolower(md5(microtime().rand(1000, 9999))), 0, 30), 6));
         // insert the choice record
         $newitemid = $DB->insert_record('amcquiz', $data);
         // immediately after inserting "activity" record, call this
         $this->apply_activity_instance($newitemid);
         // @TODO send data to API in order to clone data with the new key...
     }

     protected function process_group($data)
     {
         global $DB;

         $data = (object) $data;
         $oldid = $data->id;
         $data->amcquiz_id = $this->get_new_parentid('amcquiz');

         // insert the choice record
         $newitemid = $DB->insert_record('amcquiz_group', $data);
         $this->set_mapping('amcquiz_group', $oldid, $newitemid);
     }

     protected function process_question($data)
     {
         global $DB;

         $data = (object) $data;
         $data->group_id = $this->get_mappingid('amcquiz_group', $data->group_id);

         // insert the question record
         $DB->insert_record('amcquiz_group_question', $data);
     }

     protected function process_parameter($data)
     {
         global $DB;

         $data = (object) $data;
         $data->amcquiz_id = $this->get_new_parentid('amcquiz');
         // should create a new randomseed ?

         // insert the choice record
         $newitemid = $DB->insert_record('amcquiz_parameter', $data);
     }
 }
