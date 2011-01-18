<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once PATH_THIRD.'wb_category_select/config.php';

/**
 * Wb Category Select Fieldtype Class for EE2
 *
 * @package   WB Category Select
 * @author    Wes Baker <wes@wesbaker.com>
 */
class Wb_category_select_ft extends EE_Fieldtype {

	var $info = array(
		'name'    => WB_CAT_SELECT_NAME,
		'version' => WB_CAT_SELECT_VER
	);

	// enable tag pairs
	var $has_array_data = TRUE;

	/**
	 * Fieldtype Constructor
	 */
	function Wb_category_select_ft()
	{
		parent::EE_Fieldtype();
	}

	// Settings --------------------------------------------------------------------

	/**
	 * Display Field Settings
	 * @param Array $data Field settings
	 */
	function display_settings($data)
	{
		$data = $this->_default_settings($data);
		
		$this->EE->lang->loadfile('wb_category_select');

		// Categories
		$this->EE->table->add_row(
			lang('wb_category_select_groups', 'wb_category_select_groups'),
			$this->_build_category_checkboxes($data)
		);

		// Multiple?
		$this->EE->table->add_row(
			lang('wb_category_select_multi', 'wb_category_select_multi'),
			$this->_build_multi_radios($data)
		);
	}

	/**
	 * Display Matrix Cell Settings
	 * @param Array $data Cell settings
	 * @return Array Multidimensional array of setting name, HTML pairs
	 */
	public function display_cell_settings($data)
	{
		$settings = (isset($data['wb_category_select'])) ? $data['wb_category_select'] : array();
		$settings = $this->_default_settings($settings);
		
		$this->EE->lang->loadfile('wb_category_select');

		return array(
			// Categories
			array(
				lang('wb_category_select_groups'),
				$this->_build_category_checkboxes($settings)
			),

			// Multiple?
			array(
				lang('wb_category_select_multi'),
				$this->_build_multi_radios($settings)
			)
		);
	}
	
	/**
	 * Builds the default settings
	 * @param Array $data Data array from display_settings or display_cell_settings
	 * @return Array $data variable merged with default settings
	 */
	private function _default_settings($data)
	{
		return array_merge(
			array(
				'category_groups'  => array(),
				'multi' => 'n'
			),
			(array) $data
		);
	}
	
	/**
	 * Builds a string of category checkboxes
	 * @param Array $data Data array from display_settings or display_cell_settings
	 * @return String String of checkbox fields
	 */
	private function _build_category_checkboxes($data)
	{
		// Get list of category groups
		$site_id = $this->EE->config->item('site_id');
		$category_groups = $this->EE->db->select("group_id, group_name")->get_where('category_groups', array("site_id" => $site_id));
		
		// Build checkbox list
		$checkboxes = '';
		$category_group_settings = $data['category_groups'];
		foreach ($category_groups->result_array() as $index => $row) {
			// Determine checked or not
			$checked = (
							is_array($category_group_settings) 
							AND is_numeric(array_search($row['group_id'], $category_group_settings))
						) ? TRUE : FALSE;
			
			// Build checkbox
			$checkboxes .= "<p><label>";
			$checkboxes .= form_checkbox('wb_category_select[category_groups][]', $row["group_id"], $checked);
			$checkboxes .= " " . $row['group_name'];
			$checkboxes .= "</label></p>";
		}
		
		return $checkboxes;
	}

	/**
	 * Builds a string of yes/no radio buttons
	 */
	private function _build_multi_radios($data)
	{
		return form_radio('wb_category_select[multi]', 'y', ($data['multi'] == 'y'), 'id="wb_category_select_multi_y"') . NL
			. lang('yes', 'wb_category_select_multi_y') . NBS.NBS.NBS.NBS.NBS . NL
			. form_radio('wb_category_select[multi]', 'n', ($data['multi'] == 'n'), 'id="wb_category_select_multi_n"') . NL
			. lang('no', 'wb_category_select_multi_n');
	}

	// Save Settings --------------------------------------------------------------------

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

	// Display Field --------------------------------------------------------------------

	/**
	 * Display Field
	 * @param Array $data Field data
	 */
	function display_field($data)
	{
		return $this->_build_field($data, FALSE);
	}
	
	/**
	 * Display Matrix Cell
	 * @param Array $data Cell data
	 */
	public function display_cell($data)
	{
		return $this->_build_field($data, TRUE);
	}
	
