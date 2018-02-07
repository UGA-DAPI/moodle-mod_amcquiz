<?php
// This file is part of mod_offlinequiz for Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Creates the PDF forms for offlinequizzes
 *
 * @package       mod
 * @subpackage    amcquiz
 * @author        Juergen Zimmer <zimmerj7@univie.ac.at>
 * @copyright     2012 University of Vienna
 * @since         Moodle 2.2+
 * @license       http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace mod_amcquiz;

defined('MOODLE_INTERNAL') || die('');


class translator
{

    /**
     * A dictionnary describing html tags and corresponding latex formula
     * @var array
     */
    private $texdictionnary;

    /**
     * DOMDocument created from the html
     * @var DOMDocument
     */
    private $document;

    /**
     * Informations kept for errors / warning along the data process
     * @var array
     */
    private $infos;

    /**
     * amcquiz parameters
     * used for image adaptation
     * @var stdClass
     */
    private $quizparameters;

    public function __construct($quizparameters = null) {
        $this->texdictionnary = $this->get_html_tex_dictionnary();
        $this->infos = [
          'errors' => [],
          'warnings' => []
        ];
        $this->quizparameters = $quizparameters;
    }

    /**
     * Function to replace @@PLUGINFILE@@ references to a proper url and copy moodle files to local folder.
     * This methode is highly inspired from offline quiz moodle plugin
     * https://github.com/academic-moodle-cooperation/moodle-mod_offlinequiz/blob/master/html2text.php#L50
     *
     * @param string $input The input string (Moodle HTML) $content
     * @param int $contextid The context ID. $question->contextid
     * @param string $filearea The filearea used to locate the image files. 'questiontext' | 'answer'
     * @param int $itemid The itemid used to locate the image files. $question->id | $answer->id
     * @param string $destfolder the folder where we have to copy the images.
     * @param string $type detailed type of the image 'question-answer' / 'question-description' / 'group-description'
     * @return string The result string
     */
    public function fix_img_paths($html, $contextid, $filearea, $itemid, $destfolder, $type) {
        global $CFG, $DB;

        require_once($CFG->dirroot.'/filter/tex/lib.php');
        require_once($CFG->dirroot.'/filter/tex/latex.php');
        $fs = get_file_storage();

        $output = $html;

        $strings = preg_split("/<img/i", $output);

        $output = array_shift($strings);

        foreach ($strings as $string) {
            // Define a unique temporary name for each image file.
            srand(microtime() * 1000000);
            $unique = str_replace('.', '', microtime(true) . '_' . rand(0, 100000));

            $imagetag = substr($string, 0, strpos($string, '>'));
            $attributestrings = explode(' ', $imagetag);
            $attributes = array();
            foreach ($attributestrings as $attributestring) {
                $valuepair = explode('=', $attributestring);
                if (count($valuepair) > 1 && strlen(trim($valuepair[0])) > 0) {
                    $attributes[strtolower(trim($valuepair[0]))] = str_replace('"', '', str_replace("'", '', $valuepair[1]));
                }
            }

            if (array_key_exists('width', $attributes) && $attributes['width'] > 0) {
                $imagewidth = $attributes['width'];
            } else {
                $imagewidth = 0;
            }


            if (array_key_exists('height', $attributes) && $attributes['height'] > 0) {
                $imageheight = $attributes['height'];
            } else {
                $imageheight = 0;
            }

            $imagefilename = '';
            if (array_key_exists('src', $attributes) && strlen($attributes['src']) > 10) {
                $pluginfilename = $attributes['src'];
                $imageurl = false;
                $parts = preg_split("!$CFG->wwwroot/filter/tex/pix.php/!", $pluginfilename);

                if (preg_match('!@@PLUGINFILE@@/!', $pluginfilename)) {

                    $pluginfilename = str_replace('@@PLUGINFILE@@/', '', $pluginfilename);
                    $pathparts = pathinfo($pluginfilename);
                    if (!empty($pathparts['dirname']) && $pathparts['dirname'] != '.') {
                        $filepath = '/' . $pathparts['dirname'] . '/';
                    } else {
                        $filepath = '/';
                    }
                    if ($imagefile = $fs->get_file($contextid, 'question', $filearea, $itemid, $filepath, rawurldecode($pathparts['basename']))) {
                        $imagefilename = $imagefile->get_filename();
                        // Copy image content to temporary file.
                        $pathparts = pathinfo($imagefilename);

                        $file = 'media/' . $unique . '.' . strtolower($pathparts["extension"]);
                        clearstatcache();
                        if (!check_dir_exists($destfolder  . DIRECTORY_SEPARATOR . 'media/', true, true)) {
                            print_error("Could not create data directory");
                        }
                        $imagefile->copy_content_to($destfolder . DIRECTORY_SEPARATOR . $file);
                    } else {
                        $output .= 'Image file not found ' . $pathparts['dirname'] . DIRECTORY_SEPARATOR . $pathparts['basename'];
                        $this->infos['errors'][] = $output;
                    }
                } else {
                    // Image file URL.
                    $imageurl = true;
                }

                if (!$imageurl) {

                    if (!file_exists($destfolder . DIRECTORY_SEPARATOR . $file)) {
                        $output .= get_string('imagenotfound', 'amcquiz', $file);
                    } else {
                        // In answer texts we want a line break to avoid the picture going above the line.
                        if ($filearea == 'answer') {
                            $output .= '<br/>';
                        }

                        // Finally, add the image tag
                        $output .= '<img src="' . $file . '" data-type="' . $type . '" align="middle" width="' . $imagewidth . '" height="' .
                            $imageheight .'"/>';
                    }
                }
            }
            // insert formated html in the right place
            $output .= substr($string, strpos($string, '>') + 1);
        }

        return $output;
    }

