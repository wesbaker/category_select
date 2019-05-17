<?php if (!defined('BASEPATH')) { exit('No direct script access allowed'); }

require_once PATH_THIRD . 'wb_category_select/config.php';

/**
 * Wb Category Select Fieldtype Class for EE2
 *
 * @package   WB Category Select
 * @author    Wes Baker <wes@wesbaker.com>
 */
class Wb_category_select_ft extends EE_Fieldtype {

	var $info = array(
		'name'		=> WB_CAT_SELECT_NAME,
		'version'	=> WB_CAT_SELECT_VER
	);

	// enable tag pairs
	var $has_array_data = TRUE;
	
	private $EE2 = FALSE;
	
	public function __construct()
	{
		parent::__construct();
		
		if (defined('APP_VER') && version_compare(APP_VER, '3.0.0', '<'))
		{
			$this->EE2 = TRUE;
		}

	}

	// Settings --------------------------------------------------------------------

	/**
	 * Display Field Settings
	 * @param Array $data Field settings
	 */
	function display_settings($data)
	{
		$data = $this->_default_settings($data);

		ee()->lang->loadfile('wb_category_select');

		if ($this->EE2)
		{
			
			// Categories
			ee()->table->add_row(
				lang('wb_category_select_groups', 'wb_category_select_groups'),
				$this->_build_category_checkboxes($data)
			);

			// Multiple?
			ee()->table->add_row(
				lang('wb_category_select_multi', 'wb_category_select_multi'),
				$this->_build_radios($data)
			);
			ee()->table->add_row(
				lang('wb_category_select_show_first_level_only', 'wb_category_select_show_first_level_only'),
				$this->_build_radios($data, 'show_first_level_only')
			);
			ee()->table->add_row(
				lang('wb_category_select_multi_double_panes', 'wb_category_select_multi_double_panes'),
				$this->_build_radios($data, 'multi_double_panes')
			);
			
		}
		else
		{
			
			$fields[] = $this->_build_category_checkboxes($data);
			$fields[] = $this->_build_radios($data);
			$fields[] = $this->_build_radios($data, 'show_first_level_only');
			$fields[] = $this->_build_radios($data, 'multi_double_panes');
			
			return array('wb_category_select_groups' => array(
				'label' => 'field_options',
				'group' => 'wb_category_select',
				'settings' => $fields,
			));
		}
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

		ee()->lang->loadfile('wb_category_select');

		return array(
			// Categories
			array(
				lang('wb_category_select_groups'),
				$this->_build_category_checkboxes($settings, 'matrix')
			),
			// Multiple?
			array(
				lang('wb_category_select_multi'),
				$this->_build_radios($settings, 'multi', 'matrix')
			),
			array(
				lang('wb_category_select_show_first_level_only', 'wb_category_select_show_first_level_only'),
				$this->_build_radios($settings, 'show_first_level_only', 'matrix')
			),
			array(
				lang('wb_category_select_multi_double_panes', 'wb_category_select_multi_double_panes'),
				$this->_build_radios($settings, 'multi_double_panes', 'matrix')
			),
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
				'category_groups' => array(),
				'multi' => 'n',
				'show_first_level_only' => 'n',
				'multi_double_panes' => 'n'
			),
			(array) $data
		);
	}

	/**
	 * Builds a string of category checkboxes
	 * @param Array $data Data array from display_settings or display_cell_settings
	 * @return String String of checkbox fields
	 */
	private function _build_category_checkboxes($data, $type='')
	{
		// Get list of category groups
		$site_id = ee()->config->item('site_id');
		$category_groups = ee()->db->select("group_id, group_name")
			->get_where('category_groups', array("site_id" => $site_id));

		if ($this->EE2 || $type=='matrix')
		{
			// Build checkbox list
			$checkboxes = '';
			$category_group_settings = $data['category_groups'];
			foreach ($category_groups->result_array() as $index => $row)
			{
				// Determine checked or not
				$checked = (is_array($category_group_settings)
					AND is_numeric(array_search($row['group_id'], $category_group_settings))) ? TRUE : FALSE;

				// Build checkbox
				$checkboxes .= "<p><label>";
				$checkboxes .= form_checkbox('wb_category_select[category_groups][]', $row["group_id"], $checked);
				$checkboxes .= " " . $row['group_name'];
				$checkboxes .= "</label></p>";
			}

			return $checkboxes;
		}
		else
		{
			$cat_groups = array();
			foreach ($category_groups->result_array() as $index => $row)
			{
				$cat_groups[$row["group_id"]] = $row["group_name"];
			}

			return array(
				'title' => 'wb_category_select_groups',
				'fields' => array(
					'wb_category_select[category_groups]' => array(
						'type' => 'checkbox',
						'choices' => $cat_groups,
						'value' => $data['category_groups'],
					)
				)
			);
			
		}
	
	}

	/**
	 * Builds a string of yes/no radio buttons
	 */
	private function _build_radios($data, $name = 'multi', $type='')
	{

		if ($this->EE2 || $type=='matrix')
		{

			$radio_yes = form_radio(
				"wb_category_select[{$name}]",
				'y',
				($data[$name] == 'y'),
				"id='wb_category_select_{$name}_y'"
			);
			$radio_no = form_radio(
				"wb_category_select[{$name}]",
				'n',
				($data[$name] == 'n'),
				"id='wb_category_select_{$name}_n'"
			);

			return $radio_yes
				. NL . lang('yes', "wb_category_select_{$name}_y")
				. NBS . NBS . NBS . NBS . NBS . NL
				. $radio_no
				. NL . lang('no', "wb_category_select_{$name}_n");

		}
		else
		{
			return array(
				'title' => "wb_category_select_{$name}",
				//'desc' => 'wb_category_select_groups',
				'fields' => array(
					"wb_category_select[{$name}]" => array(
						'type' => 'yes_no',
						'value' => $data[$name],
					)
				)
			);
		}	

	}

	// Save Settings --------------------------------------------------------------------

	/**
	 * Save Field Settings
	 */
	function save_settings($settings)
	{
		if ($this->EE2)
		{
			$settings = array_merge(ee()->input->post('wb_category_select'), $settings);
			
			$settings['field_show_fmt'] = 'n';
			$settings['field_type'] = 'wb_category_select';
			
			return $settings;
		}
		else
		{

			$wb_category_select = ee()->input->post('wb_category_select');

			return array(
				'category_groups' => $wb_category_select['category_groups'],
				'multi' => $wb_category_select['multi'],
				'show_first_level_only' => $wb_category_select['show_first_level_only'],
				'multi_double_panes' => $wb_category_select['multi_double_panes'],
				'field_wide' => true
			);
			
		}

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

		if ($settings['multi'] == 'y')
		{
			if ($settings['multi_double_panes'] == 'y')
			{
				$this->_add_js_css($field_id, $cell);
			}
			
			if (is_string($data))
			{
				$data = explode("\n", $data);
			}

			return form_multiselect($field_name . '[]', $options, $data, 'id="' . $field_id . '" class="wb_multi_select" style="width:100%;" size=10');
		}

		return form_dropdown($field_name, $options, $data, 'id="' . $field_id . '"');
	}

	/**
	 * Build the list of categories given a settings object that contains category groups
	 * @param Object $settings Settings Object for the field. If passing in matrix cell settings, only send the field's settings (e.g. $this->settings['field_name'])
	 * @return array Multidimensional array of category groups and categories
	 */
	private function _build_category_list($settings)
	{
		$options = ($settings['multi'] == 'y') ? array() : array('' => '');
		$site_id = ee()->config->item('site_id');
		
		if ($settings['category_groups'])
		{
			foreach ($settings['category_groups'] as $category_group_id)
			{
				// Get Categories based on Category Group
				ee()->load->library('api');
				if ($this->EE2)
				{
					ee()->api->instantiate('channel_categories');
					$spacer = '&mdash;';
				}
				else 
				{
					ee()->legacy_api->instantiate('channel_categories');
					$spacer = '—';
				}

				$categories = ee()->api_channel_categories->category_tree($category_group_id);

				if ( ! empty($categories))
				{
					foreach ($categories as $cat_id => $cat_data)
					{
						if ($settings['show_first_level_only'] == 'n' || ($settings['show_first_level_only'] == 'y' && $cat_data[5] == 1))
						{
							$prefix = str_repeat($spacer, $cat_data[5] - 1);
							$options[$cat_data[3]][$cat_id] = $prefix . $cat_data[1];
						}
					}
				}
			}
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
		if (is_array($data))
		{
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

	/**
	 * Validate Matrix Cell
	 */
	function validate_cell($data)
	{
		if ($this->settings['col_required'] == 'y')
		{
			if (!$data)
			{
				return lang('col_required');
			}
		}

		return TRUE;
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
		return (is_string($data)) ? explode("\n", $data) : $data;
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

		// if multiple selections aren't allowed, or if no tagdata, send back
		// the category ID or piped list
		if (empty($tagdata))
		{
			return is_array($data) ? implode('|', $data) : $data;
		}

		// pre_process() fallback for Matrix
		if (is_string($data))
		{
			$data = $this->pre_process($data);
		}

		if (is_string($data))
		{
			$data = array($data);
		}

		// loop through the tag pair for each selected category,
		// parsing the {category_id} tags
		$parsed = ee()->TMPL->parse_variables(
			$tagdata,
			$this->_get_category_data($data)
		);

		ee()->load->library('typography');
		return ee()->typography->parse_file_paths($parsed);
	}

	// -------------------------------------------------------------------------

	/**
	 * Given a list of category IDs, returns
	 * @param  Array $cat_ids Array of category IDs
	 * @return Array          Array of data read for the parser containing
	 *                        category IDs, names, and url_titles
	 */
	private function _get_category_data($cat_ids)
	{
		$parse = array();
		
		if ($cat_ids) 
		{
			// Pull in category data and map it
			$category_query = ee()->db->where_in('cat_id', $cat_ids)
				->get('categories')
				->result_array();
			$category_data = array();
			foreach ($category_query as $data)
			{
				$category_data[$data['cat_id']] = $data;
			}

			// Create the array for parsing
			
			foreach ($cat_ids as $category_id)
			{
				if( isset( $category_data[$category_id] ) )
				{
					$process = array();
					foreach ($category_data[$category_id] as $k => $v)
					{
						$k = (strpos($k,'cat_') !== false) ? str_replace('cat_','category_',$k) : 'category_'.$k;
						$process[$k] = $v;
					}


					$parse[] = $process;
				}
			}
		}

		return $parse;
	}
	
	
	/**
	 * Set CP CSS and JS
	 */
	private function _add_js_css($field_id, $cell)
	{
		if ( ! ee()->session->cache('wb_category_select', 'cp_assets_set'))
		{
			$cssFile = 'wb_category_select/css/multi.min.css';
			$jsFile = 'wb_category_select/js/multi.min.js';
			
			$cssPath = PATH_THIRD_THEMES . $cssFile;
			$cssFileTime = (is_file($cssPath) ? filemtime($cssPath) : uniqid());
			
			$css = URL_THIRD_THEMES;
			$css .= "{$cssFile}?v={$cssFileTime}";
			$cssTag = "<link rel=\"stylesheet\" href=\"{$css}\">";
			ee()->cp->add_to_head($cssTag);

			$jsPath = PATH_THIRD_THEMES . $jsFile;
			$jsFileTime = (is_file($jsPath) ? filemtime($jsPath) : uniqid());
			
			$js = URL_THIRD_THEMES;
			$js .= "{$jsFile}?v={$jsFileTime}";
			$jsTag = "<script type=\"text/javascript\" src=\"{$js}\"></script>";
			ee()->cp->add_to_foot($jsTag);

			ee()->cp->add_to_head('
			<style>
				#'.$field_id.'.wb_multi_select { opacity: 0.2; }
				.multi-wrapper .non-selected-wrapper { background:#fff; }
				.multi-wrapper .item-group { padding:5px; }
				.multi-wrapper .item-group .group-label { padding:5px 0 10px 0; font-weight:bold; margin-left: -5px; }
				.multi-wrapper .item { padding: 7px 10px; border:1px solid #cdcdcd; margin-bottom:10px; }
				.multi-wrapper .search-input { margin:0 !important; }
				.multi-wrapper .non-selected-wrapper::-webkit-scrollbar, .multi-wrapper .selected-wrapper::-webkit-scrollbar{background-color:#f1f1f1;width:12px; }
				.multi-wrapper .non-selected-wrapper::-webkit-scrollbar-thumb, .multi-wrapper .selected-wrapper::-webkit-scrollbar-thumb {background-color: #cfcfcf; border: 3px solid #f1f1f1; -moz-border-radius: 10px; -webkit-border-radius: 10px; border-radius: 10px; }
			</style>
			');
			
			ee()->session->set_cache('wb_category_select', 'cp_assets_set', true);
		}

		if ($cell)
		{
			ee()->cp->add_to_foot('
			<script type="text/javascript">
				(function($) {
					Matrix.bind("wb_category_select", "display", function(cell){ $(this).find("select").multi(); });	
				})(jQuery);
			</script>
			');
		}
		else
		{
			ee()->cp->add_to_foot('
			<script type="text/javascript">
				(function($) {
					$("#'.$field_id.'.wb_multi_select").multi();
				})(jQuery);
			</script>
			');
		}

	}
	
	function update($current = '')
	{
		if($current == $this->info['version'])
		{
			return FALSE;
		}
		return TRUE;
	}

}
