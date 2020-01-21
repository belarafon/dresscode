<?php

/**
 * Sub Modules are small model witch help change behavior of module in specific cases.
 * Sub Modules must work on hooks & filters which must be added in parent module.
 * Use HooksFilterManager helper to get, create, use filters & actions.
 **/

namespace bhr\Helper;

interface SubModulesInterface {
	public function initHooks();
}