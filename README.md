ACF QuickEdit Fields
====================

WordPress plugin which extends the funtionality of the Advanced Custom Fields Plugin (Pro, Version 5+).
http://www.advancedcustomfields.com/pro/

Show Advanced Custom Fields in post list table.
Edit field values in Quick Edit and / or Bulk edit.

Proofed to work with ACF Pro 5.x.
It will not work with ACF Free (version 4.x).

**Supported fields:**

| Field Type       | Column display | Quick Edit  | Bulk Edit   |
|------------------|----------------|-------------|-------------|
| *Basic*          |                |             |             |
| Text             | Yes            | Yes         | Yes         |
| Textarea         | Yes            | Yes         | Yes         |
| Number           | Yes            | Yes         | Yes         |
| Email            | Yes            | Yes         | Yes         |
| URL              | Yes            | Yes         | Yes         |
| Password         | Yes (1)        | Yes         | No          |
| *Content*        |                |             |             |
| WYSIWYG          | No             | No          | No          |
| oEmbed           | No             | No          | No          |
| Image            | Yes            | No          | No          |
| File             | Yes            | No          | No          |
| Gallery          | No             | No          | No          |
| *Choice*         |                |             |             |
| Select           | Yes            | Yes         | Yes         |
| Checkbox         | Yes            | Yes         | Yes         |
| Radio            | Yes            | Yes         | Yes         |
| True/False       | Yes            | Yes         | Yes         |
| *Relational*     |                |             |             |
| Post Object      | Yes            | No          | No          |
| Page Link        | Yes            | No          | No          |
| Relationship     | Yes            | No          | No          |
| Taxonomy         | No             | No          | No          |
| User             | No             | No          | No          |
| *jQuery*         |                |             |             |
| Google Map       | No             | No          | No          |
| Date Picker      | Yes            | Yes         | Yes         |
| Date Time Picker | ?              | ?           | ?           |
| Time Picker      | ?              | ?           | ?           |
| Color Picker     | Yes            | Yes         | Yes         |
| *jQuery*         |                |             |             |
| Message          | No             | No          | No          |
| Tab              | No             | No          | No          |
| Repeater         | No             | No          | No          |
| Flexible Content | No             | No          | No          |
| Clone            | No             | No          | No          |

(1) Password will show as placeholder.

Contributing
------------
If you encounter an issue or wish for a certain feature, please do not ask me to 
fix it for you. I will not, but I would really appreciate when you fix it yourself 
and send me a pull request.



ToDo:
-----
 - [x] Load main class only in admin
 - [ ] Code Docs
