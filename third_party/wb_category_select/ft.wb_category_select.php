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
		$checkboxes = $this->_build_category_checkboxes($data);
		
		$this->EE->lang->loadfile('wb_category_select');
		$this->EE->table->add_row(
			lang('wb_category_select_groups', 'wb_category_select_groups'),
			$checkboxes
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
		$checkboxes = $this->_build_category_checkboxes($settings);
		
		$this->EE->lang->loadfile('wb_category_select');
		return array(
			array(lang('wb_category_select_groups'), $checkboxes)
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
				'category_groups'  => array()
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
						) ?
						TRUE : FALSE;
			
			// Build checkbox
			$checkboxes .= "<p><label>";
			$checkboxes .= form_checkbox('wb_category_select[category_groups][]', $row["group_id"], $checked);
			$checkboxes .= " " . $row['group_name'];
			$checkboxes .= "</label></p>";
		}
		
		return $checkboxes;
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
	
	public function save_cell_settings($settings)
	{
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
		// Figure out field_name and field_id
		$field_name = $cell ? $this->cell_name : $this->field_name;
		$field_id = str_replace(array('[', ']'), array('_', ''), $field_name);
		
		// Establish Settings
		$settings = $cell ? $this->settings['wb_category_select'] : $this->settings;
		
		// Build options array
		$options = $this->_build_category_list($settings);
		
		return form_dropdown($field_name, $options, $data, 'id="'.$field_id.'"');
	}
	
	/**
	 * Build the list of categories given a settings object that contains category groups
	 * @param Object $settings Settings Object for the field. If passing in matrix cell settings, only send the field's settings (e.g. $this->settings['field_name'])
	 * @return array Multidimensional array of category groups and categories
	 */
	private function _build_category_list($settings)
	{
		$options = array();
		$site_id = $this->EE->config->item('site_id');
		
		foreach ($settings['category_groups'] as $category_group_id) {
			// Get Category Group Name for optgroups
			$category_group_name = $this->EE->db->select('group_name')->get_where('category_groups', array('group_id' => $category_group_id))->result_array();
			$category_group_name = $category_group_name[0]['group_name'];
			
			// Get Categories based on Category Group
			$categories = $this->EE->db->select('cat_id, cat_name')->get_where('categories', array("site_id" => $site_id, "group_id" => $category_group_id));
			$options_inner = array();
			foreach ($categories->result_array() as $index => $category) {
				$options_inner[$category['cat_id']] = $category['cat_name'];
			}
			$options["$category_group_name"] = $options_inner;
		}
		
		return $options;
	}
}
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
		$checkboxes = $this->_build_category_checkboxes($data);
		
		$this->EE->lang->loadfile('wb_category_select');
		$this->EE->table->add_row(
			lang('wb_category_select_groups', 'wb_category_select_groups'),
			$checkboxes
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
		$checkboxes = $this->_build_category_checkboxes($settings);
		
		$this->EE->lang->loadfile('wb_category_select');
		return array(
			array(lang('wb_category_select_groups'), $checkboxes)
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
				'category_groups'  => array()
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
						) ?
						TRUE : FALSE;
			
			// Build checkbox
			$checkboxes .= "<p><label>";
			$checkboxes .= form_checkbox('wb_category_select[category_groups][]', $row["group_id"], $checked);
			$checkboxes .= " " . $row['group_name'];
			$checkboxes .= "</label></p>";
		}
		
		return $checkboxes;
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
		// Figure out field_name and field_id
		$field_name = $cell ? $this->cell_name : $this->field_name;
		$field_id = str_replace(array('[', ']'), array('_', ''), $field_name);
		
		// Establish Settings
		$settings = $cell ? $this->settings['wb_category_select'] : $this->settings;
		
		// Build options array
		$options = $this->_build_category_list($settings);
		
		return form_dropdown($field_name, $options, $data, 'id="'.$field_id.'"');
	}
	
	/**
	 * Build the list of categories given a settings object that contains category groups
	 * @param Object $settings Settings Object for the field. If passing in matrix cell settings, only send the field's settings (e.g. $this->settings['field_name'])
	 * @return array Multidimensional array of category groups and categories
	 */
	private function _build_category_list($settings)
	{
		$options = array();
		$site_id = $this->EE->config->item('site_id');
		
		foreach ($settings['category_groups'] as $category_group_id) {
			// Get Category Group Name for optgroups
			$category_group_name = $this->EE->db->select('group_name')->get_where('category_groups', array('group_id' => $category_group_id))->result_array();
			$category_group_name = $category_group_name[0]['group_name'];
			
			// Get Categories based on Category Group
			$categories = $this->EE->db->select('cat_id, cat_name')->get_where('categories', array("site_id" => $site_id, "group_id" => $category_group_id));
			$options_inner = array();
			foreach ($categories->result_array() as $index => $category) {
				$options_inner[$category['cat_id']] = $category['cat_name'];
			}
			$options["$category_group_name"] = $options_inner;
		}
		
		return $options;
	}
}
