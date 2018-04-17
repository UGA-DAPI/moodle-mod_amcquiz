<?php

// module
$string['modulename'] = 'Questionnaire AMC';
$string['modulename_help'] = 'L\'activité Questionnaire AMC permet aux enseignant de créer des questionnaires ...

Les questionnaires AMC peuvent être utilisés

* As course exams
* As mini tests for reading assignments or at the end of a topic
* As exam practice using questions from past exams
* To deliver immediate feedback about performance
* For self-assessment';
$string['modulename_link'] = 'mod/amcquiz/view';
$string['modulenameplural'] = 'Questionnaires AMC';
$string['pluginname'] = 'Questionnaire AMC';

// plugin global settings
$string['settings_scoring_rules'] = 'Règles de notation';
$string['settings_scoring_rules_help'] = "Chaque groupe de règle est séparé par au moins 3 tirets [---].
<p>
La première ligne de chaque groupe sera le titre affiché dans la liste déroulante.
Il peut y avoir des éléments de description à la suite de la première ligne. Cette description sera affichée dans le formulaire principal des options.
Chaque ligne devrait contenir une règle de score comme: <code>M|S ; default|[points] ; [rule]</code>.
La syntaxe d'une règle est décrite ici <a href=\"http://home.gna.org/auto-qcm/auto-multiple-choice.fr/interface-graphique.shtml#bareme\">AMC's documentation</a>.
Lorsque le score de la question n'est pas explicite il peut être écrit <code>SCORE</code> dans la règle.
</p>

Exemple:
<pre>
Défaut
Pour une question simple à un point, un point pour une bonne réponse et aucun point dans tous les autres cas.
Pour une autre question simple, tous les points pour une bonne réponse, 0 si pas de réponse et -1 point dans tous les autres cas.
Pour une question à multiples bonnes réponses, un point est retiré par réponse incorrecte, sans dépasser -1 par question.

S ;       1 ; e=0,v=0,m=0,b=1
S ; default ; e=-1,v=0,m=-1,b=SCORE
M ; default ; e=-1,m=-1,p=-1,haut=SCORE

---
Tout ou rien
Pour toute question, tous les points si la réponse est totalement juste, 0 sinon.
S ; default ; e=0,v=0,m=0,b=SCORE
M ; default ; e=0,mz=SCORE
</pre>

<p>Pour chaque question la première règle correspondant aux 2 1ères colonnes sera utilisée.</p>
";

$string['settings_scoring_rules_default'] = 'Tout ou rien
Pour toute question, tous les points si la réponse est totalement juste, 0 sinon.
S ; default ; e=0,v=0,m=0,b=SCORE
M ; default ; e=0,mz=SCORE';

// Settings
$string['settings_amcquiz_apiurl_short'] = 'Url de l\'API';
$string['settings_amcquiz_apiurl_full'] = 'URL de base vers l\'API qui sera utilisée pour les traitements AMC. Le dernier [/] dans l\'URL est obligatoire.';
$string['settings_code_length_short'] = 'Longueur code';
$string['settings_code_length_full'] = 'Longueur du code étudiant pour l\'affichage AMC.';
$string['settings_instructionslstudent_short'] = 'Consigne / n° étudiant';
$string['settings_instructionslstudent_full'] = 'Valeur par défaut du champ homonyme, à la création de questionnaires papier.';
$string['settings_instructionslstudent_default'] = 'Veuillez coder votre numéro d\'étudiant ci-contre, et écrire votre nom dans la case ci-dessous.';
$string['settings_instructionslnamestd_short'] = 'Zone d\'identification / Standard';
$string['settings_instructionslnamestd_full'] = 'Consigne par défaut du champ, à la création d\'un questionnaire papier standard.';
$string['settings_instructionslnamestd_default'] = 'Nom et prénom';
$string['settings_instructionslnameanon_short'] = 'Zone d\'identification / Anonyme';
$string['settings_instructionslnameanon_full'] = 'Consigne par défaut du champ, à la création d\'un questionnaire papier anonyme.';
$string['settings_instructions_short'] = 'Instructions par défaut';
$string['settings_instructions_default'] = 'Utilisez de préférence un crayon et noircissez complètement chaque case sélectionnée.';
$string['settings_idnumberprefixes_short'] = 'Préfixes du n° d\'étudiant';
$string['settings_idnumberprefixes_full'] = '<p>Préfixes, un par ligne. Attention aux espaces.</p><p>Chacun des préfixes sera inséré au début du numéro d\'étudiant de chaque copie, jusqu\'à ce que l\'étudiant soit identifié parmi les utilisateurs inscrits dans Moodle (cf import LDAP et idnumber). Si aucun préfixe ne permet de trouver l\'étudiant, une identification sans préfixe sera ensuite testée.</p>';
$string['settings_amcquiz_apiglobalkey_short'] = 'Clée globale pour l\'API AMC';
$string['settings_amcquiz_apiglobalkey_full'] = 'C\'est la clée globale pour utiliser l\'API AMC.';

