<?php

namespace mod_amcquiz\local\managers;

class curlmanager
{
    /**
     * First method to be called when an amcquiz instance is created
     * The API will initiate folders and associatie folder with key.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function init_amcquiz(\stdClass $amcquiz)
    {
        $status = 200;
        // if this key does not exist should return an error
        $apiglobalkey = get_config('mod_amcquiz', 'apiglobalkey');

        if (!$apiglobalkey || empty($apiglobalkey)) {
            return [
             'status' => 500,
             'message' => get_string('curl_init_amcquiz_no_key', 'mod_amcquiz'),
           ];
        }

        $curlrequest = $this->build_base_curl_request('quiz/add', true);
        $postfields = [
         'globalkey' => $apiglobalkey,
         'apikey' => $amcquiz->apikey,
        ];
        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
            'status' => 400,
            'message' => 'error',
          ];
        }

        return json_decode($result, true);
        /*
        return [
         'status' => $status,
         'message' => 200 === $status ? get_string('curl_init_amcquiz_success', 'mod_amcquiz') : get_string('curl_init_amcquiz_error', 'mod_amcquiz'),
        ];
        */
    }

    /**
     * Tell the API to delete an amcquiz.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function delete_amcquiz(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('quiz/delete', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Send the zipped file to API. The API will generate subjects, catalog and correction.
     *
     * @param stdClass $amcquiz
     * @param string   $zip     base64 encoded file content
     *
     * @return array
     */
    public function send_zipped_amcquiz(\stdClass $amcquiz, string $zip)
    {
        $curlrequest = $this->build_base_curl_request('document/from/zip', true);

        $postfields = [
          'zip' => $zip,
          'apikey' => $amcquiz->apikey,
        ];
        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);
        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error '.curl_error($curlrequest),
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Send the latex file from mod_form.php.
     *
     * @param stdClass $amcquiz
     * @param string   $file    base64 enconded file content
     *
     * @return array
     */
    public function send_latex_file(\stdClass $amcquiz, string $file)
    {
        $curlrequest = $this->build_base_curl_request('quiz/upload/latex', true);
        $postfields = [
          'file' => $file,
          'apikey' => $amcquiz->apikey,
        ];
        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Get prepare-source.tex file. noy used.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_latex_file(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('latex', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
        /*$status = 200;

        return [
          'status' => $status,
          'message' => 200 === $status ? get_string('curl_get_definition_file_success', 'mod_amcquiz') : get_string('curl_get_definition_file_error', 'mod_amcquiz'),
          'data' => [
            'url' => 'prepare-source.tex',
          ],
        ];*/
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////DOCUMENTS///////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Generate subjects, catalog and correction based on previously uploaded latex file.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function generate_documents(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('document/from/latex', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];
        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Get subject, catalog, correction "action links".
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_documents(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('documents', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
          'data' => [
              'subject' => 'fakesubjectactionurl',
              'catalog' => 'fakecatalogactionurl',
              'correction' => 'fakecorrectionactionnurl',
              'zip' => 'fakezipactionurl',
          ],
        ];

        //return json_decode($result, true);
    }

    /**
     * Get the amcquiz subject PDF file real link.
     *
     * @param stdClass $amcquiz [description]
     *
     * @return array
     */
    public function get_subject_pdf(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('documents/subject', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'actionurl' => $amcquiz->documents['subject'],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
        ];

        //return json_decode($result, true);
    }

    /**
     * Get the amcquiz catalog PDF file real link.
     *
     * @param stdClass $amcquiz [description]
     *
     * @return array
     */
    public function get_catalog_pdf(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('documents/catalog', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'actionurl' => $amcquiz->documents['catalog'],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
        ];

        //return json_decode($result, true);
    }

    /**
     * Get the amcquiz correction PDF file real link.
     *
     * @param stdClass $amcquiz [description]
     *
     * @return array
     */
    public function get_correction_pdf(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('documents/correction', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'actionurl' => $amcquiz->documents['correction'],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
        ];

        //return json_decode($result, true);
    }

    /**
     * Get the amcquiz "documents in a zip file" real link.
     *
     * @param stdClass $amcquiz [description]
     *
     * @return array
     */
    public function get_documents_zip(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('documents/zip', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'actionurl' => $amcquiz->documents['zip'],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
        ];

        //return json_decode($result, true);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////SHEETS///////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Upload scanned sheets.
     *
     * @param stdClass $amcquiz
     * @param string   $file    base64 encoded file
     *
     * @return array
     */
    public function upload_sheets(\stdClass $amcquiz, string $file)
    {
        $curlrequest = $this->build_base_curl_request('sheet/upload', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'sheets' => $file,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Delete all sheets.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function delete_all_sheets(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('sheet/delete', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'sheets' => $file,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Delete all malformed scaned sheets.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function delete_unrecognized_sheets(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('delete/unknown', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Delete one malformed scaned sheet.
     *
     * @param stdClass $amcquiz
     * @param string   $number
     *
     * @return array
     */
    public function delete_unrecognized_sheet(\stdClass $amcquiz, string $number)
    {
        $curlrequest = $this->build_base_curl_request('delete/unknown/student', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'studentnumber' => $number,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Get sheets data for a given amcquiz.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_sheets(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('sheet', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////ASSOCIATIONS////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * get association data for a given amcquiz.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_associations(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('association', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
               'status' => 400,
               'message' => 'error',
            ];
        }

        return json_decode($result, true);
    }

    /**
     * Launch association process.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function launch_association(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('association/associate/all', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
               'status' => 400,
               'message' => 'error',
            ];
        }

        return json_decode($result, true);
    }

    /**
     * associate one sheet manually.
     *
     * @param stdClass $amcquiz
     * @param string   $filecode filname version_number
     * @param string   $idnumber student idnumber
     *
     * @return array
     */
    public function associate_sheet_manually(\stdClass $amcquiz, string $filecode, string $idnumber)
    {
        $curlrequest = $this->build_base_curl_request('association/associate/one', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'filecode' => $filecode,
          'idnumber' => $idnumber,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
               'status' => 400,
               'message' => 'error',
            ];
        }

        return json_decode($result, true);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///////////////////////////////////////////////GRADES///////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * launch grade process.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function launch_grade(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('grading/grade', true);
        $scoringrules = amcquiz_parse_scoring_rules();
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'gradegranularity' => $amcquiz->parameters->gradegranularity,
          'grademax' => $amcquiz->parameters->grademax,
          'graderounding' => $amcquiz->parameters->graderounding,
          'scoringrules' => $scoringrules[$amcquiz->parameters->scoringset],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * get grade stats.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_grade_stats(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('grading/stats', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Get amcquiz grade documents "action links".
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_grade_files(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('grading', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
          'data' => [
              'csv' => 'fakescsvactionurl',
              'ods' => 'fakeodsactionurl',
              'apogee' => 'fakeapogeeactionnurl',
          ],
        ];

        return json_decode($result, true);
    }

    /**
     * Get grade CSV file real link.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_grade_csv(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('grading/csv', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'actionurl' => $amcquiz->grades['files']['csv'],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
        ];

        //return json_decode($result, true);
    }

    /**
     * Get grade ODS file real link.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_grade_ods(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('grading/ods', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'actionurl' => $amcquiz->grades['files']['ods'],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
        ];

        //return json_decode($result, true);
    }

    /**
     * Get grade APOGEE file real link.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_grade_apogee(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('grading/apogee', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
          'actionurl' => $amcquiz->grades['files']['apogee'],
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return [
          'status' => 200,
          'message' => 'success',
        ];

        //return json_decode($result, true);
    }

    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////CORRECTION//////////////////////////////////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

    /**
     * Launch correcttion process.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function annotate(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('annotation/annotate', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Get correction PDF file real link.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_correction_pdf(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('annotation/pdf', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Get amcquiz corrections data.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function get_amcquiz_corrections(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('annotation', true);
        $postfields = [
          'apikey' => $amcquiz->apikey,
        ];

        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);
        if (!$result) {
            return [
             'status' => 400,
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

    /**
     * Build a curl request.
     *
     * @param string $actionurl the action to call
     * @param bool   $isPost
     *
     * @return curl
     */
    private function build_base_curl_request(string $actionurl, bool $isPost = false)
    {
        $apiurl = get_config('mod_amcquiz', 'apiurl');
        $url = $apiurl.$actionurl;
        $curlrequest = curl_init($url);
        curl_setopt($curlrequest, CURLOPT_HTTPHEADER, array('Host: '.$_SERVER['HTTP_HOST']));
        curl_setopt($curlrequest, CURLOPT_RETURNTRANSFER, true);
        if ($isPost) {
            curl_setopt($curlrequest, CURLOPT_POST, true);
        }

        return $curlrequest;
    }
}
