WB Category Select
==================

Select (dropdown or multiselect) field that shows a list of pre-defined category
groups and the categories within them.

Usage
-----

### Single Variable

When displaying the field on the front end, you can either use a single
variable:

  {category_select}

Which will either display a single category ID or a piped list (e.g. `1|3|17`)
depending on whether you allow for multiple selections.

### Variable Pair

You can also use a variable pair:

  {category_select}
    <option val="{category_id}">{category_name} (<code>{category_url_title}</code>)</option>
  {/category_select}

You have access to several variables inside the variable pair:

- `{category_id}`
- `{category_site_id}`
- `{category_group_id}`
- `{category_parent_id}`
- `{category_name}`
- `{category_url_title}`
- `{category_description}`
- `{category_image}`
- `{category_order}`

You also have access to one parameter:

- backspace

Install
-------

1. Download the repository
2. Move third\_party/wb\_category\_select to expressionengine/third\_party (EE2) or system/users/addons (EE3)


Field Options
-------------

- Category Groups - Select the field groups you want to show up in the drop down
  field
- Allow multiple selections? - Changes the field from a dropdown to a
  multiselect
  - When using multiple selections, you can either use a tag pair and use
    `{category_id}` within that or just use a single tag to get a pipe delimited
    list of the category IDs


Change Log
----------

- 2.0 (Onstuimig)
  - Added EE3 support
  - Added Grid and Bloqs compatibility
- 1.6
  - Added support for category description, category order, category parent ID, category group ID, and category site ID
  - Prevent PHP notice when category select has no value
- 1.5
  - Added support for category image
  - Removed old backspace code in lieu of built-in backspace support
- 1.4
  - Category ID, name, and url_title are avaiable for both multi selections
    and single selections
- 1.3.5
  - Category lists can show only parents
  - Added Matrix cell validation
  - Fixed a deprecated warning for the field type constructor
- 1.3.4
  - Fixed a bug where a warning would appear with Better Workflow.
- 1.3.3
  - Loosened up the $tagdata check when using the tag
- 1.3.2
  - Removed unneeded constructor that was also causing deprecation notices
- 1.3
  - Category lists now show when a category is nested
- 1.2
  - Added option for multiple categories select (thanks to Brandon Kelly)
- 1.1.5
  - Added check for invalid category groups
  - Sorting the category name list on a per category group basis
- 1.1.4
  - Removed duplicated code from fieldtype
- 1.1.3
  - Fixed a settings problem with the Matrix Cell
- 1.1.2
  - Rewrote display field function to cut down on duplication
- 1.1.1
  - Fixed a problem with Matrix compatibility
- 1.1
  - Added Matrix compatibility
- 1.0.1
  - Fixed a warning when no previous settings existed for the field
- 1.0.0
  - Initial Version of WB Category Select