// Instance settings
$string['modform_amcquizname'] = 'Questionnaire';
$string['modform_uselatexfile'] = 'Utiliser un fichier Latex';
$string['modform_uselatexfilelabel'] = 'Le fichier Latex défini les paramètres AMC et certains paramètres du questionnaire.';
$string['modform_latexfile'] = 'Fichier Latex (*.tex)';
$string['modform_instructionsheader'] = 'Instructions';
$string['modform_general_instructions'] = 'Instructions générales';
$string['modform_description'] = 'Description';
$string['modform_description_help'] = 'Une description courte du questionnaire.';
$string['modform_anonymous'] = 'Questionnaire annonyme';
$string['modform_studentnumber_instructions'] = 'Instructions pour le numéro d\'étudiant';
$string['modform_studentname_instructions'] = 'Instructions pour le nom d\'étudiant';
$string['modform_scoring_parameters_header'] = 'Score';
$string['modform_grademax'] = 'Note maximale';
$string['modform_gradegranularity'] = 'Granularité de la note';
$string['modform_graderounding_strategy'] = 'Stratégie pour l\'arrondi de la note';
$string['modform_scoring_strategy'] = 'Stratégie pour le calcul du score';
$string['grade_rounding_strategy_nearest'] = 'Au plus proche';
$string['grade_rounding_strategy_lower'] = 'Inférieur';
$string['grade_rounding_strategy_upper'] = 'Suppérieur';
$string['modform_amc_parameters_header'] = 'Paramètres AMC';
$string['modform_sheets_versions'] = 'Nombre de versions';
$string['modform_questions_columns'] = 'Nombre de colonnes pour les questions';
$string['modform_shuffle_questions'] = 'Ordre aléatoire des questions';
$string['modform_shuffle_answers'] = 'Ordre aléatoire des réponses';
$string['modform_separate_sheets'] = 'Copies de réponse séparées';
$string['modform_sheets_columns'] = 'Nombre de colonnes pour chaque copie';
$string['modform_display_scores'] = 'Affichage des scores';
$string['modform_display_scores_no'] = 'Ne pas afficher';
$string['modform_display_scores_beginning'] = 'Afficher au début de la question';
$string['modform_display_scores_end'] = 'Afficher à la fin de la question';
$string['modform_mark_multi'] = 'Marquer si plusieurs bonnes réponses';
$string['modform_mark_multi_help'] = 'Si cette case est cochée, un trèfle apparaitra devant toute question ayant plusieurs bonnes réponses.';
$string['modform_display_score_rules'] = 'Afficher la règle de calcul du score';
$string['modform_display_score_rules_help'] = 'La règle pour le calcul du score sera imprimée sur la copie.';
$string['modform_custom_layout'] = 'Agencement personnalisé';
$string['modform_custom_layout_help'] = 'Définir un agencement personnalisé pour AMC';

// Tabs
$string['tab_documents'] = 'Documents';
$string['tab_sheets'] = 'Copies';
$string['tab_associate'] = 'Identification';
$string['tab_grade'] = 'Notation';
$string['tab_correction'] = 'Correction';

// Questions
$string['qbank_questions_categories'] = 'Catégories disponnibles';
$string['question_no_question_yet'] = 'Aucune question pour le groupe.';
$string['question_create_new'] = 'Créer';
$string['question_create_new_help'] = 'Créer une ou plusieurs questions (ouvre la page banque de question)';
$string['question_nb_questions'] = 'Nombre de questions';
$string['question_create_group_help'] = 'Ajouter un groupe';
$string['question_create_group'] = 'Groupe';
$string['question_delete_group'] = 'Supprimer le groupe';
$string['question_add_description_help'] = 'Ajouter une description pour le groupe';
$string['question_add_from_bank'] = 'Banque';
$string['question_add_from_bank_help'] = 'Ajouter des questions depuis la banque de question';
$string['question_toggle_question_details'] = 'Afficher / cacher les détails de la question';
$string['question_toggle_group_description'] = 'Afficher / cacher la description de la question';
$string['question_preview_question'] = 'Prévisualiser la question';
$string['question_delete_question'] = 'Enlever la question';

