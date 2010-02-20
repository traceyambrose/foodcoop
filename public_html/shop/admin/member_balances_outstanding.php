<?php
$user_type = 'valid_c';
include_once ("config_foodcoop.php");
session_start();
validate_user();


include("template_hdr.php");

?><style type="text/css">
table {
  width:90%;
  border: 1px solid #000066;
  margin:0;
  }
td {
  border: 1px solid #dddddd;
  padding:0px 5px 0px 5px;
  }
a {
  text-decoration: none;
  color: #123456;
  }
a:hover {
  text-decoration: underline;
  color: #a86420;
  }
tr:hover {
  background:#f8ffe0;
  }
</style><?

// Display this many rows at a time
$group_size = 50;

if ($_GET['begin_row'])
  {
    $this_begin_row = $_GET['begin_row'];
  }
else
  {
    $this_begin_row = 0;
  }
// Find out how many members there are:
$query='
  SELECT
    COUNT(member_id) AS count
  FROM
    '.TABLE_MEMBER;
$sql = mysql_query($query);
$row = mysql_fetch_array($sql);
$number_of_members = $row['count'];

// Find out the current order cycle:
$query='
  SELECT
    delivery_id
  FROM
    '.TABLE_CURDEL;
$sql = mysql_query($query);
$row = mysql_fetch_array($sql);
$current_delivery_id = $row['delivery_id'];

// Either do a member detail lookup or...
if ($_GET['lookup'])
  {
    include("member_balance_function.php");
    $member_id = preg_replace('/[^0-9]/','',$_GET['lookup']);
    $display = getMemberBalance($member_id, $current_delivery_id, 'display');
    echo $display;
  }
// ...show totals for a group of members
else
  {
    include("member_balance_function.php");
    ?><table><tr><th>Member Name</th><th>Member_ID / Username</th><th>Balance Total</th></tr><?
    // Cycle through the list of the members for this grouping
    $query='
      SELECT
        member_id,
        last_name,
        first_name,
        username_m 
      FROM
        '.TABLE_MEMBER.'
      ORDER BY
        member_id ASC
      LIMIT
        '.$this_begin_row.', '.$group_size;
    $sql = mysql_query($query);
    while($row = mysql_fetch_array($sql))
      {
        $member_id = $row['member_id'];
        $member_name = $row['first_name']." ".$row['last_name'];
        $username = $row['username_m'];
        $return_value = array_pop (getMemberBalance($member_id, $current_delivery_id, ""));
        $balance = number_format ($return_value['balance'], 2);
        $amount_paid = number_format ($return_value['amount_paid'], 2);
  //    $user_total = getMemberTotal($member_id,$current_delivery_id,"display");
        if ($amount_paid != "0.00")
          {
            $paid_value = "[after paying $amount_paid]";
          }
        else
          {
            $paid_value = '';
          }
        echo'
          <tr>
            <td>'.$member_name.'</td>
            <td>'.$member_id.' '.$username.'</td>
            <td><a href="member_balances_outstanding.php?lookup='.$member_id.'&begin_row='.$this_begin_row.'">'.$balance.' '.$paid_value.'</a></td>
          </tr>';
      }
  ?></table><?
  }

// Include links to groupings of members
echo 'View members: ';
$begin_row = 0;
while ($begin_row < $number_of_members)
  {
    $end_row = $begin_row + $group_size;
    $group_begin = $begin_row + 1;

    // Truncate if over the maximum number of members
    if ($end_row > $number_of_members)
      {
        $end_row = $number_of_members;
      }

    // Provide for bolding the current choice if we are doing a detailed lookup
    $strong = ''; $not_strong = '';
    if ($begin_row == $this_begin_row)
      {
        $strong = '<strong>['; $not_strong = ']</strong>';
      }

    // Show the grouping of members
    if (($begin_row == $this_begin_row) && (! $_GET['lookup']))
      {
        echo '
        '.$strong.$group_begin.'-'.$end_row.$not_strong.' &nbsp; ';
      }
    else
      {
        echo '
          <a href="'.$_SERVER['PHP_SELF'].'?begin_row='.$begin_row.'">'.$strong.$group_begin.'-'.$end_row.$not_strong.'</a> &nbsp; ';
      }
    $begin_row = $begin_row + $group_size;
  }
echo '<br>';

include("template_footer.php"); 
?>