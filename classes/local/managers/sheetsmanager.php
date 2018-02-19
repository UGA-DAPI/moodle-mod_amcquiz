<?php

namespace mod_amcquiz\local\managers;

class sheetsmanager
{

    /**
     * Get questions related to a group
     * @param  int    $groupid
     * @param  int    $cmid course module id
     * @return array a collection of moodle question
     */
    public function delete_sheets(int $amcquizid)
    {
        $result = [];
        $result['errors'] = [];
        $result['warnings'] = [];

        return $result;
    }
}
