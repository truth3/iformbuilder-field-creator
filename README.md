# iformbuilder-field-creator
A simple utility designed to loop through all profiles and forms, adding a new field with a default value

To get started you need to define your **server** in "fieldCreator.php". 

You also need to add one file to the **auth** folder (keys.php). The keys.php file should look like the example below. The client key and secret needs to be associated to a user which is a server admin.

```php
<?php
//::::::::::::::  SET STATIC VARIABLES   ::::::::::::::

//Database Server Admin API App -
$client = '#####';
$secret = '#####';

?>
