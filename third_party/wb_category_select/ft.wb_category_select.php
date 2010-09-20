<?php if (! defined('BASEPATH')) exit('No direct script access allowed');


if (! defined('wb_category_select_VER'))
{
	// get the version from config.php
	require PATH_THIRD.'wb_category_select/config.php';
	define('wb_category_select_VER',  $config['version']);
}


/**
 * Wb Category Select Fieldtype Class for EE2
 *
 * @package   WB Category Select
 * @author    Wes Baker <wes@wesbaker.com>
 */
class Wb_category_select_ft extends EE_Fieldtype {

	var $info = array(
		'name'    => 'WB Category Select',
		'version' => wb_category_select_VER
	);

	/**
	 * Fieldtype Constructor
	 */
	function Wb_category_select_ft()
	{
		parent::EE_Fieldtype();
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field Settings
	 */
	function display_settings($data)
	{		
		// merge in default field settings
		$data = array_merge(
			array(
				'category_groups'  => ''
			),
			$data
		);
		
		// Get list of category groups
		$site_id = $this->EE->config->item('site_id');
		$category_groups = $this->EE->db->query("SELECT group_id, group_name FROM exp_category_groups WHERE site_id = $site_id");
		
		// Build checkbox list
		$checkboxes = '';
		$category_group_settings = $data['category_groups'];
		foreach ($category_groups->result_array() as $index => $row) {
			// Determine checked or not
			$checked = (is_numeric(array_search($row['group_id'], $category_group_settings))) ? TRUE : FALSE;
			
			// Build checkbox
			$checkboxes .= "<p><label>";
			$checkboxes .= form_checkbox('wb_category_select[category_groups][]', $row["group_id"], $checked);
			$checkboxes .= " " . $row['group_name'];
			$checkboxes .= "</label></p>";
		}
		
		$this->EE->lang->loadfile('wb_category_select');
		$this->EE->table->add_row(
			lang('wb_category_select_groups', 'wb_category_select_groups'),
			$checkboxes
		);
	}

	// --------------------------------------------------------------------

	/**
	 * Save Field Settings
	 */
	function save_settings($settings)
	{
		$settings = array_merge(
			$this->EE->input->post('wb_category_select'),
			$settings
		);
		
		$settings['field_show_fmt'] = 'n';
		$settings['field_type'] = 'wb_category_select';

		return $settings;
	}

	// --------------------------------------------------------------------

	/**
	 * Display Field
	 */
	function display_field($data, $cell = FALSE)
	{		
		// Figure out site, field_name and field_id
		$site_id = $this->EE->config->item('site_id');
		$field_name = $cell ? $this->cell_name : $this->field_name;
		$field_id = str_replace(array('[', ']'), array('_', ''), $field_name);
		
		// Build options array
		$options = array();
		foreach ($this->settings['category_groups'] as $category_group_id) {
			// Get Category Group Name for optgroups
			$category_group_name = $this->EE->db->query("SELECT group_name FROM exp_category_groups WHERE group_id = $category_group_id");
			$category_group_name = $category_group_name->result_array();
			$category_group_name = $category_group_name[0]['group_name'];
			
			// Get Categories based on Category Group
			$categories = $this->EE->db->query("SELECT cat_id, cat_name FROM exp_categories WHERE site_id = $site_id AND group_id = $category_group_id");
			$options_inner = array();
			foreach ($categories->result_array() as $index => $category) {
				$options_inner[$category['cat_id']] = $category['cat_name'];
			}
			$options["$category_group_name"] = $options_inner;
		}
		
		return form_dropdown($field_name, $options, $data, 'id="'.$field_id.'"');
	}
}
