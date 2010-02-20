<?php

function member_since($membership_date)
  {
    $year = substr($membership_date, 0, 4);
    $month = substr($membership_date, 4, 2);
    $day = substr($membership_date, 6);
    $member_since = date('F j, Y',mktime(0, 0, 0, $month, $day, $year));
  }