<?php

namespace Cd;

use DB;

class Drupal
{
    public static function table( $t ){
    	return DB::connection('drupal')->table( $t );
    }
}
