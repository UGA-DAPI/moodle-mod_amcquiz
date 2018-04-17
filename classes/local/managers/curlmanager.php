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
        $apikey = get_config('mod_amcquiz', 'apiglobalkey');

        if (!$apikey || empty($apikey)) {
            return [
             'status' => 500,
             'message' => get_string('curl_init_amcquiz_no_key', 'mod_amcquiz'),
           ];
        }

        return [
         'status' => $status,
         'message' => 200 === $status ? get_string('curl_init_amcquiz_success', 'mod_amcquiz') : get_string('curl_init_amcquiz_error', 'mod_amcquiz'),
       ];

        /*$curlrequest = $this->build_base_curl_request('create.php', true);
        $postfields = [
         'key' => $apikey,
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

        return json_decode($result, true);*/
    }

    public function amcquiz_get_definition_file(\stdClass $amcquiz)
    {
        $status = 200;

        return [
          'status' => $status,
          'message' => 200 === $status ? get_string('curl_get_definition_file_success', 'mod_amcquiz') : get_string('curl_get_definition_file_error', 'mod_amcquiz'),
          'data' => [
            'url' => 'prepare-source.tex',
          ],
        ];
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
        $curlrequest = $this->build_base_curl_request('http://bbb.u-ga.fr/amc/', true);

        $postfields = [
          'zip' => $zip,
          'key' => $amcquiz->apikey,
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

        /*  $curlrequest = $this->build_base_curl_request('upload.php', true);
          $postfields = [
            'zip' => $zip,
            'key' => $amcquiz->apikey,
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

          return json_decode($result, true);*/
    }

    /**
     * Generate subjects, catalog and correction.
     *
     * @param stdClass $amcquiz
     *
     * @return array
     */
    public function generate_documents(\stdClass $amcquiz)
    {
        return [
           'status' => 200,
           'message' => 'success',
         ];
        /*
        $curlrequest = $this->build_base_curl_request('generate_documents.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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
        */
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
        $curlrequest = $this->build_base_curl_request('upload_latex.php', true);
        $postfields = [
          'file' => $file,
          'key' => $amcquiz->apikey,
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
        $curlrequest = $this->build_base_curl_request('deleteunrecognized.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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
     * [get_amcquiz_documents description].
     *
     * @param stdClass $amcquiz [description]
     *
     * @return [type] [description]
     */
    public function get_amcquiz_documents(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('documents.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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

    // @TODO get Sheets via curl
    public function get_amcquiz_sheets(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('sheets.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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
        $curlrequest = $this->build_base_curl_request('associations.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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
        return [
         'status' => 200,
         'message' => 'hurray!',
        ];
    }

    public function associate_sheet_manually(\stdClass $amcquiz, string $filecode, string $idnumber)
    {
        return [
         'status' => 200,
         'message' => 'hurray!',
        ];
    }

    public function launch_grade(\stdClass $amcquiz)
    {
        return [
         'status' => 200,
         'message' => 'hurray!',
        ];

        /*$curlrequest = $this->build_base_curl_request('grade.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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

        return json_decode($result, true);*/
    }

    public function annotate(\stdClass $amcquiz)
    {
        return [
         'status' => 200,
         'message' => 'hurray!',
        ];
    }

    public function get_amcquiz_grade_stats(\stdClass $amcquiz)
    {
        $curlrequest = $this->build_base_curl_request('get_notation_stats.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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
        $curlrequest = $this->build_base_curl_request('get_notation_files.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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
        $curlrequest = $this->build_base_curl_request('corrections.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
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
        $curlrequest = $this->build_base_curl_request('upload_sheets.php', true);
        $postfields = [
          'key' => $amcquiz->apikey,
          'file' => $file,
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
        return [
            'status' => 200,
            'message' => 'success',
        ];
    }

    public function delete_amcquiz(\stdClass $amcquiz)
    {
        return [
            'status' => 200,
            'message' => 'success',
        ];
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
