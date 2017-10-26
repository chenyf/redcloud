<?php

namespace Common\Behaviors;

use Think\Behavior;
use Think\Hook;

class RegisterAppHookBehavior extends Behavior {

	public function run(&$param) {

		$listens = C('LISTEN');

		foreach ($listens as $hook => $listen) {
			Hook::add($hook, $listen);
		}

	}

}