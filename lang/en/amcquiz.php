<?php

// module
$string['modulename'] = 'AMC Quiz';
$string['modulename_help'] = 'The AMC quiz activity enables a teacher to create quizzes ...

AMC Quizzes may be used

* As course exams
* As mini tests for reading assignments or at the end of a topic
* As exam practice using questions from past exams
* To deliver immediate feedback about performance
* For self-assessment';
$string['modulename_link'] = 'mod/amcquiz/view';
$string['modulenameplural'] = 'AMC Quizzes';
$string['pluginname'] = 'AMC Quiz';

// plugin global settings
$string['settings_scoring_rules'] = 'Scoring rules';
$string['settings_scoring_rules_help'] = "Groups of rules are separed by a line of at least 3 dashes.
<p>
The first line of each block will be the title displayed in the dropdown list.
Eventually, lines of description follow. They will be displayed on the main form of settings.
After a eventual blank line, each line should contain a scoring rule like: <code>M|S ; default|[points] ; [rule]</code>.
The syntax of each rule is described in <a href=\"http://home.gna.org/auto-qcm/auto-multiple-choice.fr/interface-graphique.shtml#bareme\">AMC's documentation</a>.
When the question score is not explicit, it can be written <code>SCORE</code> in the rule.
</p>

Example:
<pre>
Default
For a single choice question with one point, one point for a good answer and no points for all other cases.
For a single choice question with multiple right answer, all points for a right answer, 0 if no answer given and -1 for all other cases.
For a multiple question with multiple right answers, 1 point is substracted by wrong answer, without exceeding -1 per question.

S ;       1 ; e=0,v=0,m=0,b=1
S ; default ; e=-1,v=0,m=-1,b=SCORE
M ; default ; e=-1,m=-1,p=-1,haut=SCORE

---
All or nothing
For all question, all the points if the answer is totally right and else 0.
S ; default ; e=0,v=0,m=0,b=SCORE
M ; default ; e=0,mz=SCORE
</pre>

<p>For each question, the first rule matching on the 2 first columns will be used.</p>
";
$string['settings_scoring_rules_default'] = 'All or nothing
For all question, all the points if the answer is totally right and else 0.
S ; default ; e=0,v=0,m=0,b=SCORE
M ; default ; e=0,mz=SCORE';

$string['settings_amcquiz_apiurl_short'] = 'API url';
$string['settings_amcquiz_apiurl_full'] = 'Path to API used for AMC scripts. Please note the last [/] char at the end of the url.';
$string['settings_code_length_short'] = 'Code length';
$string['settings_code_length_full'] = 'Student code length for AMC display';
$string['settings_instructionslstudent_short'] = 'Instructions / student number';
$string['settings_instructionslstudent_full'] = 'Default value of the homonymous field, when creating paper questionnaires.';
$string['settings_instructionslstudent_default'] = 'Please code the student number here, and write your name below.';
$string['settings_instructionslnamestd_short'] = 'Identification area / Standard';
$string['settings_instructionslnamestd_full'] = 'Default instruction for the field when creating a new standard paper questionnaire.';
$string['settings_instructionslnamestd_default'] = 'Name et first name';
$string['settings_instructionslnameanon_short'] = 'Identification area / Anonymous';
$string['settings_instructionslnameanon_full'] = 'Default instruction for the field when creating an anonymous paper questionnaire.';
$string['settings_instructions_short'] = 'Default instructions';
$string['settings_instructions_default'] = 'Please use a pencil and gray each selected case completely.';
$string['settings_idnumberprefixes_short'] = 'Prefix for student number';
$string['settings_idnumberprefixes_full'] = '<p>Prefixes, one per row. Beware of spaces.</p><p>Each prefix will be inserted at the beginning of the student number of each sheet, until the identification of the student among the moodle users (cf LDAP import and idnumber). If the student can not be found, a no prefix identification will be attempted.</p>';

