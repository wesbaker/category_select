<?php

require_once PATH_THIRD."wb_category_select/config.php";

return array(
    'author'         => 'Wes Baker',
    'author_url'     => 'https://github.com/wesbaker/category_select.ee2_addon',
    'name'           => WB_CAT_SELECT_NAME,
    'description'    => 'Select (dropdown or multiselect) field that shows a list of pre-defined category groups and the categories within them.',
    'version'        => WB_CAT_SELECT_VER,
    'namespace'      => '\\',
	'fieldtypes' => array(
		'wb_category_select' => array(
			'name' => 'WB Category Select',
			'compatibility' => 'text' 
		)
	)
);