    public function html_to_tex($html, $contextid = null, $filearea = null, $itemid = null, $destfolder = null, $type = null) {
        // call fix_img_paths only if necessary (ie not for global instruction)
        if ($contextid) {
            $html = $this->fix_img_paths($html, $contextid, $filearea, $itemid, $destfolder, $type);
        }

        $this->document = new \DOMDocument();
        $this->document->loadHTML($html, LIBXML_COMPACT | LIBXML_HTML_NOIMPLIED | LIBXML_NOBLANKS | LIBXML_NOCDATA | LIBXML_NOENT | LIBXML_NONET);
        $latex = $this->parse_dom($this->document);

        return [
          'latex' => $latex,
          'errors' => $this->infos['errors'],
          'warnings' => $this->infos['warnings']
        ];
    }

    public function parse_dom(\DOMDocument $document){
        $output = '';
        foreach ($document->childNodes as $node) {
            $output .= $this->node_to_tex($node);
        }
        return $output;
    }

    /**
     * @param DOMNode $node
     * @return string
     */
    protected function node_to_tex(\DOMNode $node) {
        switch ($node->nodeType) {
            case XML_ELEMENT_NODE:
                return  $this->element_to_tex($node);
                break;
            case XML_TEXT_NODE:
                return  $this->text_to_tex($node->nodeValue);
                break;
            case XML_DOCUMENT_TYPE_NODE:
                return  '';
                break;
            default:
                $this->infos['errors'][] = 'unexpected node: ' . $node->nodeName;
                return  '';
        }
    }

