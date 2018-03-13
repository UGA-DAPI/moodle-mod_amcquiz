<?php

namespace mod_amcquiz\local\managers;

class curlmanager
{
    // https://www.dewep.net/realisations/utiliser-curl-php
    public function delete_unrecognized_sheets($data)
    {
        $curl = curl_init('https://en.wikipedia.org/api/rest_v1/page/');
        //curl_setopt($curl, CURLOPT_URL, $lien);
        //curl_setopt($curl, CURLOPT_POST, true);
        // this is the default value so no need to
        curl_setopt($this->curlrequest, CURLOPT_HTTPGET, true);
        //curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $return = curl_exec($this->curlrequest);
        echo '<pre>';
        print_r(json_decode($return));
        curl_close($this->curlrequest);
        die;
    }

    public function test_post_api($amcquiz, $data = [])
    {
        $apikey = $amcquiz->apikey;
        $curlrequest = curl_init($apiurl.'index.php');
        curl_setopt($curlrequest, CURLOPT_RETURNTRANSFER, true);

        $postfields = [
          'name' => ' Henri Dus',
          'key' => $amcquiz->apikey,
        ];
        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);
        $result = curl_exec($curlrequest);
        curl_close($curlrequest);

        return json_decode($result, true);
    }

    public function send_zipped_amcquiz($amcquiz, $zip)
    {
        $curlrequest = $this->build_base_curl_request('upload.php', true);
        $postfields = [
          'zip' => $zip,
          'key' => $amcquiz->apikey,
        ];
        curl_setopt($curlrequest, CURLOPT_POSTFIELDS, $postfields);

        $result = curl_exec($curlrequest);
        curl_close($curlrequest);

        return json_decode($result, true);
    }

    private function build_base_curl_request($actionurl, $isPost = false)
    {
        $apiurl = get_config('mod_amcquiz', 'apiurl');
        $url = $apiurl.$actionurl;
        $curlrequest = curl_init($url);
        curl_setopt($curlrequest, CURLOPT_RETURNTRANSFER, true);
        if ($isPost) {
            curl_setopt($curlrequest, CURLOPT_POST, true);
        }

        return $curlrequest;
    }
}