$string['settings_amcquiz_apiglobalkey_short'] = 'AMC API global key';
$string['settings_amcquiz_apiglobalkey_full'] = 'This the global key for AMC API.';

// Instance settings
$string['modform_amcquizname'] = 'Questionnaire';
$string['modform_uselatexfile'] = 'Use a Latex file';
$string['modform_uselatexfilelabel'] = 'The Latex file define AMC and some questionnaire parameters.';
$string['modform_latexfile'] = 'Latex file (*.tex)';
$string['modform_instructionsheader'] = 'Instructions';
$string['modform_general_instructions'] = 'General instructions';
$string['modform_description'] = 'Description';
$string['modform_description_help'] = 'A short description for the questionnaire.';
$string['modform_anonymous'] = 'Anonymous questionnaire';
$string['modform_studentnumber_instructions'] = 'Instructions for the student number';
$string['modform_studentname_instructions'] = 'Instructions for the student name';
$string['modform_scoring_parameters_header'] = 'Scoring';
$string['modform_grademax'] = 'Maximum grade';
$string['modform_gradegranularity'] = 'Grade granularity';
$string['modform_graderounding_strategy'] = 'Grade rounding strategy';
$string['modform_scoring_strategy'] = 'Strategy used for score compution';
$string['grade_rounding_strategy_nearest'] = 'Nearest';
$string['grade_rounding_strategy_lower'] = 'Lower';
$string['grade_rounding_strategy_upper'] = 'Upper';
$string['modform_amc_parameters_header'] = 'AMC parameters';
$string['modform_sheets_versions'] = 'Number of versions';
$string['modform_questions_columns'] = 'Number of columns for questions';
$string['modform_shuffle_questions'] = 'Shuffle questions';
$string['modform_shuffle_answers'] = 'Shuffle answers';
$string['modform_separate_sheets'] = 'Separate answer sheet';
$string['modform_sheets_columns'] = 'Number of columns for each sheet';
$string['modform_display_scores'] = 'Display scores';
$string['modform_display_scores_no'] = 'Do not display';
$string['modform_display_scores_beginning'] = 'Display at the beginning of question';
$string['modform_display_scores_end'] = 'Display at the end of question';
$string['modform_mark_multi'] = 'Put a mark if multiple good answer';
$string['modform_mark_multi_help'] = 'If checked, a clover leaf will appear on any question having multiple right answer.';
$string['modform_display_score_rules'] = 'Display score rules';
$string['modform_display_score_rules_help'] = 'The score rule will be printed.';
$string['modform_custom_layout'] = 'Custom layout';
$string['modform_custom_layout_help'] = 'Set a custom layout for AMC';

// Tabs
$string['tab_documents'] = 'Documents';
$string['tab_sheets'] = 'Sheets';
$string['tab_associate'] = 'Identification';
$string['tab_grade'] = 'Notation';
$string['tab_correction'] = 'Correction';

// Questions
$string['qbank_questions_categories'] = 'Available categories';
$string['question_no_question_yet'] = 'No question for the group.';
$string['question_create_new'] = 'Create';
$string['question_create_new_help'] = 'Create one or several questions (open question bank page)';
$string['question_nb_questions'] = 'Number of questions';
$string['question_create_group_help'] = 'Add a group';
$string['question_create_group'] = 'Group';
$string['question_delete_group'] = 'Delete group';
$string['question_add_description_help'] = 'Add a description for the group';
$string['question_add_from_bank'] = 'Bank';
$string['question_add_from_bank_help'] = 'Add questions from question bank';
$string['question_toggle_question_details'] = 'Show / hide question details';
$string['question_toggle_group_description'] = 'Show / hide question description';
$string['question_preview_question'] = 'Preview question';
$string['question_delete_question'] = 'Remove question';

// Documents
$string['document_answer_sheet_title'] = 'Answer sheet';
$string['documents_generate'] = 'Generate documents';

