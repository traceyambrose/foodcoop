<?php
// SERVER AND SITE SETUP

                          // Version is used to keep you alerted to new versions and updates of the software
$current_version        = '1.5.3';

                          // Some common timezone options in the United States:
                          //    Pacific:  America/Los_Angeles
                          //    Mountain: America/Denver
                          //    Central:  America/Chicago
                          //    Eastern:  America/New_York
$local_time_zone        = 'New Zealand/Auckland';

                          // Base url is used for web references.  Do NOT include a trailing slash.
$site_url               = 'http://foodcoop.tt.craigambrose.com';

                          // Directory for the local food coop software on the website (typically /shop/)
$food_coop_store_path   = '/';

                          // Internal file path to document_root
$file_path              = '/home/ttnz/sites/foodcoop';

                          // Domain name -- used for email references
$domainname             = 'foodcoop.tt.craigambrose.com';

                          // Name of your organization -- used in some textual messages
$site_name              = 'Atamai Foods Inc';

                          // Contact information used for textual reference (HTML code should be okay here)
$site_contact_info      = 'Atamai Village Council PO Box 321 Motueka';

                          // Mailing address (HTML code should be okay here)
$site_mailing_address   = 'Atamai Village Council PO Box 321 Motueka';

                          // Directory for graphic files for the coop section of your website
$site_graphics          = '/grfx/';

                          // Filename of your favicon (in the root directory)
$favicon                = '/favicon.ico';

                          // Typical period of cycle -- for presetting prep functions
$days_per_cycle         = 28;

                          // End-of-order window for institutional buyers (seconds) Set $institution_window
                          // value high to allow institutional buyers all the time and set to zero to prevent
                          // any use. NOTE: 3600 * 24 = 1 day in seconds.
$institution_window     = (3600 * 24) * 0;

                          // Show actual price vs. show cooperative price (between the producer and customer price)
$show_actual_price      = false;

                          // If this is true, each new member_id will fill in missing values. Otherwise each
                          // will be sequentially higher according to the auto-increment value.
$fill_in_member_id      = true; 

                          // If this is true, producer contact information will be shown on the public pages
                          // otherwise set to false to only shown for logged-in members.
$prdcr_info_public       = true; 

                          // Set custom paging directives for htmldoc here
$htmldoc_paging         = '<!-- MEDIA DUPLEX NO --><!-- MEDIA TOP 0.3in --><!-- MEDIA BOTTOM 0.3in -->';


// EXTERNAL FILE SETUP

                          // Note: these pages must be under $site_url
                          // how to join the coop
$page_membership        = $food_coop_store_path.'join.php';

                          // membership standards
$page_terms_of_service  = '/terms_of_service.php';

                          // pickup and deilvery locations:
$page_locations         = $food_coop_store_path.'locations.php';

                          // list of producers in the coop
$page_coopproducers     = $food_coop_store_path.'coopproducers.php';

                          // path to invoices
$invoice_web_path       = $food_coop_store_path.'members/invoices/';
$invoice_file_path      = $file_path.$invoice_web_path;


// DATABASE SETUP

                          // Enter the db host
$db_host                = 'localhost';

                          // Enter the username for db access
$db_user                = 'foodcoop';

                          // Enter the password for db access
$db_pass                = 'bootlace';

                          // Enter the database name
$db_name                = 'foodcoop';

                          // This is probably blank
$db_prefix              = '';

                          // If you want to use a master password to access all member accounts
                          // enter the MD5 of master password as generated by mysql
$md5_master_password    = '';


