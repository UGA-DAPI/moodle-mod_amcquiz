<?php

// module
$string['modulename'] = 'AMC Quiz';



// plugin global settings
$string['settings_scoring_rules'] = "Chaque groupe de règle est séparé par au moins 3 tirets [---].
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

$string['settings_socring_rules_default'] = "Tout ou rien
Pour toute question, tous les points si la réponse est totalement juste, 0 sinon.
S ; default ; e=0,v=0,m=0,b=SCORE
M ; default ; e=0,mz=SCORE";


// Settings.
$string['settings_code_length_short'] = 'Longueur code';
$string['settings_code_length_full'] = 'Longueur du code étudiant pour l\'affichage AMC';
$string['settings_instructionslstudent_short'] = 'Consigne / n° étudiant';
$string['settings_instructionslstudent_full'] = 'Valeur par défaut du champ homonyme, à la création de questionnaires papier.';
$string['settings_instructionslstudent_default'] = 'Veuillez coder votre numéro d\'étudiant ci-contre, et écrire votre nom dans la case ci-dessous.';
$string['settings_instructionslnamestd_short'] = 'Zone d\'identification / Standard';
$string['settings_instructionslnamestd_full'] = 'Consigne par défaut du champ, à la création d\'un questionnaires papier standard.';
$string['settings_instructionslnamestd_default'] = 'Nom et prénom';
$string['settings_instructionslnameanon_short'] = 'Zone d\'identification / Anonyme';
$string['settings_instructionslnameanon_full'] = 'Consigne par défaut du champ, à la création d\'un questionnaires papier anonyme.';
$string['settings_instructions_short'] = 'Default instructions';
$string['settings_instructions_full'] = 'Les élements sont séparés par au moins 3 tirets. La première ligne de chaque block sera le titre affiché dans la liste déroulante. Exemple:<pre>Concours<br/>Vous avez 4 heures.<br/>L\'anonymat est garanti.<br/>---<br/>Premier examen<br/>Utilisez de préférence un crayon et noricissez complètement chaque case sélectionnée.</pre>';
$string['settings_instructions_default'] = 'Concours
Vous avez 4 heures.
L\'anonymat est garanti.
---
Premier examen
Utilisez de préférence un crayon et noricissez complètement chaque case sélectionnée.';
$string['settings_idnumberprefixes_short'] = 'Préfixes du n° d\'étudiant';
$string['settings_idnumberprefixes_full'] = '<p>Préfixes, un par ligne. Attention aux espaces.</p><p>Chacun des préfixes sera inséré au début du numéro d\'étudiant de chaque copie, jusqu\'à ce que l\'étudiant soit identifié parmi les utilisateurs inscrits dans Moodle (cf import LDAP et idnumber). Si aucun préfixe ne permet de trouver l\'étudiant, une identification sans préfixe sera ensuite testée.</p>';


// Instance settings
$string['modform_amcquizname'] = 'Questionnaire';
$string['modform_uselatexfile'] = 'Utiliser un fichier Latex';
$string['modform_uselatexfilelabel'] = 'Le fichier Latext défini les paramètres AMC et certains paramètres du questionnaire.';
$string['modform_latexfile'] = 'Fichier Latex (*.tex).';
$string['modform_instructionsheader'] = 'Instructions';
$string['modform_top_instructions_predefined'] = 'Instructions prédéfinies.';
$string['modform_top_instructions_predefined_help'] = 'Choisissez une des instruction prédéfinie pour remplir automatiquement le champ instructions supérieures.';
$string['modform_top_instructions'] = 'Instructions supérieures';
$string['modform_description'] = 'Description';
$string['modform_description_help'] = 'Une description courte du questionnaire.';
$string['modform_anonymous'] = 'Questionnaire annonyme.';
$string['modform_studentnumber_instructions'] = 'Instructions pour le numéro d\'étudiant.';
$string['modform_studentname_instructions'] = 'Instructions pour le nom d\'étudiant.';

// add those fields to the form
$string['modform_scoring_parameters_header'] = 'Score';
$string['modform_grademax'] = 'Note maximale';
$string['modform_gradegranularity'] = 'Granularité de la note';
$string['modform_graderounding_strategy'] = 'Stratégie pour l\'arrondi de la note.';
$string['modform_scoring_strategy'] = 'Stratégie pour le calcul du score.';
$string['grade_rounding_strategy_nearest'] = 'Au plus proche';
$string['grade_rounding_strategy_lower'] = 'Inférieur';
$string['grade_rounding_strategy_upper'] = 'Suppérieur';

$string['modform_amc_parameters_header'] = 'Paramètres AMC';
$string['modform_sheets_versions'] = 'Nombre de versions';
$string['modform_questions_columns'] = 'Numbre de colonnes pour les questions';
$string['modform_shuffle_questions'] = 'Ordre aléatoire des questions';
$string['modform_shuffle_answers'] = 'Ordre aléatoire des réponses';
$string['modform_separate_sheets'] = 'Copies de réponse séparées';
$string['modform_sheets_columns'] = 'Nombre de colonnes pour chaque copie';

$string['modform_display_scores'] = 'Affichage des scores';
$string['modform_display_scores_no'] = 'Ne pas afficher';
$string['modform_display_scores_beginning'] = 'Afficher au début de la question';
$string['modform_display_scores_end'] = 'Afficher à la fin de la question';

$string['modform_mark_multi'] = 'Marquer si plusieurs bonnes réponses';
$string['modform_mark_multi_help'] = 'Si cette case est cochée, un trèfle apparaitra si une question a plusieurs bonnes réponses.';

$string['modform_display_score_rules'] = 'Afficher la règle de calcul du score';
$string['modform_display_score_rules_help'] = 'La règle pour le calcul du score sera imprimée sur la copie.';

$string['modform_custom_layout'] = 'Agencement personnalisé';
$string['modform_custom_layout_help'] = 'Définir un agencement personnalisé pour AMC';