// Sheets
$string['sheets_add_sheets'] = 'Add sheets';
$string['sheets_delete_existing_sheets'] = 'Delete existing sheets';
$string['sheets_unrecognized_sheets'] = 'Unrecognized sheets';
$string['sheets_delete_unrecognized_sheets'] = 'Delete all unrecognized sheets';

// Associate
$string['associating_no_data_for_query'] = 'No data for your query';
$string['associationmode'] = 'Show associations';
$string['associationusermode'] = 'Show students';
$string['unknown'] = 'Unknown';
$string['manual'] = 'Manual identifications';
$string['auto'] = 'Automatic identifications';
$string['without'] = 'Without sheets';
$string['associating_sheets_identified'] = '{$a->automatic} sheet(s) automaticaly identified, {$a->manualy} sheet(s) manualy identified and {$a->unknown} unknown.';
$string['associating_launch_association'] = 'Launch association';

// Grading
$string['grading_launch_grade'] = 'Launch notation';
$string['grading_notes'] = 'Notes';
$string['grading_file_notes_table'] = 'Files notes tables';
$string['grading_sheets_identified'] = '{$a->known} sheets identified and {$a->unknown} unknown.';
$string['grading_statistics'] = 'Statistics';
$string['grading_not_satisfying_notation'] = 'If the result of the notation does not satisfy you, you can change the scale and relaunch the correction.';
$string['grading_size'] = 'Workforce';
$string['grading_mean'] = 'Mean';
$string['grading_median'] = 'Median';
$string['grading_mode'] = 'Mode';
$string['grading_range'] = 'Range';
$string['grading_no_stats'] = 'No statistics available yet.';

// Correction
$string['correction_corrected_sheets'] = 'Corrected sheets';
$string['correction_individual_sheets_available'] = 'individual annotated sheets available.';
$string['correction_generate_corrected_sheets'] = 'Generate corrected sheets';
$string['correction_sheets_access'] = 'Sheets access';
$string['correction_allow_access'] = 'Allow each student to access';
$string['correction_copy_only'] = 'The annotated corrected sheet';
$string['correction_whole_correction'] = 'The whole correction';
$string['correction_warn_students'] = 'Warn students';
$string['correction_send_moodle_message'] = 'Send a message';
$string['correction_send_moodle_message_title'] = 'Send a Moodle message to each student';
$string['selectuser'] = 'Select student';

// API / CURL

$string['api_init_amcquiz_success'] = 'Amcquiz structure creation success.';
$string['api_init_amcquiz_error'] = 'An error occured while creating amcquiz structure.';
$string['api_init_amcquiz_curl_error'] = 'A CURL error occured while creating amcquiz structure.';

$string['api_get_definition_file_success'] = 'Get amcquiz definition file success.';
$string['api_get_definition_file_error'] = 'An error occured while retrieving amcquiz definition file.';
$string['api_get_definition_file_curl_error'] = 'A CURL error occured while retrieving amcquiz definition file.';

$string['api_send_zipped_quiz_success'] = 'Zipped sent';
$string['api_send_zipped_quiz_error'] = 'An error occured while sending amcquiz zipped file.';
$string['api_send_zipped_quiz_curl_error'] = 'A CURL error occured while sending amcquiz zipped file.';

$string['api_generate_documents_success'] = 'Documents created.';
$string['api_generate_documents_error'] = 'An error occured while creating documents.';
$string['api_generate_documents_curl_error'] = 'A CURL error occured while creating documents.';

$string['api_send_latex_file_success'] = 'Latex file sent.';
$string['api_send_latex_file_error'] = 'An error occured while sending latex file.';
$string['api_send_latex_file_curl_error'] = 'A CURL error occured while sending latex file.';

$string['api_delete_unrecognized_sheets_success'] = 'Unrecognized sheets deleted.';
$string['api_delete_unrecognized_sheets_error'] = 'An error occured while deleting unrecognized sheets.';
$string['api_delete_unrecognized_sheets_curl_error'] = 'A CURL error occured while deleting unrecognized sheets.';

