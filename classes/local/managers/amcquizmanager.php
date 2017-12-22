<?php

namespace mod_amcquiz\managers;

class amcquizmanager
{


    public function get_amcquiz_record(int $id)
    {
        global $DB;
        // get amcquiz from db
    }

    public function build_from_record(stdClass $amcquizrecord)
    {
       return $amcquizrecord;
    }


}