// DISPLAY SETUP

                          // Configure this to reflect your desired routing code template: The following
                          // values will be auto-filled from like-named variables in the scripts used
                          // to create the routing templates.  For example, !BASKET_ID! is replaced
                          // with the contents of the $basket_id variable.
                          //
                          //   !BASKET_ID!       customer basket id
                          //   !MEMBER_ID!       member id number
                          //   !FIRST_NAME!      customer first name
                          //   !LAST_NAME!       customer last name
                          //   !SHOW_MEM!        customer name in "Last, First" format
                          //   !SHOW_MEM2!       customer_name in "First Last" format
                          //   !BUSINESS_NAME!   customer business name -- may not exist
                          //   !HUB!             the delivery hub
                          //   !TRUCK_CODE!      routing truck code
                          //   !DELCODE_ID!      delivery code id (the abbreviation)
                          //   !DELCODE!         delivery code (long form of name)
                          //   !DELTYPE!         delivery type (H:home, W:work, P:pickup)
                          //   !A_BUSINESS_NAME! producer business name
                          //   !PRODUCT_ID!      product numeric id
                          //   !PRODUCT_NAME!    full product name
                          //   !ITEM_PRICE!      item price per pricing-unit (not the total)
                          //   !ORDERING_UNIT!   units used for ordering
                          //   !QUANTITY!        quantity of ordering units that were ordered
                          //   !STORAGE_CODE!    product storage code (may not always apply)
$route_code_template =    '!HUB!-!MEMBER_ID!-!DELCODE_ID! !TRUCK_CODE! [!STORAGE_CODE!]';

                          // Font face used in various locations
$fontface =               'arial';

                          // Another font declaration used in other locations
$font =                   '<font size="-1" face="'.$fontface.'">';

                            // Some longer listings use this value for pagination
$default_results_per_page = 50;

                          // Percentage charged to producers
$producer_markdown =      0.10;

                          // Percentage charged to customers
$customer_markup =        0.10;

                          // Percentage charged to institutions
$institution_markup =     0.05;

                          // Change this if your organization is, i.e. a "partnership".  This is used in
                          // various textual places.  i.e. "Welcome to the ******"
$organization_type =      'cooperative';

                          // Use this to enable producer confirmation settings (NOT FULLY TESTED)
$req_prdcr_confirm =      false;

                          // Use this to control whether paypal fees are passed to customers.  Please note
                          // that it is of questionable legality to pass along paypal or credit-card fees.
                          // Also note that this ability will probably be deprecated in future versions so
                          // it is strongly suggested NOT to use this setting.  If paypal charges will not
                          // be passed on to customers, then set this value to zero.  To always use paypal
                          // surcharges, set this to a very large number -- like 1000000
$delivery_no_paypal =     0;

                          // Don't rely on this to be completely fool-proof, but it is a beginning.
$state_tax =              0.06;

                          // Show logo in the header?
$show_header_logo =       true;

                          // Show site name in the header?
$show_header_sitename =   false;

                          // Enable/disable pdf generation by htmldoc
$use_htmldoc =            true;

                          // 1: if new producers should be pending; 0: if new producers should have immediate access
$new_producer_pending =   '1';

                          // Possible values for calculating charges for items with random weights:
                          // ZERO : Use a zero charge for the items
                          // AVG  : Use an average cost for the two weights
                          // MAX  : Use maximum costs
                          // MIN :  Use minimum costs
                          // Does not affect DISPLAY (see customer_invoice_template
                          // Only affects calculations of totals/costs
$random_calc =            'ZERO';

                          // true or false if membership should be a taxable quantity
$membership_taxed =       false;

                          // Set according to whether the co-op fee is taxable.  Choose from:
                          // For everything that has a co-op fee:       'always'
                          // Only for things that are already taxed:    'on taxable items'
                          // The coop fee is never taxed for anything:  'never'
$coop_fee_taxed =         'on taxable items';


// CONTACT EMAIL SETUP

                          // Set up your site email addresses here.  The software uses all of these email aliases
                          // however you can point them all to just a few (or one) address if you desire.

$email_customer         = 'customer@'.$domainname;
$email_general          = 'info@'.$domainname;
$email_help             = 'help@'.$domainname;
$email_membership       = 'membership@'.$domainname;
$email_orders           = 'orders@'.$domainname;
$email_paypal           = 'paypal@'.$domainname;
$email_pricelist        = 'pricelist@'.$domainname;
$email_problems         = 'problems@'.$domainname;
$email_producer_care    = 'producer-care@'.$domainname;
$email_software         = 'software@'.$domainname;
$email_standards        = 'standards@'.$domainname;
$email_treasurer        = 'treasurer@'.$domainname;
$email_volunteer        = 'volunteer@'.$domainname;
$email_webmaster        = 'web@'.$domainname;

                          // The membership form will be sent to these email address(es) -- separate with commas
                          // Use "SELF" to send an email copy to the member who is filling out the form.
