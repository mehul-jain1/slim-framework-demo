<?php
// cron job to update user subscriptions for coming week
// cron job to add subscription order for next day  of user
require_once __DIR__ . '/../vendor/PHPExcel/PHPExcel.php';
require_once __DIR__ . '/../vendor/autoload.php';
use Mailgun\Mailgun;
$container=array();
$container['db']=connectPDO(); // get mysql connections
update_subscriptions();
function update_subscriptions()
{
          global $container;
          date_default_timezone_set('Asia/Kolkata');
          $checkTime = '2100'; // for editing  subscription
          $quantity_default_week=array();
          $quantity_current_week=array();
          $set_default_quantity=1 ; // set default quantity to 1 if not fetched
          $result = array("status"=>0,"msg"=>"Something went wrong !!");
          try{
                    $sql="select count(`s_order_id`) as s_order_count from `subscriptions_orders`  where `initiated_time`>".strtotime($checkTime);
                // Get DB Object
                $db_select = $container['db'];
                $s_stmt= $db_select->prepare($sql);
                $s_stmt->execute();
                $s_order_row=$s_stmt->fetch(PDO::FETCH_ASSOC);
                $s_order_count=$s_order_row['s_order_count'];
                if($s_order_count>0)
                {
                    $result['status']=1;
                    $result['msg']="Cron job Already Ran!! ";
                }
                else
                {
                // fetch only active subscriptions
                $sql="select s.`subscription_id`, s.`quantity_default_week`,s.`quantity_current_week`,s.`mobile_no`,s.`uid`,s.`product_id`,p.`product_price` from subscriptions s , products p where p.`product_id`=s.`product_id` and s.`subscription_status`=1";
                // Get DB Object
                $db_select = $container['db'];
                $s_stmt= $db_select->prepare($sql);
                $s_stmt->execute();
                if($s_stmt->rowCount()>0)
                {
                  while($subscription_row=$s_stmt->fetch(PDO::FETCH_ASSOC))
                  {
                    // get dfault and current subscription and update the today default quantity of subscribed user
                    $quantity_default_week=explode(',',$subscription_row['quantity_default_week']);
                    $quantity_current_week=explode(',',$subscription_row['quantity_current_week']);

                    if(intval(date('Hi')) >= intval($checkTime))
                    {
                      $current_day_index=get_current_day_index();
                      if(isset($quantity_default_week[$current_day_index]))
                      {
                        // copy current day default quantity to current quantity after 9 pm
                        $quantity_current_week[$current_day_index]=$quantity_default_week[$current_day_index];
                      }
                      else{
                        $quantity_current_week[$current_day_index]=$set_default_quantity;
                      }
                    $time=time();
                    $subscription_id=intval($subscription_row['subscription_id']);
                    $quantity_current_week_string=implode(',',$quantity_current_week);
                    $subscription_data_sql="update  `subscriptions` set `quantity_current_week`=:quantity_current_week  where `subscription_id`=:subscription_id";
                        // Get DB Object
                        $db_update = $container['db'];
                        $u_stmt= $db_update->prepare($subscription_data_sql);
                        $u_stmt->bindParam(":quantity_current_week",$quantity_current_week_string);
                        $u_stmt->bindParam(":subscription_id",$subscription_id);
                        $u_stmt->execute();
                        if($u_stmt->rowCount()>0)
                        {
                        }
                        else {
                        }

                      // then update the subscription order of the user for next day if exist in order subscription table
                      if($current_day_index>=6)
                      $current_day_index=0;
                      else
                      $current_day_index+=1;
                      if(isset($quantity_current_week[$current_day_index]))
                      $quantity_next_day=(float)$quantity_current_week[$current_day_index]; // get the next day quantity to deliver
                      else
                      $quantity_next_day=$set_default_quantity;
                      // echo $quantity_next_day ." ".$subscription_row['uid']."\n";
                      if(isset($quantity_next_day) && $quantity_next_day!=0)
                      {
                        // calcualte price for next day and save
                        $price=$subscription_row['product_price']*$quantity_next_day;
                        try{
                            // Get DB Object
                            $db_insert = $container['db'];
                            $time=time();
                            $ins_q=$db_insert->prepare("INSERT INTO `subscriptions_orders`(`uid`,`subscription_id`,`product_id`,`initiated_time`,`quantity`,`price`,`mobile_no`) VALUES(:uid,:subscription_id,
                            :product_id,:initiated_time,:quantity,:price,:mobile_no)");
                            $subscription_value=array(':uid'=>$subscription_row['uid'],
                                                      ':subscription_id'=>$subscription_row['subscription_id'],
                                                      ':product_id'=>$subscription_row['product_id'],
                                                      ':initiated_time'=>$time,
                                                      ':quantity'=>$quantity_next_day,
                                                      ':price'=>$price,
                                                      ':mobile_no'=>$subscription_row['mobile_no']
                                                    );
                            $ins_q->execute($subscription_value);
                            if($ins_q->rowCount()>0)
                            {

                            }
                            else {
                            }
                        }catch(PDOException $e){
                          echo $e->getMessage();
                        }
                      }
                    }
                  }
                }
                else {
                }
                $result['msg']="";
               }
                // fetch subscription orders
                $subscriptions_orders=array();
                $sql="select so.`s_order_id`,so.`uid`,u.`name`,so.`mobile_no`,so.`subscription_id`,s.`subscription_type`,so.`quantity`,so.`price`,so.`product_id`,so.`initiated_time`,p.`product_name`,p.`product_price`, so.`s_order_status` from subscriptions_orders so,subscriptions s,products p ,user u  where so.`subscription_id`=s.`subscription_id` and so.`product_id`=p.`product_id`and so.`uid`=u.`uid` and so.`initiated_time`>".strtotime($checkTime);
                // Get DB Object
                $db_select = $container['db'];
                $s_stmt= $db_select->prepare($sql);
                $s_stmt->execute();
                if($s_stmt->rowCount()>0)
                {
                  while($daily_sub_order_row=$s_stmt->fetch(PDO::FETCH_ASSOC))
                  {
                    $daily_sub_order_row['initiated_time']=date("F j, Y, g:i a",$daily_sub_order_row['initiated_time']);
                    $subscriptions_orders[]=$daily_sub_order_row;
                  }
                }

                // fetch placed orders
                $placed_orders=array();
                $time=strtotime($checkTime)-(24*60*60); // one day back
                $sql="select o.`order_id`, o.`mobile_no`,o.`product_id` ,o.`initated_time`,o.`order_status`,o.`product_quantity`,p.`product_name`,p.`product_price`,p.`product_price_quantity` from `orders` o, `products` p where  o.`product_id`=p.`product_id` and o.`initated_time`>".$time." order by `initated_time` asc ;";
                // Get DB Object
                $db_select = $container['db'];
                $s_stmt= $db_select->prepare($sql);
                $s_stmt->execute();
                if($s_stmt->rowCount()>0)
                {
                  while($order_row=$s_stmt->fetch(PDO::FETCH_ASSOC))
                  {
                    $order_row['initated_time']=date("F j, Y, g:i a",$order_row['initated_time']);
                    $placed_orders[]=$order_row;
                  }
                }
                $order_flag=0;
                $sub_order_flag=0;
                $subscription_attach_array=array();
                $objPHPExcel = new PHPExcel(); // create new excel object
                // generate excel sheet for the next day subscriptions orders
                if(count($subscriptions_orders)>0)
                {
                  $sub_order_flag=1;
                  // Create new PHPExcel object
                  $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Subscriptions');
                  $objPHPExcel->addSheet($myWorkSheet, 0);
                  $objPHPExcel->setActiveSheetIndex(0);
                  //set title
                  $subscription_sheet_title=array("Subscription Order ID","uid","Name","Mobile No","Subscription ID","Subscription Type","Quantity","Price","Product ID","Initiated Time","Product Name","Product Price","Subscription Order Status");
                  $objPHPExcel->getActiveSheet()
                              ->fromArray(
                                  $subscription_sheet_title,  // The data to set
                                  NULL,        // Array values with this value will not be set
                                  'A1'         // Top left coordinate of the worksheet range where
                                               //    we want to set these values (default is A1)
                                        );
                 // set rows
                  $objPHPExcel->getActiveSheet()
                              ->fromArray(
                                  $subscriptions_orders,  // The data to set
                                  NULL,        // Array values with this value will not be set
                                  'A2'         // Top left coordinate of the worksheet range where
                                               //    we want to set these values (default is A1)
                                        );
                  $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                  $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
                  $text="Subscription Orders for tomorrow";
                }
                else
                $text="No subscription orders for tomorrow";

                // generate excel sheet for the last day placed  orders (from last day 9 pm to current)
                if(count($placed_orders)>0)
                {
                  $order_flag=1;
                  // Create new PHPExcel object
                  $myWorkSheet = new PHPExcel_Worksheet($objPHPExcel, 'Orders');
                  $objPHPExcel->addSheet($myWorkSheet, 1);
                  $objPHPExcel->setActiveSheetIndex(1);
                  //set title
                  $order_sheet_title=array("order_id","mobile_no","product_id","initated_time","order_status ","product_quantity","product_name","product_price","product_price_quantity");
                  $objPHPExcel->getActiveSheet()
                             ->fromArray(
                                  $order_sheet_title,  // The data to set
                                  NULL,        // Array values with this value will not be set
                                  'A1'         // Top left coordinate of the worksheet range where
                                              //    we want to set these values (default is A1)
                                       );
                // set rows
                  $objPHPExcel->getActiveSheet()
                             ->fromArray(
                                  $placed_orders,  // The data to set
                                  NULL,        // Array values with this value will not be set
                                  'A2'         // Top left coordinate of the worksheet range where
                                              //    we want to set these values (default is A1)
                                       );
                }
                if($sub_order_flag || $order_flag)
                {
                    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
                    $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
                    $subscription_attach_array=array('attachment'=>array('update_subscriptions.xlsx'));
                    $result['status']=1;
                    $result['msg'].=" Orders Fetched!!";
                }

                // send mail to admin
                # Instantiate the client.
                $mgClient = new Mailgun('key-230e681310d55262f1b4a522351e4b24');
                $domain = "sandbox45ab372e285848dd98318d94e284f353.mailgun.org";
                # Make the call to the client.
                $msg_result = $mgClient->sendMessage($domain, array(
                        'from'    => 'Cron daemon <info@gaudugdham.com>',
                        'to'      => 'mehuljain160@gmail.com',
                    //     'cc'      => 'saahil2910@gmail.com',
                    //     'bcc'     => 'bar@example.com',
                        'subject' => 'tomorrow\'s orders for BackToRoots',
                        'text'    => $text,
                    //     'html'    => '<html>HTML version of the body</html>'
                    ),$subscription_attach_array);
               $msg_result=(array)$msg_result;
               if($msg_result['http_response_code']==200)
               $result['msg'].=" Also, Mailed to admin";
               echo json_encode($result);
              }
              catch(PDOException $e){
                echo $e->getMessage();
              }

}

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
function get_current_day_index()
{
  date_default_timezone_set('Asia/Kolkata');
  $current_day=date('D');
  switch ($current_day) {
    case 'Mon':
      $current_day_index=0;
      break;
    case 'Tue':
      $current_day_index=1;
      break;
    case 'Wed':
      $current_day_index=2;
      break;
    case 'Thu':
      $current_day_index=3;
      break;
    case 'Fri':
      $current_day_index=4;
      break;
    case 'Sat':
      $current_day_index=5;
      break;
    case 'Sun':
      $current_day_index=6;
      break;
    default:
    $current_day_index=0;
      break;
  }
  return $current_day_index;
}

?>
