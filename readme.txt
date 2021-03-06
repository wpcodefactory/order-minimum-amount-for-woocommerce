=== Order Minimum/Maximum Amount for WooCommerce ===
Contributors: wpcodefactory
Tags: woocommerce, order minimum amount, order maximum amount
Requires at least: 4.4
Tested up to: 5.7
Stable tag: 4.0.4
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Set required minimum and maximum order amounts in WooCommerce.

== Description ==

**Order Minimum/Maximum Amount for WooCommerce** plugin lets you set required minimum and/or maximum amounts (e.g. sum, quantity, weight, volume, etc.) for orders in WooCommerce.

= Main Features =

With our plugin you can set these **minimum** and/or **maximum** order amounts (you can enable multiple amount types at once):

* order **sum**,
* order **quantity**,
* order **weight**,
* order **volume**,
* order **length**,
* order **width**,
* order **height**,
* order **area** (i.e. length x width),
* order **products** (i.e. number of different products),
* order **product categories** (i.e. number of different product categories),
* order **product tags** (i.e. number of different product tags).

= More Features =

* Optionally set different order amounts on **per user role** basis.
* Display (and customize) customer **messages** on **cart** and **checkout** pages.
* Select if you want to **exclude taxes**, **shipping**, **discounts** and/or **fees** when calculating cart total **sum**.
* Optionally **block the checkout page** for customers when amount requirements are not met.
* For maximum amounts: optionally **validate** amounts immediately on **add to cart**, or completely **hide "add to cart" button** for products with exceeded amounts.
* And more...

= Premium Version =

With [premium version](https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/) you can additionally:

* Set different order amounts on **per user** basis.
* Set amounts **per product** (including variations), **per product category** and/or **per product tag**.
* Set amounts **per shipping method** or **per shipping zone**.
* Set amounts **per payment gateway**.
* Set amounts **per membership**.
* Set amounts **by currency**.
* **Skip** min/max amount checks if **selected coupons** were applied.
* **Skip** min/max amount checks if there are **selected products** (including variations) in cart.
* Include/exclude selected products (including variations) when calculating **cart total** for the amount checks.
* Display messages anywhere on your site with a **shortcode**.

= Feedback =

* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* [Visit plugin site](https://wpfactory.com/item/order-minimum-maximum-amount-for-woocommerce/).

== Installation ==

1. Upload the entire plugin folder to the `/wp-content/plugins/` directory.
2. Activate the plugin through the "Plugins" menu in WordPress.
3. Start by visiting plugin settings at "WooCommerce > Settings > Order Min/Max Amount".

== Screenshots ==

1. Frontend example – Cart notice.

== Changelog ==

= 4.0.4 - 04/05/2021 =
* Fix - General - "Maximum" Amount Limit - Hide "add to cart" button - PHP Notice: Undefined index on shop pages.
* Dev - Messages - Add mini-cart notices option.
* Dev - Messages - Replace additional positions by positions.
* Dev - Refactor code creating a new class only for messages.

= 4.0.3 - 20/04/2021 =
* Fix - Messages - Show product page messages from "Display on empty cart" option.
* Dev - Coupons - Add "Exclude all" option allowing to not check min/max amounts if any coupons have been applied.
* Dev - Messages - Add "Smart product scope" option allowing to Show only product scope messages relevant to the current product.
* WC tested up to: 5.2.

= 4.0.2 - 18/03/2021 =
* Fix - Messages - Product page notice gets displayed even when it's disabled.

= 4.0.1 - 17/03/2021 =
* Dev - Settings - Descriptions updated.
* Dev - Settings - Improve notes style.
* Dev - General - Create "Add user metas to the REST API" option.
* Dev - Messages - Add product page notices option.
* Dev - Messages - Add "Display on empty cart" option.
* Tested up to: 5.7.

= 4.0.0 - 13/02/2021 =
* Fix - Messages - "Per product / category / tag > Payment Gateways / Shipping" messages removed.
* Fix - Messages - "Payment Gateways" messages are now properly marked in admin settings.
* Fix - Cart Total - Sum - Was incorrectly calculating total instead of subtotal when "Order Sum Options > Order sum" option was set to "Order subtotal". This is fixed now.
* Fix - Cart Total - Sum - Was ignoring "Order Sum Options > Exclude" options. This is fixed now.
* Dev - General - Amount types - "Products", "Product categories" and "Product tags" options added.
* Dev - General - Amount types - "Length", "Width", "Height" and "Area" options added.
* Dev - Messages - Payment gateways placeholders are now loaded only if "Payment Gateways" section is enabled.
* Dev - Messages - Shipping placeholders are now loaded only if "Shipping" section is enabled.
* Dev - Messages - Advanced Options - "Remove old notices" option added.
* Dev - Products - Per product - "List variations" option added.
* Dev - Shipping - "Hide unavailable" option added.
* Dev - Shipping - "WooCommerce Table Rate Shipping" (by "JEM Plugins") plugin compatibility added.
* Dev - Shipping - "WooCommerce Table Rate Shipping" (by "wpWax") plugin compatibility added.
* Dev - Shipping - "Advanced: Special cases" option added.
* Dev - Payment Gateways - "Hide unavailable" option added.
* Dev - "Coupons" section added.
* Dev - Cart Products - "List variations" option added.
* Dev - Cart Total - "List variations" option added.
* Dev - "Advanced: Priority Options" subsections added to the "User Roles", "Users", "Shipping", "Payment Gateways" and "Memberships" sections.
* Dev - Sum - Changed the way order sum (total and subtotal) is calculated: manually summing line values now (instead of using `WC()->cart->get_subtotal()`, `WC()->cart->get_total( 'edit' )`, etc.).
* Dev - Sum - Now loading shipping script (i.e. update cart on updated shipping method) for "sum + cart notices + include shipping" condition.
* Dev - Shortcodes - `[alg_wc_order_min_max_amount]` - Checking if `scope` is enabled (in case if there is `scope` shortcode attribute set).
* Dev - Settings - `get_products_options()` - Current option values are now added to the list. Used in "Cart Products" and "Cart Total" sections.
* Dev - Settings - Restyled and descriptions updated.
* Dev - Major code refactoring.
* WC tested up to: 5.0.
* Plugin author updated.

= 3.4.1 - 12/01/2021 =
* Dev - Memberships - `get_user_memberships()` function updated (now checking for the `plan` slug as well).
* Dev - Localisation - `load_plugin_textdomain` moved to the `init` action.
* Dev - Code refactoring.
* WC tested up to: 4.9.

= 3.4.0 - 30/12/2020 =
* Fix - Shortcodes - `[alg_wc_oma_translate]` - Moved to the free version.
* Dev - Advanced - "Validate on add to cart" option added.
* Dev - Advanced - 'Hide "add to cart" button' options added.
* Dev - "Payment Gateways" section added.
* Dev - "Memberships" section added.
* Dev - Settings - Shipping - Notes updated.
* Dev - Settings - "Scope Options" moved to a separate "Products" settings section.
* Dev - Settings - "Products" section renamed to "Cart Products".
* Dev - Settings - Restyled.
* Dev - Code refactoring.
* Tested up to: 5.6.
* WC tested up to: 4.8.

= 3.3.0 - 21/11/2020 =
* Fix - Shortcodes - `[alg_wc_order_min_max_amount]` - `%product_title%` and `%term_title%` placeholders are now processed in shortcode content.
* Fix - Developers - Amount types - `format()` - Properly applying the `alg_wc_oma_amount_format` filter now.
* Dev - General - "Require all types" option added.
* Dev - Scope Options - `get_notices_per_product()` - Now grouping notices by scope (instead of by product).
* Dev - Messages - `%shipping_method%`, `%shipping_zone%` and `%shipping_zone_locations%` placeholders added.
* Dev - Messages - Advanced - "Format amounts" options added.
* Dev - Shipping - "Shipping messages" option added.
* Dev - Shipping - "Table Rate for WooCommerce by Flexible Shipping" plugin compatibility added.
* Dev - Shipping - Returning full method ID when retrieving current shipping method as a fallback (e.g. when using old shipping methods without the instance parameter).
* Dev - Products - "Validate all products" option added.
* Dev - "Cart Total" section added.
* Dev - Developers - Amount types - `format()` - `$value` param added to the `alg_wc_oma_amount_format` filter.
* Dev - Developers - `alg_wc_oma_get_notices` filter added.
* Dev - Allowing compares with zero cart total.
* Dev - Admin settings restyled.
* Dev - Code refactoring.
* WC tested up to: 4.7.

= 3.2.2 - 06/11/2020 =
* Fix - Core - `get_default_message()` function produced "fatal error". This is fixed now.
* Fix - Settings per Item - "Nonce verification failed" bug fixed.

= 3.2.1 - 03/11/2020 =
* Dev - General - Advanced - "Block checkout process" option added (defaults to `yes`).

= 3.2.0 - 20/10/2020 =
* Dev - General - "Per product", "Per product category" and "Per product tag" options added.
* Dev - Messages - All options are available in free version now.
* Dev - Messages - Admin settings descriptions updated.
* Dev - User Roles - "Enabled user roles" option added.
* Dev - User Roles - All options are available in free version now.
* Dev - User Roles - Admin settings restyled ("Save all changes for all roles") button removed.
* Dev - Users - Allowing negative amounts now. This is equivalent to "no min/max amount".
* Dev - "Shipping" section added.
* Dev - Currencies - Admin settings restyled ("Save all changes for all currencies") button removed.
* Dev - Code refactoring.
* WC tested up to: 4.6.

= 3.1.2 - 22/09/2020 =
* Dev - General - Order Sum Options - "Order sum" option added. Available values: "Order total" (default) and "Order subtotal".

= 3.1.1 - 17/09/2020 =
* Dev - General - Order Sum Options - "Exclude taxes" option added.
* Dev - General - Order Sum Options - "Exclude fees" option added.
* Dev - Core - `is_equal()` - Epsilon value is always equal to the amount step now.
* Dev - Core - `check_min_max_amount()` - Applying `floatval()` to all arguments now.

= 3.1.0 - 16/09/2020 =
* Dev - "Currencies" section added.
* Dev - "Products" section added.
* Dev - Allowing negative amounts now. This is equivalent to "no min/max amount".
* Dev - Code refactoring.
* Tested up to: 5.5.
* WC tested up to: 4.5.

= 3.0.0 - 04/08/2020 =
* Dev - General - "Limits" (i.e. "Minimum", "Maximum") option added.
* Dev - General - "Types" option added. It's now also possible to set min/max order "Weight" and "Volume" amounts.
* Dev - Messages - Default messages updated.
* Dev - Messages - New placeholders added: `%amount%`, `%total%`, `%diff%`, `%amount_raw%`, `%total_raw%`, `%diff_raw%`. Old placeholders are now deprecated.
* Dev - Messages - Additional Positions - Now displaying multiple notices when applicable (instead of only the first one).
* Dev - Comparing values with epsilon now.
* Dev - Admin settings restyled; "Amounts" section added.
* Dev - Shortcodes - `[alg_wc_order_min_max_amount]` - Shortcodes are now processed in content.
* Dev - Shortcodes - `[alg_wc_order_min_max_amount]` - Optional `on_empty` attribute added.
* Dev - Developers - `alg_wc_oma_placeholders` filter added.
* Dev - Developers - `alg_wc_oma_version_updated` action added.
* Dev - Developers - Amount types - `alg_wc_oma_amount_types`, `alg_wc_oma_amount_cart_total`, `alg_wc_oma_amount_title`, `alg_wc_oma_amount_unit` and `alg_wc_oma_amount_format` filters added.
* Dev - Major code refactoring.
* Description in readme.txt updated.

= 2.2.3 - 18/07/2020 =
* Dev - `alg_wc_oma_check_order_min_max_amount` filter added.
* Dev - `alg_wc_oma_block_checkout` filter added.

= 2.2.2 - 17/07/2020 =
* Dev - `get_cart_total()` - Using `WC()->cart->get_total( 'edit' )` function now (instead of `WC()->cart->total`).
* WC tested up to: 4.3.

= 2.2.1 - 27/05/2020 =
* Dev - Localization - Messages added to the translation file.
* WC tested up to: 4.1.

= 2.2.0 - 14/04/2020 =
* Dev - Messages - "Additional Positions" options added.
* Dev - `[alg_wc_order_min_max_amount]` shortcode added.
* Dev - Code refactoring.
* Dev - Admin "reset settings" notice updated.
* Dev - Admin settings descriptions updated.
* WC tested up to: 4.0.
* Tested up to: 5.4.
* Plugin URI updated.

= 2.1.0 - 30/10/2019 =
* Fix - General - Exclude shipping - Shipping tax function fixed.
* Dev - "Order Min/Max Amount per User" options added.
* Dev - User Roles - Enable section - Defaults to `no` now.
* Dev - Admin settings restyled.
* Dev - Code refactoring.
* WC tested up to: 3.7.

= 2.0.0 - 30/07/2019 =
* Dev - "Order **Maximum** Sum/Quantity" options added.
* Dev - "Order Minimum **Quantity**" options added.
* Dev - Messages - `%min_order_sum_diff%` and `%min_order_qty_diff%` placeholders added.
* Dev - Messages - Placeholders replaced: `%minimum_order_amount%` with `%min_order_sum%` and `%cart_total%` with `%cart_total_sum%`.
* Dev - User Roles - "Enable section" option added (defaults to `yes`).
* Dev - User Roles - Roles settings are stored in array now.
* Dev - User Roles - "Customer" role moved to the top of the list.
* Dev - Step in settings increased to `0.000001`.
* Dev - Major code refactoring.

= 1.2.1 - 25/07/2019 =
* Dev - Messages - Shortcodes are now processed in cart and checkout messages; `[alg_wc_oma_translate]` shortcode added for WPML/Polylang translations.
* Dev - Admin settings - Descriptions updated; "Your settings have been reset" notice added.
* Tested up to: 5.2.
* WC tested up to: 3.6.

= 1.2.0 - 30/10/2018 =
* Fix - "get_cart_url is deprecated" notice fixed.
* Dev - "Exclude discounts" option added.
* Dev - "Notice type on checkout page" and "Notice type on cart page" options added.
* Dev - Now checking all user roles instead of first one only.
* Dev - "Raw" values are now allowed in messages.
* Dev - Amount step decreased in admin settings.
* Dev - Admin settings sections restyled and descriptions updated.
* Dev - Code refactoring.
* Dev - Plugin URI updated.

= 1.1.0 - 24/07/2017 =
* Dev - Autoloading plugin options.
* Dev - `exit` added after `wp_safe_redirect()`.
* Dev - Plugin URI updated.
* Dev - Plugin header ("Text Domain" etc.) updated.

= 1.0.1 - 08/02/2017 =
* Dev - Language (POT) file added.
* Fix - Link fixed in User Role settings.

= 1.0.0 - 04/02/2017 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.