$email_member_form      = 'SELF,'.$email_membership;

                          // The producer form will be sent to these email address(es) -- separate with commas
                          // The "SELF" term does not function with this form.
$email_producer_form    = 'SELF,'.$email_standards;              // Where new producer emails notifications are sent

                          // Name of the membership coordinator or other official contact person (plain-text only).
                          // This is used e.g. for signing the member welcome letter (Use double-quotes so the
                          // newline character will be preserved)
$authorized_person      = "Tracey Ambrose\nPresident";








// GATHER CONFIGURATION OVER-RIDES FROM AN EXTERNAL FILE
@include_once ("config_override.php"); // Include override values only if the file exists








// ______ DEFNINITION OF CONSTANTS _________

// Highly unlikely that you will need to modify anything below this point

date_default_timezone_set($local_time_zone);
define('CURRENT_VERSION' ,      $current_version);
define('DB_NAME' ,              $db_name);
define('HOST_NAME' ,            $db_host);
define('MYSQL_USER' ,           $db_user);
define('MYSQL_PASS' ,           $db_pass);
define('MD5_MASTER_PASSWORD' ,  $md5_master_password);
define('PRODUCER_MARKDOWN' ,    $producer_markdown);
define('CUSTOMER_MARKUP' ,      $customer_markup);
define('INSTITUTION_MARKUP' ,   $institution_markup);
define('ORGANIZATION_TYPE' ,    $organization_type);
define('REQ_PRDCR_CONFIRM' ,    $req_prdcr_confirm);
define('DELIVERY_NO_PAYPAL' ,   $delivery_no_paypal);
define('STATE_TAX' ,            $state_tax);
define('SHOW_HEADER_LOGO' ,     $show_header_logo);
define('SHOW_HEADER_SITENAME' , $show_header_sitename);
define('FAVICON' ,              $favicon);
define('USE_HTMLDOC' ,          $use_htmldoc);
define('DAYS_PER_CYCLE' ,       $days_per_cycle);
define('INSTITUTION_WINDOW' ,   $institution_window);
define('SHOW_ACTUAL_PRICE' ,    $show_actual_price);
define('FILL_IN_MEMBER_ID' ,    $fill_in_member_id);
define('PRDCR_INFO_PUBLIC' ,    $prdcr_info_public);
define('HTMLDOC_PAGING' ,       $htmldoc_paging);
define('NEW_PRODUCER_PENDING' , $new_producer_pending);
define('RANDOM_CALC' ,          $random_calc);
define('MEMBERSHIP_IS_TAXED' ,  $membership_taxed);
define('COOP_FEE_IS_TAXED' ,    $coop_fee_taxed);

//General page information
define('BASE_URL' ,           $site_url);
define('PATH' ,               $food_coop_store_path);
define('FILE_PATH' ,          $file_path);
define('INVOICE_FILE_PATH' ,  $invoice_file_path);
define('INVOICE_WEB_PATH' ,   $invoice_web_path);
define('c' ,                  $domainname); // localfoodcoop.org
define('DOMAIN_NAME',         $domainname);
define('SITE_NAME' ,          $site_name);
define('SITE_CONTACT_INFO' ,  $site_contact_info);
define('SITE_MAILING_ADDR' ,  $site_mailing_address);


// Pages OUTSIDE of the FoodCoop application
define('MEMBERSHIP_PAGE',         $page_membership); //to refer membership questions
define('TERMS_OF_SERVICE',        $page_terms_of_service); //to refer membership for terms of use standards
define('LOCATIONS_PAGE',          $page_locations);
define('COOP_PRODUCERS_PAGE',     $page_coopproducers);
define('DIR_GRAPHICS',            $site_graphics);
define('SELF',                    $_SERVER['PHP_SELF']);
define('PER_PAGE' ,               $default_results_per_page); //default number of search results per page
define('ROUTE_CODE_TEMPLATE',    $route_code_template);

