WB Category Select
==================

Select (dropdown or multiselect) field that shows a list of pre-defined category groups and the categories within them


Install
-------

1. Download the repository
2. Move third\_party/wb\_category\_select to expressionengine/third\_party


Field Options
-------------

- Category Groups - Select the field groups you want to show up in the drop down field
- Allow multiple selections? - Changes the field from a dropdown to a multiselect
	- When using multiple selections, you can either use a tag pair and use `{category_id}` within that or just use a single tag to get a pipe delimited list of the category IDs


Change Log
----------

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