    /**
     * @param DOMElement $e
     * @return string
     */
    protected function element_to_tex(\DOMElement $e) {
        $wrapper = null;

        if ($e->hasAttribute('class')) {
            $classes = preg_split('/\s+/', $e->getAttribute('class'));
            foreach ($classes as $class) {
                if (isset($this->texdictionnary[$e->nodeName . '.' . $class])) {
                    $wrapper = $this->mapping_to_tex($this->texdictionnary[$e->nodeName . '.' . $class], $e);
                    break;
                }
            }
        }

        if (!isset($wrapper)) {
            if (isset($this->texdictionnary[$e->nodeName])) {
                $wrapper = $this->mapping_to_tex($this->texdictionnary[$e->nodeName], $e);
            } else {
                $this->infos['warnings'][] = 'unknown tag: ' . $e->nodeName;
                return '';
            }
        }

        if ($wrapper->hide) {
            return '';
        }

        $tex = $wrapper->before;
        if ($e->hasChildNodes()) {
            foreach ($e->childNodes as $elem) {
                $tex .= $this->node_to_tex($elem);
            }
        } else {
            $tex .= $wrapper->content;
        }
        $tex .= $wrapper->after;
        return $tex;
    }

    protected function create_wrapper(string $before = null, string $after = null) {
        $wrapper = new \stdClass();
        $wrapper->hide = false;
        $wrapper->content = '';
        $wrapper->before = $before ? $before : '';
        $wrapper->after = $after ? $after : '';
        return $wrapper;
    }

    /**
     * Convert a simple HTML string (no tag, no entity) into a TeX string.
     *
     * @param string $htmlText
     * @return string
     */
    protected function text_to_tex($htmlText) {
        return utf8_decode(html_entity_decode(htmlentities($htmlText)));
    }

    /**
     * @param array $texdictionnaryelement
     * @param DOMElement $element
     * @return stdClass
     */
    protected function mapping_to_tex($texdictionnaryelement, $element) {

        if (isset($texdictionnaryelement['type'])) {
            if ($texdictionnaryelement['type'] === 'hide') {
                $wrapper = $this->create_wrapper();
                $wrapper->hide = true;
                return $wrapper;
            } elseif ($texdictionnaryelement['type'] === 'skip') {
                return $this->create_wrapper();
            } elseif ($texdictionnaryelement['type'] === 'custom') {
                if (isset($texdictionnaryelement['method'])) {
                    $function = (string) $texdictionnaryelement['method'];
                } else {
                    $function = "tag_{$element->nodeName}_to_tex";
                }
                return $this->$function($element);
            } elseif (isset($texdictionnaryelement['tex'])) {
                if ($texdictionnaryelement['type'] === 'macro') {
                    $before = '\\' . $texdictionnaryelement['tex'] . '{';
                    $after = '}';
                    $wrapper = $this->create_wrapper($before, $after);
                    $wrapper->content = $element->nodeValue ? $element->nodeValue : $element->nodeText;
                    return $wrapper;
                } elseif ($texdictionnaryelement['type'] === 'env') {
                    $before = '\\begin{' . $texdictionnaryelement['tex'] . '}';
                    $after = '\end{' . $texdictionnaryelement['tex'] . '}';
                    $wrapper = $this->create_wrapper($before, $after);
                    $wrapper->content = $element->nodeValue ? $element->nodeValue : $element->nodeText;
                    return $wrapper;
                } elseif ($texdictionnaryelement['type'] === 'raw' && is_array($texdictionnaryelement['tex']) && count($texdictionnaryelement['tex']) === 2) {
                    $before = $texdictionnaryelement['tex'][0];
                    $after = $texdictionnaryelement['tex'][1];
                    $wrapper = $this->create_wrapper($before, $after);
                    $wrapper->content = $element->nodeValue ? $element->nodeValue : $element->nodeText;
                    return $wrapper;
                }
            }
        } else {
            // the current tag is not referenced in dictionnary
            $wrapper = $this->create_wrapper();
            $wrapper->hide = true;
            $this->infos['warnings'][] = 'unknown element in dictionnary: ' . $element->nodeName;
            return $wrapper;
        }
    }

