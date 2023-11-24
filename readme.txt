=== Order Minimum/Maximum Amount for WooCommerce ===
Contributors: wpcodefactory, omardabbas, karzin, anbinder, algoritmika, kousikmukherjeeli
Tags: woocommerce, order minimum amount, order maximum amount
Requires at least: 4.4
Tested up to: 6.4
Stable tag: 4.3.8
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Set required minimum and maximum order amounts in WooCommerce.

== Description ==

**Order Minimum/Maximum Amount for WooCommerce** plugin lets you set required minimum and/or maximum amounts (e.g. sum, quantity, weight, volume, etc.) for orders in WooCommerce.

### &#9989; Main Features ###

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

### More Features ###

* Optionally set different order amounts on **per user role** basis.
* Display (and customize) customer **messages** on **cart** and **checkout** pages.
* Select if you want to **exclude taxes**, **shipping**, **discounts** and/or **fees** when calculating cart total **sum**.
* Optionally **block the checkout page** for customers when amount requirements are not met.
* For maximum amounts: optionally **validate** amounts immediately on **add to cart**, or completely **hide "add to cart" button** for products with exceeded amounts.
* And more...

### &#127942; Premium Version ###

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
* Set a default/minimum/maximum parameter from the **quantity input** based on the quantity amount type.

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

= 4.3.8 - 24/11/2023 =
* Dev - Compatibility - Add compatibility option with PayPal for WooCommerce plugin to disable the PayPal buttons.
* WC tested up to: 8.3.
* Tested up to: 6.4.

= 4.3.7 - 02/11/2023 =
* Fix - Priority replaces product tag values. Remove it.

= 4.3.6 - 30/10/2023 =
* Dev - General - Block checkout process - New option: Checkout hook.

= 4.3.5 - 18/10/2023 =
* Dev - User roles - New option: Get user roles method.
* WC tested up to: 8.2.

= 4.3.4 - 26/09/2023 =
* WC tested up to: 8.1
* Update plugin icon, banner.

= 4.3.3 - 04/09/2023 =
* Dev - General > Checkout options > Block store api request

= 4.3.2 - 20/08/2023 =
* Fix - Counting all unique product categories in the cart products to restrict order by category count.
* Dev - Messages - New option: Display one message for each limit problem.
* Dev - Checkout options - Block checkout process using REST API.
* Dev - Checkout options - New function `get_cart_total` for REST API.
* Dev - HPOS compatibility.
* WC tested up to: 8.0.
* Tested up to: 6.3.

= 4.3.1 - 09/07/2023 =
* new setting General - Include WC Subscription recurring amount to cart total.

= 4.3.0 - 20/06/2023 =
* WC tested up to: 7.8
* Tested up to: 6.2.

= 4.2.9 - 20/03/2023 =
* Fix plugin name.

= 4.2.8 - 16/03/2023 =
* Fix - Typo in Russian translation.
* WC tested up to: 7.5.

= 4.2.7 - 15/02/2023 =
* Dev - Currencies - New option to calculate currency value by exchange rates.
* Dev - Compatibility - Get exchange rates from the Currency Switcher plugin by WP Wham.
* Dev - Improve the way of initializing the main class.
* WC tested up to: 7.4.

= 4.2.6 - 02/02/2023 =
* Add Russian translation.
* WC tested up to: 7.3.

= 4.2.5 - 05/01/2023 =
* Dev - Messages - Cart - Add "Blocks cart enqueue data" as a way of showing the notice on Cart block.
* Dev - Messages - Checkout - Add "Blocks checkout enqueue data" as a way of showing the notice on Checkout block.
* Dev - General - Checkout options - Block checkout process will now work on Checkout block.
* WC tested up to: 7.2.

= 4.2.4 - 11/11/2022 =
* Fix - General - Cart options - Proceed to checkout button - Disable is not working consistently.

= 4.2.3 - 09/11/2022 =
* Fix - General - Cart options - Proceed to checkout button - Disable is not working consistently.
* Tested up to: 6.1.
* WC tested up to: 7.1.

= 4.2.2 - 15/09/2022 =
* Dev - Improve compatibility with the Avada theme.
* WC tested up to: 6.9.

= 4.2.1 - 12/09/2022 =
* Fix - General - Cart options - Improve "Proceed to checkout buttons" checks.

= 4.2.0 - 16/08/2022 =
* Fix - PHP Warning: Undefined array key "line_subtotal".
* Fix - General - "Maximum" amount limit options - Validate on add to cart.
* Fix - General - "Maximum" amount limit options - Hide "add to cart" button.
* Fix - General - "Quantity" amount type options - Min and Max attributes should reflect when changing between variations.
* Dev - General - "Maximum" amount limit options - Improve hiding on variations.
* WC tested up to: 6.8.

= 4.1.9 - 21/07/2022 =
* Dev - Compatibility - WooCommerce Subscriptions - Add new option to skip min/max amount checks if the user has a switching subscription item in cart.
* WC tested up to: 6.7.

= 4.1.8 - 14/06/2022 =
* Fix - Products - Per product - Error: Cannot access protected property `Alg_WC_OMA_Pro_Products::$do_list_variations`.
* WC tested up to: 6.5.

