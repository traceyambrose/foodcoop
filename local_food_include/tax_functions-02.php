<?php

/*******************************************************************************

Because different locales have different tax calculations, this
function will probably need to be customized for each installation.

The function should return the city_name, copo_city, city_tax_rate,
county_name, copo_county, county_tax_rate, state_id, state_tax_rate.

The term "copo" is from the original Oklahoma installation and seems
to refer to the state tax code for that entity (city, county, etc.).


deltype may be "W" for delivery to a work address
               "H" for delivery to a home address
            or "P" for pickup by the customer

Usually (???) taxes are calculated at the location where the
transaction takes place (i.e. work, home, or delcode location).
So we globalize all relevant information and operate on it
as appropriate to the locale requirements.

*******************************************************************************/

function tax_rates ()
  {
    global // For delivery to home address:
           $address_line1, $address_line2, $city, $county, $state, $zip,
           // For delivery to work address:
           $work_address_line1, $work_address_line2, $work_city, $work_state, $work_zip,
           // For pickup at the pickup location (not yet implemented in v1.5.x):
           $delcode_address_line1, $delcode_address_line2, $delcode_city, $delcode_state, $delcode_zip,
           // Other relevant variables:
           $connection, $delcode_id, $deltype;

    if ($deltype == 'H') // Home delivery (based upon home zip-code)
      {
        $query = '
        SELECT
          city.copo AS copo_city,
          city_tax.current_rate AS city_tax_rate
        FROM
            '.TABLE_ZIP_CITYTAXNO.' AS city
        LEFT JOIN '.TABLE_SALES_TAX.' AS city_tax ON city_tax.copo = city.copo
        WHERE city.zip = "'.$zip.'"';
        $result = @mysql_query($query, $connection) or die(mysql_error());
        while ( $row = mysql_fetch_array($result) )
          {
            $copo_city = $row['copo_city'];
            $city_tax_rate = $row['city_tax_rate'];
          }
        $query = '
        SELECT
          county.copo AS copo_county,
          county_tax.current_rate AS county_tax_rate
        FROM
            '.TABLE_ZIP_COUNTYTAXNO.' AS county
        LEFT JOIN '.TABLE_SALES_TAX.' AS county_tax ON county_tax.copo = county.copo
        WHERE county.zip = "'.$zip.'"';
        while ( $row = mysql_fetch_array($result) )
          {
            $copo_county = $row['copo_county'];
            $county_tax_rate = $row['county_tax_rate'];
          }
      }
    elseif ($deltype == 'W') // Work delivery (based upon work zip-code)
      {
        $query = '
        SELECT
          city.copo AS copo_city,
          city_tax.current_rate AS city_tax_rate
        FROM
            '.TABLE_ZIP_CITYTAXNO.' AS city
        LEFT JOIN '.TABLE_SALES_TAX.' AS city_tax ON city_tax.copo = city.copo
        WHERE city.zip = "'.$work_zip.'"';
        $result = @mysql_query($query, $connection) or die(mysql_error());
        if ( $row = mysql_fetch_array($result) )
          {
            $copo_city = $row['copo_city'];
            $city_tax_rate = $row['city_tax_rate'];
          }
        $query = '
        SELECT
          county.copo AS copo_county,
          county_tax.current_rate AS county_tax_rate
        FROM
            '.TABLE_ZIP_COUNTYTAXNO.' AS county
        LEFT JOIN '.TABLE_SALES_TAX.' AS county_tax ON county_tax.copo = county.copo
        WHERE county.zip = "'.$work_zip.'"';
        if ( $row = mysql_fetch_array($result) )
          {
            $copo_county = $row['copo_county'];
            $county_tax_rate = $row['county_tax_rate'];
          }
      }
    else // $deltype = 'P' (pickup) (based upon zip-code of delivery location: from delivery_codes.copo_city)
      {
        $query = '
        SELECT
          city.copo AS copo_city,
          city_tax.current_rate AS city_tax_rate,
          county.copo AS copo_county,
          county_tax.current_rate AS county_tax_rate
        FROM '.TABLE_DELCODE.'
        LEFT JOIN '.TABLE_ZIP_CITYTAXNO.' AS city ON city.copo = '.TABLE_DELCODE.'.copo_city
        LEFT JOIN '.TABLE_ZIP_COUNTYTAXNO.' AS county ON city.county = county.county
        LEFT JOIN '.TABLE_SALES_TAX.' AS city_tax ON city_tax.copo = city.copo
        LEFT JOIN '.TABLE_SALES_TAX.' AS county_tax ON county_tax.copo = county.copo
        WHERE
          '.TABLE_DELCODE.'.delcode_id = "'.$delcode_id.'"';
      }

    // Now run the query (whichever is correct)
    $result = @mysql_query($query, $connection) or die(mysql_error());
    while ( $row = mysql_fetch_array($result) )
      {
        $copo_city = $row['copo_city'];
        $city_tax_rate = $row['city_tax_rate'];
        $copo_county = $row['copo_county'];
        $county_tax_rate = $row['county_tax_rate'];
      }

    $state_id = 'NE';
    $state_tax_rate = STATE_TAX;

    if (! $city_tax_rate) $city_tax_rate = 0;
    if (! $county_tax_rate) $county_tax_rate = 0;
    if (! $state_tax_rate) $state_tax_rate = 0;

    return (array ($city_name, $copo_city, $city_tax_rate, $county_name, $copo_county, $county_tax_rate, $state_id, $state_tax_rate));
  }
?>