// table names as variables
$table_adj              = 'adjustments';
$table_adj_types        = 'adjustment_types'; //new
$table_auth_level       = 'authentication_levels';
$auth_table_name        = 'auth_users_c';
$table_cat              = 'categories';
$table_config           = 'configuration'; //new
$table_curdate          = 'current_delivery';
$table_basket           = 'customer_basket_items';
$table_basket_all       = 'customer_basket_overall';
$table_customer_tax     = 'customer_salestax';
$table_delcode          = 'delivery_codes';
$table_deldate          = 'delivery_dates';
$table_deltypes         = 'delivery_types'; //new
$table_fdel             = 'future_deliveries';
$table_how_heard        = 'how_heard'; //new
$table_hubs             = 'hubs'; //new
$table_jump             = 'jump_points'; //new
$table_mem              = 'members';
$table_mem_pref         = "members_prefs";
$table_mem_test         = "members_test";
$table_mem_type         = 'members_type';
$table_membership_types = 'membership_types';
$table_pay              = 'payment_method';
$table_prdcr_all        = 'producer_totals'; //not in dB
$table_prdcr            = 'producers';
$table_producers        =  $table_prdcr;
$table_prdcr_logos      = 'producers_logos'; //new
$table_prdcr_reg        = 'producers_registration'; //new
$table_prodtype         = 'production_types';
$table_product_img      = 'product_images'; //new
$table_products         = 'product_list'; //new
$table_products_temp    = 'product_list_a'; //new
$table_prep             = 'product_list_prep';
$table_previous         = 'product_list_previous';
$table_product_cat      = 'product_list_subcategories';
$table_product_store    = 'product_storage_types'; //new
$table_rt               = 'routes';
$table_tax              = 'sales_tax';
$table_session          = 'sessions'; //new
$table_set_state        = 'set_states'; //new
$table_subcat           = 'subcategories';
$table_trans            = 'transactions';
$table_trans_max        = 'transactions_invoicemax'; //new
$table_trans_type       = 'transactions_types'; //new
$table_zip              = 'zip'; //new
$table_zip_city         = 'zip_citytaxno'; //new
$table_zip_county       = 'zip_countytaxno'; //new

// note: $table_prod is sometimes TABLE_PRODUCER_REG and sometimes TABLE_PRODUCT
// these are set in the other config files, as needed.

//Table aliases
define('TABLE_ADJ' ,                  $db_prefix.$table_adj);
define('TABLE_AUTH' ,                 $db_prefix.$auth_table_name);
define('TABLE_AUTH_LEVELS' ,          $db_prefix.$table_auth_level);
define('TABLE_BASKET' ,               $db_prefix.$table_basket);
define('TABLE_BASKET_ALL' ,           $db_prefix.$table_basket_all);
define('TABLE_CATEGORY' ,             $db_prefix.$table_cat);
define('TABLE_CONFIGURATION' ,        $db_prefix.$table_config);
define('TABLE_CURDEL' ,               $db_prefix.$table_curdate);
define('TABLE_CUSTOMER_SALESTAX' ,    $db_prefix.$table_customer_tax);
define('TABLE_DELCODE' ,              $db_prefix.$table_delcode);
define('TABLE_DELDATE' ,              $db_prefix.$table_deldate);
define('TABLE_DELTYPE' ,              $db_prefix.$table_deltypes);
define('TABLE_FUTURE_DELIVERY' ,      $db_prefix.$table_fdel );
define('TABLE_HOW_HEARD' ,            $db_prefix.$table_how_heard);
define('TABLE_HUBS' ,                 $db_prefix.$table_hubs);
define('TABLE_JUMP_POINTS' ,          $db_prefix.$table_jump); //new
define('TABLE_MEMBER' ,               $db_prefix.$table_mem);
define('TABLE_MEMBER_TYPE' ,          $db_prefix.$table_mem_type);
define('TABLE_MEMBERSHIP_TYPES' ,     $db_prefix.$table_membership_types);
define('TABLE_MEMBER_PREF' ,          $db_prefix.$table_mem_pref); //new
define('TABLE_PAY' ,                  $db_prefix.$table_pay);
define('TABLE_PRODUCER' ,             $db_prefix.$table_prdcr);
define('TABLE_PRODUCER_LOGOS' ,       $db_prefix.$table_prdcr_logos); //new
define('TABLE_PRODUCER_REG' ,         $db_prefix.$table_prdcr_reg);
define('TABLE_PRODUCER_TOTALS' ,      $db_prefix.$table_prdcr_all);
define('TABLE_PRODUCT' ,              $db_prefix.$table_products);
define('TABLE_PRODUCT_IMAGES' ,       $db_prefix.$table_product_img); //new
define('TABLE_PRODUCT_PREP' ,         $db_prefix.$table_prep);
define('TABLE_PRODUCT_TEMP' ,         $db_prefix.$table_products_temp); //new
define('TABLE_PRODUCT_PREV' ,         $db_prefix.$table_previous);
define('TABLE_PRODUCT_TYPES' ,        $db_prefix.$table_prodtype);
define('TABLE_PRODUCT_STORAGE_TYPES', $db_prefix.$table_product_store);
define('TABLE_ROUTE' ,                $db_prefix.$table_rt);
define('TABLE_SALES_TAX' ,            $db_prefix.$table_tax);
define('TABLE_SESSION' ,              $db_prefix.$table_session); //new
define('TABLE_SET_STATE' ,            $db_prefix.$table_set_state); //new
define('TABLE_SUBCAT_MAP' ,           $db_prefix.$table_product_cat);
define('TABLE_SUBCATEGORY' ,          $db_prefix.$table_subcat);
define('TABLE_TRANS',                 $db_prefix.$table_trans);
define('TABLE_TRANSACTIONS' ,         $db_prefix.$table_trans);
define('TABLE_TRANS_TYPES' ,          $db_prefix.$table_trans_type);
define('TABLE_TTYPES',                $db_prefix.$table_trans_type);
define('TABLE_TRANSACTIONS_MAX',      $db_prefix.$table_trans_max);
define('TABLE_ZIP' ,                  $db_prefix.$table_zip);
define('TABLE_ZIP_CITYTAXNO' ,        $db_prefix.$table_zip_city);
define('TABLE_ZIP_COUNTYTAXNO' ,      $db_prefix.$table_zip_county);