	/**
	 * Builds the field
	 * @param Array $data Field data
	 * @param Boolean $cell TRUE if the field is for a Matrix Cell, FALSE otherwise
	 * @return String The dropdown for the category select
	 */
	private function _build_field($data, $cell = FALSE)
	{
		// Establish Settings
		$settings = ($cell) ? $this->settings['wb_category_select'] : $this->settings;
		$settings = $this->_default_settings($settings);
		
		// Figure out field_name and field_id
		$field_name = ($cell) ? $this->cell_name : $this->field_name;
		$field_id = str_replace(array('[', ']'), array('_', ''), $field_name);
		
		// Build options array
		$options = $this->_build_category_list($settings);
		
		if ($settings['multi'] == 'y') {
			if (is_string($data)) $data = explode("\n", $data);
			return form_multiselect($field_name.'[]', $options, $data, 'id="'.$field_id.'"');
		}

		return form_dropdown($field_name, $options, $data, 'id="'.$field_id.'"');
	}
	
	/**
	 * Build the list of categories given a settings object that contains category groups
	 * @param Object $settings Settings Object for the field. If passing in matrix cell settings, only send the field's settings (e.g. $this->settings['field_name'])
	 * @return array Multidimensional array of category groups and categories
	 */
	private function _build_category_list($settings)
	{
		$options = ($settings['multi'] == 'y') ? array() : array('' => '');
		$site_id = $this->EE->config->item('site_id');
		
		foreach ($settings['category_groups'] as $category_group_id) {
			// Get Category Group Name for optgroups
			$category_group_name = $this->EE->db->select('group_name')->get_where('category_groups', array('group_id' => $category_group_id))->result_array();
			
			// If this isn't an array skip this item in the for loop
			if ( ! is_array($category_group_name) OR empty($category_group_name)) { continue; }
			
			$category_group_name = $category_group_name[0]['group_name'];
			
			// Get Categories based on Category Group
			$categories = $this->EE->db->select('cat_id, cat_name')->order_by('cat_name')->get_where('categories', array("site_id" => $site_id, "group_id" => $category_group_id));
			$options_inner = array();
			foreach ($categories->result_array() as $index => $category) {
				$options_inner[$category['cat_id']] = $category['cat_name'];
			}
			$options["$category_group_name"] = $options_inner;
		}
		
		return $options;
	}

	// Save Field --------------------------------------------------------------------

	/**
	 * Save Field
	 */
	function save($data)
	{
		// flatten array if multiple selections are allowed
		if (is_array($data)) {
			$data = implode("\n", $data);
		}

		return $data;
	}

	/**
	 * Save Cell
	 */
	function save_cell($data)
	{
		return $this->save($data);
	}

	// Tags --------------------------------------------------------------------

	/**
	 * Pre-process
	 *
	 * If multiple selections are allowed, this will turn the string of
	 * category IDs into an array.
	 */
	function pre_process($data)
	{
		// Establish Settings
		$settings = (isset($this->settings['wb_category_select'])) ? $this->settings['wb_category_select'] : $this->settings;
		$settings = $this->_default_settings($settings);

		// if multiple selections aren't allowed, just return the cat ID
		if ($settings['multi'] != 'y') return $data;

		$data = explode("\n", $data);

		foreach ($data as &$cat)
		{
			$cat = array('category_id' => $cat);
		}

		return $data;
	}

	/**
	 * Replace Tag
	 *
	 * If only a single category selection is allowed, this will just return
	 * the selected category ID. Otherwise, it'll loop through the tag pair,
	 * parsing the {category_id} single variable tags.
	 */
	function replace_tag($data, $params = array(), $tagdata = FALSE)
	{
		// Establish Settings
		$settings = (isset($this->settings['wb_category_select'])) ? $this->settings['wb_category_select'] : $this->settings;
		$settings = $this->_default_settings($settings);

		// if multiple selections aren't allowed, just return the cat ID
		if ($settings['multi'] != 'y') { return $data; }

		// ignore if no inner tagdata
		if ( ! $tagdata) { return; }

		// pre_process() fallback for Matrix
		if (is_string($data)) { $data = $this->pre_process($data); }

		// loop through the tag pair for each selected category,
		// parsing the {category_id} tags
		$parsed = $this->EE->TMPL->parse_variables($tagdata, $data);

		// backspace= param
		if (isset($params['backspace']) && $params['backspace'])
		{
			$parsed = substr($r, 0, -$params['backspace']);
		}

		return $parsed;
	}

}