    /**
     * @param DOMElement $e
     * @return stdClass $wrapper
     */
    protected function tag_img_to_tex(\DOMElement $e) {
        $wrapper = $this->create_wrapper();
        if (!$e->hasAttribute('src')) {
            $wrapper->hide = true;
            $this->infos['warnings'][] = 'an image was found with an empty src attribute.';
            return $wrapper;
        }
        $wrapper->before = '';
        $wrapper->after = '';
        $path = $e->getAttribute('src');
        // 'question-answer' / 'question-description' / 'group-description'
        $type = $e->getAttribute('data-type');
        $maxpxwidth = 528; // full width of a PDF including margin
        $maxpxheight = 350;

        $qcolumns = $this->quizparameters->qcolumns ? $this->quizparameters->qcolumns : 1;

        switch ($type) {
            case 'question-description':
                $maxpxwidth = (528 / $qcolumns) - 20;
                $maxpxheight = 150;
                break;
            case 'question-answer':
                $maxpxwidth = (528 / $qcolumns) - 40;
                $maxpxheight = 50;
                break;
        }


        // depends on image for answer or description size in px
        // should also depends on number of columns for answers ans questions...
      //  $maxpxwidth = $type === 'answer' ? 200 : 528;
      //  $maxpxheight = $type === 'answer' ? 100 : 350;


/*
\begin{figure}[position]
   \includegraphics[…]{…}
\end{figure}
 */

        $wrapper->content = '';
        if ($type === 'question-answer') {
            // the image should be displayed aside the preceding text
            // can not put figure without minipage since figure is a floating environment
            $wrapper->content .= '\begin{minipage}{0.48\textwidth}';
            $wrapper->content .= PHP_EOL;
            $wrapper->content .= '\begin{figure}[H]';
        }

        if ($e->hasAttribute('width')) {
            $width = $e->getAttribute('width');
            $width = $width > $maxpxwidth ? $maxpxwidth : $width;
            // 1px = 0.75 pt
            $wrapper->content .= '\includegraphics[width=' . 0.75 * $width . 'pt]{' . $path . '}';
        } elseif ($e->hasAttribute('height')) {
            $height = $e->getAttribute('height');
            $height = $height > $maxpxheight ? $maxpxheight : $height;
            $wrapper->content .= '\includegraphics[height='. 0.75 * $height . 'pt]{' . $path . '}';
        } else {
            $wrapper->content .= '\includegraphics[width=' . 0.75 * $maxpxwidth . 'pt]{' . $path . '}';
        }

        if($type === 'question-answer') {
            // the image should be displayed aside the preceding text
            $wrapper->content .= '\end{figure}';
            $wrapper->content .= PHP_EOL;
            $wrapper->content .= '\end{minipage}';
        }
        return $wrapper;
    }

    /**
     * @param DOMElement $e
     * @return stdClass $wrapper
     */
    protected function tag_table_to_tex(\DOMElement $e) {

        $wrapper = $this->create_wrapper();
        $cols = 0;
        $count = true;
        $xpath = new DOMXpath($this->document);
        $rows = [];
        foreach ($xpath->query('./thead/tr|./tbody/tr|./tr', $e) as $node) {
            if ($node->nodeType === XML_ELEMENT_NODE) {
                $cells = [];
                /* @var $node DOMElement */
                foreach ($node->childNodes as $td) {
                    if ($node->nodeType === XML_ELEMENT_NODE) {
                        $tagname = strtolower($td->nodeName);
                        if ($tagname === 'th' || $tagname === 'td') {
                            $cells[] = $this->node_to_tex($td);
                            if ($count) {
                                if ($td->hasAttribute('colspan')) {
                                    $cols += $td->getAttribute('colspan');
                                } else {
                                    $cols++;
                                }
                            }
                        }
                    }
                }
                $count = false;
                $rows[] = join(' & ', $cells);
            }
        }
        if (!$rows) {
            $wrapper->hide = true;
            return $wrapper;
        }
        $columns = array_fill(0, $cols, 'c');
        $wrapper->before = '\begin{tabular}{|' . join('|', $columns) . '|}\hline ';
        $wrapper->content = join(' \\\\ \hline ', $rows);
        $wrapper->after = ' \\\\ \hline\end{tabular}';
        return $wrapper;
    }