//field aliases for Security.class
define('FIELD_USER' ,       'username_m');
define('FIELD_PASS' ,       'password');
define('FIELD_AUTH_TYPE' ,  'auth_type');

// contact e-mail addresses
define('CUSTOMER_EMAIL' ,       $email_customer);
define('GENERAL_EMAIL' ,        $email_general);
define('HELP_EMAIL' ,           $email_help);
define('MEMBERSHIP_EMAIL' ,     $email_membership);
define('ORDER_EMAIL' ,          $email_orders);
define('PAYPAL_EMAIL' ,         $email_paypal);
define('PRICELIST_EMAIL' ,      $email_pricelist);
define('PROBLEMS_EMAIL' ,       $email_problems);
define('PRODUCER_CARE_EMAIL' ,  $email_producer_care);
define('SOFTWARE_EMAIL' ,       $email_software);
define('STANDARDS_EMAIL' ,      $email_standards);
define('TREASURER_EMAIL' ,      $email_treasurer);
define('VOLUNTEER_EMAIL' ,      $email_volunteer);
define('WEBMASTER_EMAIL' ,      $email_webmaster);

define('MEMBER_FORM_EMAIL' ,    $email_member_form);
define('PRODUCER_FORM_EMAIL' ,  $email_producer_form);

define('AUTHORIZED_PERSON' ,    $authorized_person);

$table_prod = TABLE_PRODUCER_REG;

$connection = @mysql_connect(HOST_NAME, MYSQL_USER, MYSQL_PASS) or die("Couldn't connect: \n".mysql_error());
$db = @mysql_select_db(DB_NAME, $connection) or die(mysql_error());


// This function validates a login session and redirects the user to the login screen for unauthorized access
function validate_user() 
  {
    global $user_type;
    if ($user_type == 'valid_c' && ! $_SESSION['valid_c'])
      {
        header( "Location: show_login.php?call=".$_SERVER['REQUEST_URI']);
        exit;
      }
    elseif ($user_type == 'valid_m' && ! $_SESSION['valid_m'])
      {
        header( "Location: orders_login.php?call=".$_SERVER['REQUEST_URI']);
        exit;
      }
  }
?>
