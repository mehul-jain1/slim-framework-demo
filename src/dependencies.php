<?php
// DIC configuration
$container=$app->getContainer();

// // mysql pdo connection
// $container['db']=function($container){
//   $db=$container['settings']['db'];
//   $pdo=new PDO('mysql:host='.$db['host'].';dbname='.$db['dbname'],$db['user'],$db['pass']);
//   $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//   $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
//   return $pdo;
// };
// MongoLog
$container['logger'] = function($container) {
    $logger = new \Monolog\Logger('my_logger');
    $file_handler = new \Monolog\Handler\StreamHandler("../logs/app.log");
    $logger->pushHandler($file_handler);
    return $logger;
};
// mysql db
$container['db']=connectPDO();

function connectPDO($db_no=0)
{
  $dbname = array("gaudugdh_ios");
  $dbhost = array("gaudugdham.com");
  $dbuser = array("gaudugdh_user");
  $dbpass = array("apiios@gaudugdham");

    try {
      $dbh=new PDO('mysql:host='.$dbhost[$db_no].';dbname='.$dbname[$db_no], $dbuser[$db_no], $dbpass[$db_no]);
        $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      return $dbh;
    } catch (Exception $e) {
      echo $e->getMessage();
    }

}
?>
