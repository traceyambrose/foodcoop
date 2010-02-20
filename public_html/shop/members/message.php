<?php
// Uncomment the following section to include as a notification message.

$notification_message = '
    <div style="border: 1px solid #000; padding:1em; background-color:#dfc;">

    <p style="text-align: center; font-family:Verdana,Sans-serif; font-size:14px; color:#240;">
    You are successfully logged in to your new Local Food Coop website!
    </p>

    <p style="text-align: left; font-family:Verdana,Sans-serif; font-size:12px; color:#240;">
    Your member permissions are set in the database members.auth_type.  Depending upon your permissions
    you will have access to different sections below.  Also different sections will show up based upon
    whether an ordering cycle is currently open and whether you are a producer or not.
    </p>

    <p style="text-align: left; font-family:Verdana,Sans-serif; font-size:12px; color:#240;">
    One commonly overlooked step is the &ldquo;make live&rdquo; feature.  After products have been
    added by a producer, and the producer is no longer pending, and the producer is &ldquo;listed&rdquo;,
    you must click on the &ldquo;make live&rdquo; link at the top of the &ldquo;Producer/Product List
    (monthname)&rdquo; page.  Only members with auth_type=administrator will be able to do that function.
    </p>

    <p style="text-align: left; font-family:Verdana,Sans-serif; font-size:12px; color:#240;">
    When you are ready to remove this message, it is located at
    <code style="font-weight:bold;color:#630"> /shop/members/messages.php</code> in a default installation.
    If you want to keep a message here and have it match the message on the login page, just link one
    of the files to the other and make all your changes in the single file.
    </p>

    </div>
    <br />
    <br />
';
?>
