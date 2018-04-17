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
             'message' => 'error',
           ];
        }

        return json_decode($result, true);
    }

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
     * Get subject, catalog, correction action links.
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

    // @TODO get Sheets via curl
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

    public function launch_association($amcquiz)
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

    public function launch_grade(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('grading/grade', true);
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

        return json_decode($result, true);
    }

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

    private function build_base_curl_request(string $actionurl, bool $isPost = false)
    {
        $apiurl = get_config('mod_amcquiz', 'apiurl');
        //$url = $apiurl.$actionurl;
        $url = $actionurl;
        $curlrequest = curl_init($url);
        curl_setopt($curlrequest, CURLOPT_RETURNTRANSFER, true);
        if ($isPost) {
            curl_setopt($curlrequest, CURLOPT_POST, true);
        }

        return $curlrequest;
    }
}
