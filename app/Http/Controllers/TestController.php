<?php

namespace Cd\Http\Controllers;

use Illuminate\Http\Request;

use Cd\Http\Requests;

use Cd\Opportunity;

use Cd\Dsession;

use stdClass, DB;

class TestController extends Controller
{
	public function test() {

		$test = 'test';

		return $test;
	}
}