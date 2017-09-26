<?php

require_once PATH_THIRD . 'wb_category_select/config.php';

return array(
	'author'      => 'Wes Baker',
	'author_url'  => 'http://wesbaker.com/',
	'name'        => WB_CAT_SELECT_NAME,
	'description' => 'Drop down field that shows a list of pre-defined category groups and the categories within them. Updates automatically as you add categories to those groups.',
	'version'     => WB_CAT_SELECT_VER,
	'namespace'   => 'WesBaker\CategorySelect',
	'settings_exist' => FALSE,
	'fieldtypes' => array(
		'wb_category_select' => array(
			'name' => WB_CAT_SELECT_NAME
		)
	)
);
