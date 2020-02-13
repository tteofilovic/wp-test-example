<?php

/**
 * Plugin Name:       WordPress Test Plugin
 * Version:           1.0.0
 */

define('TEST_STARS_DIR_URI', plugin_dir_url( __FILE__ ));

require_once('vendor/autoload.php');

$stars = new TestStars\Model\Stars();
$adminStars = new TestStars\Controller\Admin($stars);
$frontStars = new \TestStars\Controller\Front($stars);