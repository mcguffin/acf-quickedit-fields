ACF QuickEdit Fields
====================

WordPress plugin which extends the funtionality of the Advanced Custom Fields Plugin (Pro, Version 5+).  
http://www.advancedcustomfields.com/pro/

Show Advanced Custom Fields in post list table.  
Edit field values in Quick Edit and / or Bulk edit.

Proofed to work with ACF Pro 5.x.  
It will not work with ACF Free (version 4.x).

See the [wiki](https://github.com/mcguffin/acf-quick-edit-fields/wiki) for a quick start and a list of [supported ACF Fields](https://github.com/mcguffin/acf-quick-edit-fields/wiki/Supported-ACF-Fields).

ToDo:
-----

 - [ ] Bulk: Add no-change option to all fields
 - [x] Add columns to Terms
 - [ ] Add quickedit to Terms
 	- Fix strange 403 ajax action = `inline-save`, post type = `post`
 	- Fix data retrieval - must send post id = `{$taxonomy}_{$term_id}`
 - [ ] Conditional:
 	- [ ] Current user role
 	- [x] Post Category / Term
 	- [x] Post Format
 	- [x] Post Status
 	