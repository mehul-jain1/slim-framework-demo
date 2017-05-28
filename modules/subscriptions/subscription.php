<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
/*
--------------------------------------
subscription type
1- daily
2- alternate
3- custom
--------------------------------------------------------------------------------------
quantity_default_week - quantity which is default for every week(weekly plan)
quantity_current_week -quantity which is set by user for current week -(current -plan )
---------------------------------------------------------------------------------------
mode
i_s - insert subscription
e_s - edit subscription
------------------------
subscrition_status
1 - active
0 - inactive or stopped
---------------------------
*/
function add_subscription(Request $request, Response $response , $next)
{
  global $container;
  $container['logger']->addInfo(" Adding subscription");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['subscription_type']))
  {
    $subscription_type=intval($request_params['subscription_type']);
    $mode='i_s';
    process_subscription_type($request,$response,$subscription_type,$mode);
  }
  else
  {
    $result['msg']='Sorry! Please specify subscription type ';
    return $response->withStatus(404)->write(json_encode($result));
  }
}
function edit_subscription(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo(" Updating subscription");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['subscription_type']))
  {
    $subscription_type=intval($request_params['subscription_type']);
    $mode='e_s';
    process_subscription_type($request,$response,$subscription_type,$mode);
  }
  else
  {
    $result['msg']='Sorry! Please specify subscription type ';
    return $response->withStatus(404)->write(json_encode($result));
  }
}
function set_subscription_status(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("setting  subscription status");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if((isset($request_params['uid']) || !is_null($request->getAttribute('uid'))) && isset($request_params['subscription_id']) && isset($request_params['subscription_status']))
  {
    if(isset($request_params['uid']) && $request_params['uid'] !='')
      $uid=intval($request_params['uid']);
    else
      $uid=intval($request->getAttribute('uid'));

    $subscription_id=intval($request_params['subscription_id']);
    $subscription_status=intval($request_params['subscription_status']);
    $time=time();
    $sql="update `subscriptions` set `subscription_status`=:subscription_status ,`updated_time`=:updated_time where `subscription_id`=:subscription_id and `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":subscription_status",$subscription_status);
        $p_stmt->bindParam(":updated_time",$time);
        $p_stmt->bindParam(":subscription_id",$subscription_id);
        $p_stmt->bindParam(":uid",$uid);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          $result = array("status"=>1,"subscription_status"=>$subscription_status,"uid"=>$uid);
          $result['msg']=fetch_s_subscription_status_msg($subscription_status);
          return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result = array("status"=>0);
          $result['msg']=fetch_e_subscription_status_msg($subscription_status);
          return $response->withStatus(302)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Error in Setting Status of Subscription';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function check_subscription_exists(Request $request, Response $response , $next)
{
  global $container;
  $container['logger']->addInfo("check_subscription_exists");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();

  if(isset($request_params['subscription_id']))
  {
        $subscription_id=intval(trim($request_params['subscription_id']));
        $subscription_status=1; // can only edit active subscription
        $sql="select `subscription_id` from `subscriptions` where `subscription_id`=:subscription_id and `subscription_status`=:subscription_status";
        try{
            // Get DB Object
            $db = $container['db'];
            $p_stmt= $db->prepare($sql);
            $p_stmt->bindParam(":subscription_id",$subscription_id);
            $p_stmt->bindParam(":subscription_status",$subscription_status);
            $p_stmt->execute();
            if($p_stmt->rowCount()>0)
            {
              $response=$next($request, $response);
              return $response;
            }
            else {
              $result['msg']='Sorry! Provided  Subscription ID is not available..';
              return $response->withStatus(404)->write(json_encode($result));
            }
        }catch(PDOException $e){
          $result['msg']=$e->getMessage();
          return $response->withStatus(403)->write(json_encode($result));
        }
  }
  else {
    $result['msg']='Please specify subscription ID..';
    return $response->withStatus(403)->write(json_encode($result));
  }
}
function process_subscription_type(Request $request, Response $response,$subscription_type,$mode)
{
    $request_params = $request->getParsedBody();
    date_default_timezone_set('Asia/Kolkata');
    $quantity_default_week=array();
    $default_quantity=1;
    $checkTime = '2100'; // for editing  subscription
    if($mode=='e_s')
    {
      $current_week_list=fetch_current_week_list($request,$response,$subscription_type);
    }
    // process type
    if($subscription_type==1) // daily subscription
    {
      if(isset($request_params['quantity_week']))
      {
        $quantity_default_week=explode(',',$request_params['quantity_week']);
        // explore weekly subscription plan (0-6)
        for($i=0;$i<7;$i++)
        {
          if(!isset($current_quantity))
          {
            $quantity_default_week[$i]=(float)$quantity_default_week[$i];
            $current_quantity=(float)$quantity_default_week[$i]; // set default quantity to this for current transaction
          }
          else {
            $quantity_default_week[$i]=$current_quantity;
          }
        }
        $quantity_default_week=implode(',',$quantity_default_week);
        if($mode=='i_s')
        insert_subscription($request,$response,$subscription_type,$quantity_default_week);
        else if($mode=='e_s')
        update_subscription($request,$response,$subscription_type,$quantity_default_week);
        else {
          $result['msg']='Sorry! some error occured while processing !!!!';
          return $response->withStatus(404)->write(json_encode($result));
        }
      }
      else {
        $result['msg']='Sorry! Provide default quantity to subscribe ';
        return $response->withStatus(404)->write(json_encode($result));
      }
    }
    else if($subscription_type==2)
    {
      if(isset($request_params['quantity_week']))
      {
        $quantity_default_week=explode(',',$request_params['quantity_week']);
        // explore week
        $quantity_default_week=set_default_week_list($quantity_default_week);
        if($mode=='i_s')
        insert_subscription($request,$response,$subscription_type,$quantity_default_week);
        else if($mode=='e_s')
        update_subscription($request,$response,$subscription_type,$quantity_default_week);
        else {
          $result['msg']='Sorry! some error occured while processing !!!!';
          return $response->withStatus(404)->write(json_encode($result));
        }
      }
      else {
        $result['msg']='Sorry! Provide default quantity to subscribe ';
        return $response->withStatus(404)->write(json_encode($result));
      }
    }
    else if($subscription_type==3)  // custom option
    {
      if(isset($request_params['quantity_week']))
      {
        $quantity_default_week=explode(',',$request_params['quantity_week']);

        // for above 9 pm the quantity for next day can not be changed so discarding from updating
        if(intval(date('Hi')) >= intval($checkTime))
        {
          $current_day_index=get_current_day_index();
          if($current_day_index>=6)
          $current_day_index=0;
          else
          $current_day_index+=1;
          if(isset($current_week_list[$current_day_index]))
          {
            $quantity_default_week[$current_day_index]=$current_week_list[$current_day_index];
            // var_dump($quantity_default_week);
          }
        }

        // else  explore week
        for($i=0;$i<7;$i++)
        {
          if(isset($quantity_default_week[$i]))
          {
            $quantity_default_week[$i]=(float)$quantity_default_week[$i];
          }
          else {
            $quantity_default_week[$i]=$default_quantity;
          }
        }
        $quantity_default_week=implode(',',$quantity_default_week);
        if($mode=='i_s')
        insert_subscription($request,$response,$subscription_type,$quantity_default_week);
        else if($mode=='e_s')
        update_subscription($request,$response,$subscription_type,$quantity_default_week);
        else {
          $result['msg']='Sorry! some error occured while processing !!!!';
          return $response->withStatus(404)->write(json_encode($result));
        }
      }
      else {
        $result['msg']='Sorry! Provide default quantity to subscribe ';
        return $response->withStatus(404)->write(json_encode($result));
      }
    }
    else {
      $result['msg']='Sorry! subscription type does not exist ';
      return $response->withStatus(404)->write(json_encode($result));
    }
}
function insert_subscription(Request $request, Response $response,$subscription_type,$quantity_default_week)
{
  global $container;
  $request_params = $request->getParsedBody();
  if(isset($request_params['uid']) && $request_params['uid'] !='')
    $uid=intval($request_params['uid']);
  else
    $uid=intval($request->getAttribute('uid'));
  if(isset($request_params['product_id']) && $request_params['product_id'] !='')
    $product_id=intval($request_params['product_id']);
    if(isset($request_params['mobile_no']) && $request_params['mobile_no'] !='')
    $mobile_no=trim($request_params['mobile_no']);
  else
    $mobile_no=NULL; // mobile no if provided
  $time=time();
  if(isset($uid))
  {
    try{
        // Get DB Object
        $db = $container['db'];
        $ins_q=$db->prepare("INSERT INTO `subscriptions`(`uid`,`product_id`,`initiated_time`,`updated_time`,`quantity_default_week`,`quantity_current_week`,`subscription_type`,`mobile_no`) VALUES(:uid,:product_id,
        :initiated_time,:updated_time,:quantity_default_week,:quantity_current_week,:subscription_type,:mobile_no)");
        $subscription_value=array(':uid'=>$uid,
                         ':product_id'=>$product_id,
                         ':initiated_time'=>$time,
                         ':updated_time'=>$time,
                         ':quantity_default_week'=>$quantity_default_week,
                         ':quantity_current_week'=>$quantity_default_week, // As understood while placing order default and current week plan are same...
                         ':subscription_type'=>$subscription_type,
                         ':mobile_no'=>$mobile_no
                          );
        $ins_q->execute($subscription_value);
        if($ins_q->rowCount()>0)
        {
            $subscription_id=$db->lastInsertID();
            $result = array("status"=>1,"subscription_id"=>$subscription_id,"msg"=>"Subscribed Succesfully !! Tip -You can check your Subscriptions in My subscriptions...");
            return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result['msg']='Order Processing failed Please Retry!';
          return $response->withStatus(404)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Sorry! error occured in Placing order';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function update_subscription(Request $request, Response $response,$subscription_type,$quantity_current_week)
{
  global $container;
  $request_params = $request->getParsedBody();
  if(isset($request_params['uid']) && $request_params['uid'] !='')
    $uid=intval($request_params['uid']);
  else
    $uid=intval($request->getAttribute('uid'));

  if(isset($request_params['subscription_id']) && $request_params['subscription_id'] !='')
    $subscription_id=intval($request_params['subscription_id']);
  if(isset($request_params['make_default_flag']) && $request_params['make_default_flag'] !='')
  $make_default_flag=intval($request_params['make_default_flag']);
  else
  $make_default_flag=0;

  if(isset($uid) && isset($subscription_id))
  {
    $subscription_columns=array();
    if(isset($request_params['product_id']) && $request_params['product_id'] !='')
    $subscription_columns[]="`product_id`=".":product_id";
    if(isset($request_params['mobile_no']) && $request_params['mobile_no'] !='')
    $subscription_columns[]="`mobile_no`=".":mobile_no";
    if(isset($subscription_type)&& $subscription_type !='')
    $subscription_columns[]="`subscription_type`=".":subscription_type";
    if(isset($request_params['quantity_week']) && $request_params['quantity_week'] !='')
    $subscription_columns[]="`quantity_current_week`=".":quantity_current_week";
    if($make_default_flag)
    $subscription_columns[]="`quantity_default_week`=".":quantity_default_week";
    // set updated time
    $subscription_columns[]="`updated_time`=".":updated_time";

    if(count($subscription_columns)>0)
    {
      $time=time();
    $subscription_data_sql="update  `subscriptions` set ".join(',',$subscription_columns)."  where `subscription_id`=:subscription_id";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($subscription_data_sql);
        $p_stmt->bindParam(":subscription_id",$subscription_id);
        if(isset($request_params['product_id']) && $request_params['product_id'] !='')
        $p_stmt->bindParam(":product_id",$request_params['product_id']);
        if(isset($request_params['mobile_no']) && $request_params['mobile_no'] !='')
        $p_stmt->bindParam(":mobile_no",$request_params['mobile_no']);
        if(isset($subscription_type)&& $subscription_type !='')
        $p_stmt->bindParam(":subscription_type",$subscription_type);
        if(isset($quantity_current_week)&& $quantity_current_week !='')
        $p_stmt->bindParam(":quantity_current_week",$quantity_current_week);
        if($make_default_flag)
        $p_stmt->bindParam(":quantity_default_week",$quantity_current_week);
        $p_stmt->bindParam(":updated_time",$time);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            $result = array("status"=>1,"msg"=>"subscription updated succesfully!");
            return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result['msg']='No changes done!';
          return $response->withStatus(404)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
    }
    else {
      $result['msg']='Please specify atleast one field to update ';
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Sorry! error occured in edit Subscription';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function placed_subscriptions(Request $request, Response $response , $next)
{
  global $container;
  date_default_timezone_set('Asia/Kolkata');
  $container['logger']->addInfo("Fetching My subscriptions");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['uid']) || !is_null($request->getAttribute('uid')))
  {
    $placed_subscriptions=array();
    $quantity_current_week=array();
    $product_quantity=array();
    $checkTime = '2045'; // for editing  subscription
    if(isset($request_params['uid']) && $request_params['uid'] !='')
      $uid=intval($request_params['uid']);
    else
      $uid=intval($request->getAttribute('uid'));
    $subscription_status=0; // do not fetch cancelled subscriptions
      $sql="select s.`subscription_id`, s.`subscription_type`,s.`mobile_no`,s.`product_id`,s.`quantity_current_week`,s.`initiated_time`,s.`updated_time`,s.`subscription_status`,p.`product_name`,p.`product_img_url`,p.`product_price`,p.`product_price_quantity`,p.`product_quantity_unit` from `subscriptions` s, `products` p where `uid`=:uid  and s.`subscription_status`!=:subscription_status and s.`product_id`=p.`product_id`  order by `updated_time` desc";
      try{
          // Get DB Object
          $db = $container['db'];
          $p_stmt= $db->prepare($sql);
          $p_stmt->bindParam(":uid",$uid);
          $p_stmt->bindParam(":subscription_status",$subscription_status);
          $p_stmt->execute();
          if($p_stmt->rowCount()>0)
          {
            while($subscription_row=$p_stmt->fetch(PDO::FETCH_ASSOC))
            {
              // process weekly sbscription qunatity
              if(isset($subscription_row['quantity_current_week']) && $subscription_row['quantity_current_week']!='')
              {
                  $quantity_current_week=explode(',',$subscription_row['quantity_current_week']);

                  /* send qunatity editable status  along with quantity
                     ediable status 1
                     not editable status 0
                   */
                  foreach($quantity_current_week as $quantity)
                  {
                    $product_quantity[]=array("quantity"=>$quantity,"status"=>1);
                  }
                  $current_day_index=get_current_day_index();
                  if(intval(date('Hi')) >= intval($checkTime))
                  {
                    if($current_day_index>=6)
                    $current_day_index=0;
                    else
                    $current_day_index+=1;
                    $product_quantity[$current_day_index]["status"]=0;
                    $product_quantity[$current_day_index]["status_msg"]="Sorry! you can not edit this quantity";
                  }
                  $subscription_row['product_quantity']=$product_quantity;
              }
              // check updated_time if exist else take initated_time
              if(isset($subscription_row['updated_time']) && $subscription_row['updated_time']!='')
              $subscription_row['time']=intval($subscription_row['updated_time']);
              else
              $subscription_row['time']=intval($subscription_row['initiated_time']);
              $subscription_row['time']=date("F j, Y, g:i a",$subscription_row['time']);

              //unset unwanted vars
              unset($subscription_row['product_price_quantity']);
              // unset($subscription_row['product_quantity_unit']);
              unset($subscription_row['initiated_time']);
              unset($subscription_row['quantity_current_week']);
              unset($subscription_row['updated_time']);
              unset($product_quantity);
              $placed_subscriptions[]=$subscription_row;
            }
            // $result = array("status"=>1,"msg"=>" Placed Orders!","uid"=>$uid,"subcriptions"=>$placed_subscriptions);
            // return $response->withStatus(200)->write(json_encode($result));
          }
          $request=$request->withAttribute('subscriptions',$placed_subscriptions);
          $response=$next($request, $response);
          return $response;
      }catch(PDOException $e){
        $result['msg']=$e->getMessage();
        return $response->withStatus(403)->write(json_encode($result));
      }
  }
  else {
    $result['msg']='Error in fetching  Orders';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function my_orders(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("Fetching My subscriptions");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['uid']) || !is_null($request->getAttribute('uid')))
  {
  // get all feeds data from routes
  $subscriptions_feed=$request->getAttribute('subscriptions');
  $orders_feed=$request->getAttribute('orders');
  $result = array("status"=>1,"msg"=>"Placed orders !","subscriptions"=>$subscriptions_feed,"orders"=>$orders_feed);
  return $response->withStatus(200)->write(json_encode($result));
  return $response;
  }
  else {
    $result['msg']='Error in fetching  your orders';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function fetch_subscription_invoice(Request $request, Response $response,$next){
  global $container;
  $container['logger']->addInfo("fetching subscription invoice");
  date_default_timezone_set('Asia/Kolkata');
  $request_params = $request->getParsedBody();
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  if(isset($request_params['subscription_id']) && $request_params['subscription_id'] !='')
    $subscription_id=intval($request_params['subscription_id']);
  if(isset($subscription_id))
  {
    $user_data=array();;
    $subscription_data_sql="select sum(`quantity`) as total_quantity, sum(`price`) as total_amount,s2.`mobile_no`,s2.`uid`,s2.`subscription_type` ,s2.`subscription_status`,s2.`initiated_time` as subscription_created_date ,p.`product_name`,p.`product_price`,p.`product_quantity_unit` from `subscriptions_orders` s1 , `subscriptions` s2 ,products p  where s1.`subscription_id`=s2.`subscription_id` and s2.`product_id`=p.`product_id` and s2.`subscription_id`=:subscription_id and s1.`s_order_status`!=2 order by s1.`initiated_time` desc";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($subscription_data_sql);
        $p_stmt->bindParam(":subscription_id",$subscription_id);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            $subscription_invoice_data=$p_stmt->fetch(PDO::FETCH_ASSOC);
            if(!isset($subscription_invoice_data['total_quantity']) || $subscription_invoice_data['total_quantity']==null)
            $subscription_invoice_data['total_quantity']=0;
            if(!isset($subscription_invoice_data['total_amount']) || $subscription_invoice_data['total_amount']==null)
            $subscription_invoice_data['total_amount']=0;

            // convert to human readable date
            $subscription_invoice_data['subscription_created_date']=date("F j, Y, g:i a",$subscription_invoice_data['subscription_created_date']);
            $result = array("status"=>1,"msg"=>"Pending amount!" ,"subscription_invoice_data"=>$subscription_invoice_data);
            return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result['msg']='Oops! no pending amount for this subscription id';
          return $response->withStatus(404)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='sorry, Please specify subscription id';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function set_default_week_list($quantity_default_week)
{
  date_default_timezone_set('Asia/Kolkata');
  $default_week_list=array();
  $quantity_default_week=(float)$quantity_default_week[0];
  $count_day=0;
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
  if($current_day_index>=6) // set from next day
  $current_day_index=0;
  else
  $current_day_index++;

  while($count_day<7) // index start from 0
  {
    if(!isset($default_week_list[$current_day_index]))
    {
      // echo $current_day_index.','.$count_day;
    $default_week_list[$current_day_index]=$quantity_default_week;
    $count_day++;
    if($current_day_index>=6)
    $current_day_index=0;
    else
    $current_day_index++;
    // var_dump($default_week_list);
    }
    // set alternate day as 0 quantity (no delivery)
    if(!isset($default_week_list[$current_day_index]) && $count_day<7)
    {
      // echo $current_day_index.','.$count_day;
    $default_week_list[$current_day_index]=0;// set alternate days to 0
    $count_day++;
    if($current_day_index>6)
    $current_day_index=0;
    else
    $current_day_index++;
    // var_dump($default_week_list);
    }
  }
  ksort($default_week_list);
  $default_week_list=implode(',',$default_week_list);
  return $default_week_list;
}
function fetch_current_week_list(Request $request, Response $response,$subscription_type)
{
  global $container;
  $container['logger']->addInfo("fetching_current_week_list");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['subscription_id']))
  {
        $subscription_id=intval(trim($request_params['subscription_id']));
        $subscription_status=0;
        $sql="select `quantity_current_week` from `subscriptions` where `subscription_id`=:subscription_id and `subscription_status`!=:subscription_status";
        try{
            // Get DB Object
            $db = $container['db'];
            $p_stmt= $db->prepare($sql);
            $p_stmt->bindParam(":subscription_id",$subscription_id);
            $p_stmt->bindParam(":subscription_status",$subscription_status); // fetch active subscriptions
            $p_stmt->execute();
            if($p_stmt->rowCount()>0)
            {
              $sub_row=$p_stmt->fetch(PDO::FETCH_ASSOC);
              $quantity_current_week=$sub_row['quantity_current_week'];
              if(isset($quantity_current_week))
              {
                $quantity_current_week=explode(',',$quantity_current_week);
              }
              return $quantity_current_week;
            }
            else {
              $result['msg']='Sorry! Unable to edit current subscription !!!';
              return $response->withStatus(404)->write(json_encode($result));
            }
        }catch(PDOException $e){
          $result['msg']=$e->getMessage();
          return $response->withStatus(403)->write(json_encode($result));
        }
  }
  else {
    $result['msg']='Please specify Subscription ID..';
    return $response->withStatus(403)->write(json_encode($result));
  }
}
function fetch_s_subscription_status_msg($subscription_status)
{
  if($subscription_status==0)
  $msg="subscription Cancelled Succesfully";
  if($subscription_status==1)
  $msg="subscription started Succesfully";
  if($subscription_status==2)
  $msg="subscription stopped Succesfully";
  return $msg;
}
function fetch_e_subscription_status_msg($subscription_status)
{
  if($subscription_status==0)
  $msg="error in cancelling subscription";
  if($subscription_status==1)
  $msg="error in starting  subscription";
  if($subscription_status==2)
  $msg="error in stopping subscription";
  return $msg;
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
