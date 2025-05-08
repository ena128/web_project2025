<?php
require __DIR__ . '/../vendor/autoload.php';


 require __DIR__ . '/../routes/activitylogs.php';
require __DIR__ . '/../routes/categories.php'; 
 require __DIR__ . '/../routes/priorities.php'; 
/* require __DIR__ . '/../routes/routes.php'; */


require __DIR__ . '/../routes/tasks.php'; 
require __DIR__ . '/../routes/users.php';




Flight::route('/', function(){  //define route and define function to handle request
   echo 'Hello from flight!';
});


Flight::start();  
?>