$string['api_get_amcquiz_documents_success'] = 'Get amcquiz documents success.';
$string['api_get_amcquiz_documents_error'] = 'An error occured while retrieving amcquiz documents.';
$string['api_get_amcquiz_documents_curl_error'] = 'A CURL error occured while retrieving amcquiz documents.';

$string['api_get_amcquiz_sheets_success'] = 'Get amcquiz sheets success.';
$string['api_get_amcquiz_sheets_error'] = 'An error occured while retrieving amcquiz sheets.';
$string['api_get_amcquiz_sheets_curl_error'] = 'A CURL error occured while retrieving amcquiz sheets.';

$string['api_get_amcquiz_associations_success'] = 'Get amcquiz associations success.';
$string['api_get_amcquiz_associations_error'] = 'An error occured while retrieving amcquiz associations.';
$string['api_get_amcquiz_associations_culr_error'] = 'A CURL error occured while retrieving amcquiz associations.';

$string['api_launch_association_success'] = 'Amcquiz association process success.';
$string['api_launch_association_error'] = 'An error occured while executing association process.';
$string['api_launch_association_curl_error'] = 'A CURL error occured while executing association process.';

$string['api_associate_sheet_manually_success'] = 'Amcquiz manual association process success.';
$string['api_associate_sheet_manually_error'] = 'An error occured while executing manual association process.';
$string['api_associate_sheet_manually_curl_error'] = 'A CURL error occured while executing manual association process.';

$string['api_launch_grade_success'] = 'Amcquiz grade process success.';
$string['api_launch_grade_error'] = 'An error occured while executing grade process.';
$string['api_launch_grade_curl_error'] = 'A CURL error occured while executing grade process.';

$string['api_annotate_success'] = 'Amcquiz annotate process success.';
$string['api_annotate_error'] = 'An error occured while executing annotate process.';
$string['api_annotate_curl_error'] = 'A CURL error occured while executing annotate process.';

$string['api_get_amcquiz_grade_stats_success'] = 'Get amcquiz grade statistics success.';
$string['api_get_amcquiz_grade_stats_error'] = 'An error occured while retrieving amcquiz grade statistics.';
$string['api_get_amcquiz_grade_stats_curl_error'] = 'A CURL error occured while retrieving amcquiz grade statistics.';

$string['api_get_amcquiz_grade_files_success'] = 'Get amcquiz grade files success.';
$string['api_get_amcquiz_grade_files_error'] = 'An error occured while retrieving amcquiz grade files.';
$string['api_get_amcquiz_grade_files_curl_error'] = 'A CURL error occured while retrieving amcquiz grade files.';

$string['api_get_amcquiz_corrections_success'] = 'Get amcquiz correction success.';
$string['api_get_amcquiz_corrections_error'] = 'An error occured while retrieving amcquiz corrections.';
$string['api_get_amcquiz_corrections_curl_error'] = 'A CURL error occured while retrieving amcquiz corrections.';

$string['api_upload_sheets_success'] = 'Amcquiz upload sheets success.';
$string['api_upload_sheets_error'] = 'An error occured while uploading amcquiz sheets.';
$string['api_upload_sheets_curl_error'] = 'A CURL error occured while uploading amcquiz sheets.';

$string['api_delete_all_sheets_success'] = 'Amcquiz delete sheets success.';
$string['api_delete_all_sheets_error'] = 'An error occured while deleting amcquiz sheets.';
$string['api_delete_all_sheets_curl_error'] = 'A CURL error occured while deleting amcquiz sheets.';

$string['api_delete_amcquiz_success'] = 'Delete amcquiz success.';
$string['api_delete_amcquiz_error'] = 'An error occured while deleting amcquiz.';
$string['api_delete_amcquiz_curl_error'] = 'A CURL error occured while deleting amcquiz.';

$string['curl_init_amcquiz_no_key'] = 'No key for API authentication.';