// Documents
$string['document_answer_sheet_title'] = 'Feuille de réponse';
$string['documents_generate'] = 'Générer les documents';
$string['documents_unrecognized_sheets'] = 'Copies non reconnues';
$string['documents_delete_unrecognized_sheets'] = 'Supprimer toutes les copies non reconnues';

// Sheets
$string['sheets_add_sheets'] = 'Ajouter des copies';
$string['sheets_delete_existing_sheets'] = 'Éffacer les copies existantes';

// Associate
$string['associating_no_data_for_query'] = 'Aucune donnée pour votre requête';
$string['associationmode'] = 'Afficher les associations';
$string['associationusermode'] = 'Afficher les étudiants';
$string['unknown'] = 'Inconnues';
$string['manual'] = 'Identifications manuelles';
$string['auto'] = 'Identifications automatiques';
$string['without'] = 'Sans copies';
$string['associating_sheets_identified'] = '{$a->automatic} copie(s) automatiquement identifiée(s), {$a->manualy} copie(s) manuellement identifiée(s) et {$a->unknown} non identifiée(s).';
$string['associating_launch_association'] = 'Lancer l\'association';

// Grading.
$string['grading_launch_grade'] = 'Lancer la notation';
$string['grading_notes'] = 'Notes';
$string['grading_file_notes_table'] = 'Fichiers tableaux des notes';
$string['grading_sheets_identified'] = '{$a->known} copies identifiées et {$a->unknown} non identifiées.';
$string['grading_statistics'] = 'Statistiques';
$string['grading_not_satisfying_notation'] = 'Si le résultat de la notation ne vous convient pas, vous pouvez modifier le barème puis relancer la correction.';
$string['grading_size'] = 'Effectifs';
$string['grading_mean'] = 'Moyenne';
$string['grading_median'] = 'Médiane';
$string['grading_mode'] = 'Mode';
$string['grading_range'] = 'Intervalle';
$string['grading_no_stats'] = 'Aucune statistique disponnible actuellement.';

// Correction
$string['correction_corrected_sheets'] = 'Copies corrigées';
$string['correction_individual_sheets_available'] = 'copies individuelles annotées disponibles.';
$string['correction_generate_corrected_sheets'] = 'Générer les copies corrigées';
$string['correction_sheets_access'] = 'Accès aux copies';
$string['correction_allow_access'] = 'Permettre l\'accès de chaque étudiant';
$string['correction_copy_only'] = 'À sa copie corrigée annotée';
$string['correction_whole_correction'] = 'Au corrigé complet';
$string['correction_warn_students'] = 'Avertir les étudiants';
$string['correction_send_moodle_message'] = 'Envoyer un message';
$string['correction_send_moodle_message_title'] = 'Envoyer un message Moodle à chaque étudiant';
$string['selectuser'] = 'Sélection de l\'étudiant';

// API / CURL

$string['api_init_amcquiz_success'] = 'Structure de l\'amcquiz créée avec succès.';
$string['api_init_amcquiz_error'] = 'Une erreur est survenue lors de la création de la structure du quiz.';
$string['api_init_amcquiz_curl_error'] = 'Une erreur CURL est apparue lors de la création de la structure du quiz.';

$string['api_get_definition_file_success'] = 'Succès de la récupération du fichier de définition du quiz.';
$string['api_get_definition_file_error'] = 'Une erreur est survenue lors de la récupération du fichier de définition du quiz.';
$string['api_get_definition_file_curl_error'] = 'Une erreur CURL est survenue lors de la récupération du fichier de définition du quiz.';

$string['api_send_zipped_quiz_success'] = 'ZIP du quiz envoyé.';
$string['api_send_zipped_quiz_error'] = 'Une erreur est survenue lors de l\'envoi du quiz ZIPé.';
$string['api_send_zipped_quiz_curl_error'] = 'Une erreur CURL est survenue lors de l\'envoi du quiz ZIPé.';

$string['api_generate_documents_success'] = 'Documents créés.';
$string['api_generate_documents_error'] = 'Une erreur est survenue lors de la création des documents.';
$string['api_generate_documents_curl_error'] = 'Une erreur CURL est survenue lors de la création des documents.';

$string['api_send_latex_file_success'] = 'Fichier latex envoyé.';
$string['api_send_latex_file_error'] = 'Une erreur est survenue lors de l\'envoi du fichier latex.';
$string['api_send_latex_file_curl_error'] = 'Une erreur CURL est survenue lors de l\'envoi du fichier latex.';

