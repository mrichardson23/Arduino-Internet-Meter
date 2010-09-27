<?php
$authhost="{imap.gmail.com:993/imap/ssl/novalidate-cert}";
if ($mbox=imap_open( $authhost, "username@gmail.com", "password"))
	{
		$check = imap_mailboxmsginfo($mbox);
		if ($check->Unread < 10)
		{
			 // each unread e-mail will move the meter by ~10%, 10 messages is max
			echo chr(25 * $check->Unread);
		}
		else
		{
			echo chr(255);
		}
	} else
	{
		echo chr(0);
	}

?>