= 4.1.7 - 06/06/2022 =
* Fix - Products - Per product category/tag is not working properly.
* Fix - PHP Warning: Undefined property: `Alg_WC_OMA_Pro_Products::$do_list_variations`.
* Dev - Memberships - Add compatibility with the SUMO Memberships plugin.
* Dev - Add `user_bought_term_id` param to `alg_wc_oma_amount` shortcode with the possibility of using multiple ids with commas.
* Dev - Add `check_parent_bought_term_id` param to `alg_wc_oma_amount` shortcode. Default is `false`.
* Tested up to: 6.0.

= 4.1.6 - 20/05/2022 =
* Fix - Amounts - Shortcodes are not changing the input type from number to text in all sections.
* Fix - Products - Per product - If "List variations" is disabled the limit set on a variable product should not consider the variations.
* Dev - Currencies - New option: "Using -1 in amounts from other sections will prevent this section from overriding them".
* WC tested up to: 6.5.

= 4.1.5 - 10/05/2022 =
* Fix - General - Checkout options - "Block checkout page" may redirect to cart if paying via "Order pay page".
* WC tested up to: 6.4.

= 4.1.4 - 08/04/2022 =
* Dev - General - Cart options - Improve the disabled button style.
* Dev - Amounts - Create option to allow adding shortcodes on the amounts.
* Dev - Amounts - Create the `[alg_wc_oma_amount]` shortcode.
* Dev - Create `alg_wc_oma_amount_input_type` filter.
* Dev - Shortcode deprecated. From `[alg_wc_order_min_max_amount]` to `[alg_wc_oma_amount_msg]`.

= 4.1.3 - 20/03/2022 =
* Fix - Messages and shortcodes work with "Enable plugin" option disabled.
* WC tested up to: 6.3.

= 4.1.2 - 04/03/2022 =
* Fix - Currencies - Call to undefined method Alg_WC_OMA_Pro_Currencies::get_value_per_product().
* Dev - General - Cart options - Proceed to checkout button - Change the method used to disable the button.
* Dev - Messages - Checkout - Improve "Force refresh" option.
* Dev - Messages - Checkout - Force refresh - Add option to choose the hook used to update the notice.

= 4.1.1 - 23/02/2022 =
* Fix - General - Checkout options - "Block checkout page" redirects to cart even on successful purchases.
* Fix - Products - Per product category - Doesn't work well sometimes with multiple categories at once.
* Fix - Call to a member function get_cart () on null in `Alg_WC_OMA_Core`.
* Dev - Create new section "Fees" to add individual fee for each limit reached.
* Dev - Now `Alg_WC_OMA_Messages::get_notices()` return results in array format.
* Dev - General - Cart Page - Added option to disable "proceed to checkout" button.
* Tested up to: 5.9.
* WC tested up to: 6.2.

= 4.1.0 - 20/01/2022 =
* Dev - Shipping - Special cases - Create "Identification" option.
* Dev - Shipping - Special cases - Create "Comparison method" option.
* Dev - Shipping - Special cases - Create option that will try to autodetect Shipping IDs.
* Dev - General - Sum amount type - Create "Rounding" option.
* Dev - General - Sum amount type - Create "Rounding" precision option.
* WC tested up to: 6.1.

= 4.0.9 - 02/12/2021 =
* Dev - General - Login requirement - Add "Login requirement" option.
* Dev - General - Login requirement - Add "Notice" option
* Dev - General - Login requirement - Add "Notice type" option.
* Dev - General - Login requirement - Add "Display condition" option.

= 4.0.8 - 02/12/2021 =
* Dev - Add `alg_wc_oma_get_cart_value` filter.
* Dev - Compatibility - Product Bundles - Add option to include bundled cart item on cart total calculation.
* Dev - Compatibility - Product Bundles - Add option to create a "bundle price" amount type.
* Dev - Create compatibility section.
* Dev - `alg_wc_oma_get_cart_total_do_count_product` now passes cart item as second parameter.
* Fix - Call to a member function get_cart () on null in `Alg_WC_OMA_Core`.
* WC tested up to: 5.9.

= 4.0.7 - 30/09/2021 =
* Dev - Messages - Product page - Add `woocommerce_before_single_product` position.
* Dev - Improve `is_plugin_active` function.
* Improve readme.
* WC tested up to: 5.7.

= 4.0.6 - 01/09/2021 =
* Fix - General - REST API - Check user metas option.
* Fix - General - Quantity - Default quantity based on "Per product" minimum limits doesn't work correctly on cart.
* Fix - General - Quantity - Fix possible PHP Warning if default quantity is enabled and "Per product" option is disabled.
* Fix - General - Quantity - Min and Max parameters don't work on cart.
* WC tested up to: 5.6.

= 4.0.5 - 02/08/2021 =
* Fix - Free and pro plugins can't be active at the same time.
* Dev - General - Quantity - Add quantity input option to set default quantity based on "Per product" minimum limits.
* Dev - General - Quantity - Add quantity input option to set minimum parameter based on "Per product" minimum limits.
* Dev - General - Quantity - Add quantity input option to set maximum parameter based on "Per product" maximum limits.
* Dev - General - Quantity - Add add to cart button option to set quantity on loop pages based on "Per product" minimum limits.
* Dev - Messages - Checkout - Add "Force refresh" option.
* Dev - Add promoting notice.
* WC tested up to: 5.5.
* Tested up to: 5.8.
* Add github deploy setup.

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