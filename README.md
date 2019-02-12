# iformbuilder-field-creator
A simple utility designed to loop through all profiles and forms, adding a new field with a default value

To get started you need to add one file to the **auth** folder (keys.php). The keys.php file should look like the example below. The client key and secret needs to be associated to a user which is a server admin.

```php
<?php
//::::::::::::::  SET STATIC VARIABLES   ::::::::::::::

// Database Server Admin API App -
$client = '#####'; // abc123
$secret = '#####'; // def456
$server = '#####'; // apple.iformbuilder.com | support##.zerionsandbox.com
$elementName  = '#####'; // dfa_report_keys
$elementLabel  = '#####'; // DFA Report Keys
$dataType  = '#####'; // 1
$conditionValue  = '#####'; // false
?>