$string['api_delete_unrecognized_sheets_success'] = 'Copies non reconnues supprimées.';
$string['api_delete_unrecognized_sheets_error'] = 'Une erreur est survenue lors de la suppression des copies non reconnues.';
$string['api_delete_unrecognized_sheets_curl_error'] = 'Une erreur CURL est survenue lors de la suppression des copies non reconnues.';

$string['api_get_amcquiz_documents_success'] = 'Succès de la récupération des documents.';
$string['api_get_amcquiz_documents_error'] = 'Une erreur est survenue lors de la récupération des documents.';
$string['api_get_amcquiz_documents_curl_error'] = 'Une erreur CURL est survenue lors de la récupération des documents.';

$string['api_get_amcquiz_sheets_success'] = 'Succès de la récupération des copies.';
$string['api_get_amcquiz_sheets_error'] = 'Une erreur est survenue lors de la récupération des copies.';
$string['api_get_amcquiz_sheets_curl_error'] = 'Une erreur CURL est survenue lors de la récupération des copies.';

$string['api_get_amcquiz_associations_success'] = 'Succès de la récupération des associations.';
$string['api_get_amcquiz_associations_error'] = 'Une erreur est survenue lors de la récupération des associations.';
$string['api_get_amcquiz_associations_culr_error'] = 'Une erreur CURL est survenue lors de la récupération des associations.';

$string['api_launch_association_success'] = 'Succès du processus d\'association.';
$string['api_launch_association_error'] = 'Une erreur est survenue lors de l\'execution du processus d\'association.';
$string['api_launch_association_curl_error'] = 'Une erreur CURL est survenue lors de l\'execution du processus d\'association.';

$string['api_associate_sheet_manually_success'] = 'Succès du processus d\'association manuel.';
$string['api_associate_sheet_manually_error'] = 'Une erreur est survenue lors de l\'execution du processus d\'association manuel.';
$string['api_associate_sheet_manually_curl_error'] = 'Une erreur CURL est survenue lors de l\'execution du processus d\'association manuel.';

$string['api_launch_grade_success'] = 'Succès du processus de notation.';
$string['api_launch_grade_error'] = 'Une erreur est survenue lors de l\'execution du processus de notation.';
$string['api_launch_grade_curl_error'] = 'Une erreur CURL est survenue lors de l\'execution du processus de notation.';

$string['api_annotate_success'] = 'Succès du processus de correction.';
$string['api_annotate_error'] = 'Une erreur est survenue lors de l\'execution du processus de correction.';
$string['api_annotate_curl_error'] = 'Une erreur CURL est survenue lors de l\'execution du processus de correction.';

$string['api_get_amcquiz_grade_stats_success'] = 'Succès de la récupération des statistiques de notation.';
$string['api_get_amcquiz_grade_stats_error'] = 'Une erreur est survenue lors de la récupération des statistiques de notation.';
$string['api_get_amcquiz_grade_stats_curl_error'] = 'Une erreur CURL est survenue lors de la récupération des statistiques de notation.';

$string['api_get_amcquiz_grade_files_success'] = 'Succès de la récupération des fichiers de notation.';
$string['api_get_amcquiz_grade_files_error'] = 'Une erreur est survenue lors de la récupération des fichiers de notation.';
$string['api_get_amcquiz_grade_files_curl_error'] = 'Une erreur CURL est survenue lors de la récupération des fichiers de notation.';

$string['api_get_amcquiz_corrections_success'] = 'Succès de la récupération des corrections.';
$string['api_get_amcquiz_corrections_error'] = 'Une erreur est survenue lors de la récupération des corrections.';
$string['api_get_amcquiz_corrections_curl_error'] = 'Une erreur CURL est survenue lors de la récupération des corrections.';

$string['api_upload_sheets_success'] = 'Succès de l\'upload des copies.';
$string['api_upload_sheets_error'] = 'Une erreur est survenue lors de l\'upload des copies.';
$string['api_upload_sheets_curl_error'] = 'Une erreur CURL est survenue lors de l\'upload des copies.';

$string['api_delete_all_sheets_success'] = 'Succès de la suppression des copies.';
$string['api_delete_all_sheets_error'] = 'Une erreur est survenue lors de la suppression des copies.';
$string['api_delete_all_sheets_curl_error'] = 'Une erreur CURL est survenue lors de la suppression des copies.';

$string['api_delete_amcquiz_success'] = 'Succès de la suppression du quiz.';
$string['api_delete_amcquiz_error'] = 'Une erreur est survenue lors de la suppression du quiz.';
$string['api_delete_amcquiz_curl_error'] = 'Une erreur CURL est survenue lors de la suppression du quiz.';

$string['curl_init_amcquiz_no_key'] = 'Aucune clée trouvée pour l\'authentification avec l\'API';
