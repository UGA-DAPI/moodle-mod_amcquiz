<?php

namespace mod_amcquiz\local\managers;

class amcparamsmanager
{
    public function getGradeRoundingStrategies()
    {
        return [
            'n' => get_string('grade_rounding_strategy_nearest', 'mod_amcquiz'),
            'i' => get_string('grade_rounding_strategy_lower', 'mod_amcquiz'),
            's' => get_string('grade_rounding_strategy_upper', 'mod_amcquiz')
        ];
    }
}
