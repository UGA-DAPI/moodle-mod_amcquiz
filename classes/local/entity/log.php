<?php

namespace mod_amcquiz\local\entity;

class log
{
      /**
       * @var integer
       */
      public $id;

      /**
       * Log amcquiz id
       * @var integer
       */
      public $amcquiz_id;

      /**
       * Log action name
       * @var string
       */
      public $action;

      /**
       * Log timestamp
       * @var integer
       */
      public $timecreated;
}
