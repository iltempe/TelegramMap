You must have PHP, SQLITE DB configured and a Telegram Bot.

In localhost is possible to launch
php start.php 'sethook' to set start.php as webhook
php start.php 'removehook' to remove start.php as webhook
php start.php 'getupdates' to run getupdates.php

After setup webhook is possible to use telegram managed by webhost

To use the system
- Make a Telegram Bot
- Send Location to it
- Reply to bot with a text description
- All data are sent in the database and than convert in CSV format
- Data can mapped now

Rename settings_t_template.php in settings_t.php and put inside

- TOKEN of telegram Bot
- Link to webhook if you want use webhook
- Name of DB and Tables
- Usernam e Password of DB


To use the application use "start.php getupdates" for manual execution. "start.php sethook" for Telegram webhook execution.

A simple example is implemented here http://iltempe.github.io/Emergenzeprato/

Good Luck!
