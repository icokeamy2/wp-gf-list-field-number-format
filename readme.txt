=== List Field Number Format for Gravity Forms ===
Contributors: ovann86
Donate link: http://www.itsupportguides.com/
Tags: Gravity Forms, forms, online forms, select, list, numbers, math
Requires at least: 4.5.3
Tested up to: 4.7.0
Stable tag: 1.2.0
License: GPLv2
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Turn your list field columns into repeatable number fields

== Description ==

> This plugin is an add-on for the <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">Gravity Forms</a> (affiliate link) plugin. If you don't yet own a license for Gravity Forms - <a href="https://www.e-junkie.com/ecom/gb.php?cl=54585&c=ib&aff=299380" target="_blank">buy one now</a>! (affiliate link)

**What does this plugin do?**

* make a list field column accept only numbers
* specify the format of the number including currency, comma delimited (9,999) and dot delimited (9.999)
* specify the rounding type - round up, round down or round closest
* specify the decimal places to round to - no rounding, 0, 1, 2, 3, 4, 5
* force the number to use fixed placed notation - e.g. 10.1 would become 10.10 with 2 place fixed notation
* add a column total - automatically display the total of the column
* specify a range requirement - this can be a set number, e.g. 200 or a formula, e.g. column 1 + column 2
* calculate column values uing a formula (e.g. field = column 1 + column 2)
* compatible with <a href="https://github.com/richardW8k/RWListFieldCalculations/blob/master/RWListFieldCalculations.php">Gravity Forms List Field Calculations Add-On</a>
* compatible with Gravity PDF

> See a demo of this plugin at [demo.itsupportguides.com/list-field-number-format-for-gravity-forms/](http://demo.itsupportguides.com/list-field-number-format-for-gravity-forms/ "demo website")

**How to I use the plugin?**

Simply install and activate the plugin - no configuration required.

Open your Gravity Form, edit a 'List' field and use the 'Number Format' options to configure the columns.

**Have a suggestion, comment or request?**

Please leave a detailed message on the support tab. 

**Let me know what you think**

Please take the time to review the plugin. Your feedback is important and will help me understand the value of this plugin.

**Disclaimer**

*Gravity Forms is a trademark of Rocketgenius, Inc.*

*This plugins is provided “as is” without warranty of any kind, expressed or implied. The author shall not be liable for any damages, including but not limited to, direct, indirect, special, incidental or consequential damages or losses that occur out of the use or inability to use the plugin.*

*Note: When Gravity Forms isn't installed and you activate Gravity PDF we display a notice that includes an affiliate link to their website.*

== Installation ==

1. Install plugin from WordPress administration or upload folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in the WordPress administration
1. Open the Gravity Forms 'Forms' menu
1. Open the forms editor for the form you want to change
1. Add or open an existing list field
1. With multiple columns enabled you will see a 'Number Format' section - here you can choose which columns are number fields.

== Screenshots ==

1. Shows the number format options in the forms editor.

== Changelog ==

= 1.2.0 =
* Feature: allow calculations to be made from standard Gravity Forms number fields in addition to list field column fields
* Fix: resolve issue that stopped negative values from being used, for example -50 would be treated as 50
* Maintenance: improve support for 'ABN Lookup for Gravity Forms' plugin

= 1.1.8 =
* Maintenance: improve support for 'Sortable List Fields for Gravity Forms' plugin

= 1.1.7 =
* Fix: JavaScript improvements.

= 1.1.6 =
* Fix: Improve JavaScript in entry editor.

= 1.1.5 =
* Fix: Change how field id is determined to improve compatibility with all versions of Gravity Forms.

= 1.1.4 =
* Fix: Resolve issue with row calculations based on french notation numbers (9.999,99).
* Fix: Resolve issue field format not correctly setting when new row is added.

= 1.1.3 =
* Fix: Improve support for tables inside of field descriptions.

= 1.1.2 =
* Fix: Resolve issue with range instructions appearing in total row/column if both range instructions (e.g. Number must be between x and x) and total column enabled.
* Fix: Resolve 'undefined variable' error when submitting a form that does not include a total column.

= 1.1.1 =
* Fix: Improve support for older versions of PHP (version 5.3) "Parse error: syntax error, unexpected ["

= 1.1.0 =
* Feature: calculate column values uing a formula (e.g. field = column 1 + column 2)
* Feature: display column total - column total is automatically calculated and displayed below
* Feature: set a range value for given column - this can be a specific value (e.g. 200) or a formula (e.g. column 1 * column 2)
* Feature: add client-side and server-side range validation
* Feature: ability to hide range instructions (e.g. 'must be more than 200')
* Feature: add client-side and server-side validation for number enabled fields - this enures submitted values are in the correct format
* Maintenance: increment minimum Gravity Forms version to 1.9.15
* Maintenance: add CSS classes to number enabled fields and inputs to help with applying CSS
* Maintenance: right-align text in number enabled inputs
* Maintenance: add warning message if Gravity Forms is not installed and enabled
* Maintenance: formatting and styling updates to options in form editor

= 1.0.1 =
* Fix: change short PHP open tags to full
* Fix: resolve issue with single number format field not updating options when number format changes

= 1.0 =
* First public release.