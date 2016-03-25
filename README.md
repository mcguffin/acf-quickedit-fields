ACF QuickEdit Fields
====================

WordPress plugin which extends the funtionality of the Advanced Custom Fields Plugin (Pro, Version 5+).
http://www.advancedcustomfields.com/pro/

Show Advanced Custom Fields in post list table.
Edit field values in Quick Edit and / or Bulk edit.

Proofed to work with ACF Pro 5.x.
Will cause a fatal error when used with ACF Free (version 4.x).

**Supported fields:**

| Field Type   | Column display | Quick Edit | Bulk Edit |
|--------------|----------------|------------|-----------|
| Text         | Yes            | Yes        | Yes       |
| Textarea     | Yes            | Yes        | Yes       |
| Number       | Yes            | Yes        | Yes       |
| Email        | Yes            | Yes        | Yes       |
| URL          | Yes            | Yes        | Yes       |
| WYSIWYG      |                |            |           |
| oEmbed       |                |            |           |
| Image        | Yes            |            |           |
| File         | Yes            |            |           |
| Gallery      |                |            |           |
| Select       | Yes            | Yes        | Yes       |
| Checkbox     | Yes            | Yes        | Yes       |
| Radio        | Yes            | Yes        | Yes       |
| True/False   | Yes            | Yes        | Yes       |
| Post Object  |                |            |           |
| Page Link    |                |            |           |
| Relationship |                |            |           |
| Taxonomy     |                |            |           |
| User         |                |            |           |
| Google Map   |                |            |           |
| Datepicker   | Yes            | Yes        | Yes       |
| Colorpicker  | Yes            | Yes        | Yes       |
| Message      |                |            |           |
| Tab          |                |            |           |
| Repeater     |                |            |           |
| Flexible     |                |            |           |


Developers note
---------------
This plugin is still a stub. I am not quite sure if I will continue developing it.
If you encounter an issue or wish for a certain feature, please do not ask me to 
fix it for you. I will not, but I would really appreciate when you fix it youself 
and send me a pull request.

ToDo:
-----
 - [ ] Load main class only in admin
 - [ ] Code Docs
 - [x] Plugin localization
 - [x] Nice display for `color_picker` and `date_picker` fields
 - [x] QuickEdit: 
 	- [x] Select: does not work with multiple values
    - [x] Select: support multiple
    - [x] Date: use datepicker
    - [x] Color: use colorpicker
    - [x] Checkbox
    - [x] Radio
    - [x] Number: use `type="number"`
 