    /**
     * @param DOMElement $e
     * @return stdClass
     */
    protected function embedded_tex(\DOMElement $e) {
        $wrapper = $this->create_wrapper();
        $wrapper->content = $e->textContent;
        return $wrapper;
    }

    // Allowed types: macro, env, raw, hide, skip, custom. With 'custom', the parameter 'method' is expected, but defaults to 'tag<tagname>ToTex'. The call passes the DOMElement.
    public function get_html_tex_dictionnary() {
        $map = [
            "a" => [
              "type" => "raw",
              "tex" => ["{}", "{}"]
            ],
            "b" => [
                "type" => "macro",
                "tex" => "textbf"
            ],
            "blockquote" => [
                "type" => "env",
                "tex" => "quote"
            ],
            "body" => [
                "type" => "skip"
            ],
            "br" => [
                "type" => "raw",
                "tex" => ["\\", "\n"]
            ],
            "center" => [
                "type" => "env",
                "tex" => "center"
            ],
            "code.tex" => [
                "type" => "custom",
                "method" => "embedded_tex"
            ],
            "code" => [
                "type" => "env",
                "tex" => "verbatim"
            ],
            "dd" => [
                "type" => "raw",
                "tex" => [" ", "\n"]
            ],
            "div" => [
                "type" => "raw",
                "tex" => ["\n\n", "\n\n"]
            ],
            "dl" => [
                "type" => "env",
                "tex" => "description"
            ],
            "dt" => [
                "type" => "raw",
                "tex" => ["\\item[", "]"]
            ],
            "em" => [
                "type" => "macro",
                "tex" => "emph"
            ],
            "h1" => [
                "type" => "macro",
                "tex" => "section*"
            ],
            "h2" => [
                "type" => "macro",
                "tex" => "subsection*"
            ],
            "h3" => [
                "type" => "macro",
                "tex" => "subsubsection*"
            ],
            "h4" => [
                "type" => "macro",
                "tex" => "paragraph*"
            ],
            "h5" => [
                "type" => "macro",
                "tex" => "subparagraph*"
            ],
            "h6"=> [
                "type" => "raw",
                "tex" => ["{\\bf{}", "}"]
            ],
            "head"=> [
                "type" => "hide"
            ],
            "html"=> [
                "type"=> "skip"
            ],
            "i"=> [
                "type"=> "macro",
                "tex"=> "textit"
            ],
            "img"=> [
                "type"=> "custom"
            ],
            "li"=> [
                "type"=> "raw",
                "tex"=> ["\\item[]", ""]
            ],
            "meta"=> [
                "type"=> "hide"
            ],
            "ol"=> [
                "type"=> "env",
                "tex"=> "enumerate"
            ],
            "p"=> [
                "type"=> "raw",
                "tex"=> ["\n\n", "\n\n"]
            ],
            "pre"=> [
                "type"=> "env",
                "tex"=> "verbatim"
            ],
            "script"=> [
                "type"=> "kill"
            ],
            "span"=> [
                "type"=> "raw",
                "tex"=> ["{}", "{}"]
            ],
            "strong"=> [
                "type"=> "raw",
                "tex"=> ["{\\bf{}", "}"]
            ],
            "table"=> [
                "type"=> "custom"
            ],
            "tbody"=> [
                "type"=> "skip"
            ],
            "thead"=> [
                "type"=> "skip"
            ],
            "td"=> [
                "type"=> "skip"
            ],
            "th"=> [
                "type"=> "raw",
                "tex"=> ["{\\bf{}", "}"]
            ],
            "tr"=> [
                "type"=> "raw",
                "tex"=> [" \\\\ ", ""]
            ],
            "ul"=> [
                "type"=> "env",
                "tex"=> "itemize"
            ]
        ];

        return $map;
    }
}
