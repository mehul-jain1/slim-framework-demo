<?php
// cron job to update user subscriptions for coming week
// cron job to add subscription order for next day  of user
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
          $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
          try{
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

                      // // then update the subscription order of the user for next day if exist in order subscription table
                      if($current_day_index>=6)
                      $current_day_index=0;
                      else
                      $current_day_index+=1;
                      if(isset($quantity_current_week[$current_day_index]))
                      $quantity_next_day=(float)$quantity_current_week[$current_day_index]; // get the next day quantity to deliver
                      else
                      $quantity_next_day=$set_default_quantity;
                      echo $quantity_next_day ." ".$subscription_row['uid']."\n";
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
                else {
                }
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
