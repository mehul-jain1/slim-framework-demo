<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
function set_order(Request $request, Response $response,$next)
{
  date_default_timezone_set('Asia/Kolkata');
  global $container;
  $container['logger']->addInfo("setting  order status");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['order_id']) && isset($request_params['order_status']))
  {
    $time=time();
    $order_id=intval($request_params['order_id']);
    $order_status=intval($request_params['order_status']);
    $sql="update `orders` set `order_status`=:order_status ,`updated_time`=:updated_time where `order_id`=:order_id";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":order_status",$order_status);
        $p_stmt->bindParam(":updated_time",$time);
        $p_stmt->bindParam(":order_id",$order_id);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          $result = array("status"=>1,"order_status"=>$order_status);
          $result['msg']=fetch_s_order_status($order_status);
          return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result = array("status"=>0);
          $result['msg']=fetch_e_order_status($order_status);
          return $response->withStatus(200)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Error in Setting Status of Order';
    return $response->withStatus(500)->write(json_encode($result));
  }
}

function set_subscription(Request $request, Response $response,$next)
{
  date_default_timezone_set('Asia/Kolkata');
  global $container;
  $container['logger']->addInfo("setting  subscription status");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['subscription_id']) && isset($request_params['subscription_status']))
  {
    $subscription_id=intval($request_params['subscription_id']);
    $subscription_status=intval($request_params['subscription_status']);
    $time=time();
    $sql="update `subscriptions` set `subscription_status`=:subscription_status ,`updated_time`=:updated_time where `subscription_id`=:subscription_id ";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":subscription_status",$subscription_status);
        $p_stmt->bindParam(":updated_time",$time);
        $p_stmt->bindParam(":subscription_id",$subscription_id);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          $result = array("status"=>1,"subscription_status"=>$subscription_status);
          $result['msg']=fetch_s_subscription_status($subscription_status);
          return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result = array("status"=>0);
          $result['msg']=fetch_e_subscription_status($subscription_status);
          return $response->withStatus(200)->write(json_encode($result));
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

function set_order_sub_status(Request $request, Response $response,$next)
{
  date_default_timezone_set('Asia/Kolkata');
  global $container;
  $container['logger']->addInfo("setting  order subscriptions status");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['subscription_id']) && isset($request_params['subscription_status']) && isset($request_params['start_time']) && isset($request_params['end_time']))
  {
    $subscription_id=intval($request_params['subscription_id']);
    $s_order_status=intval($request_params['subscription_status']);
    $start_time=strtotime($request_params['start_time']);
    $end_time=strtotime($request_params['end_time']);
    $time=time();
    $sql="update `subscriptions_orders` set `s_order_status`=:s_order_status where `subscription_id`=:subscription_id and `initiated_time`>=:start_time and `initiated_time`<=:end_time ";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":s_order_status",$s_order_status);
        $p_stmt->bindParam(":subscription_id",$subscription_id);
        $p_stmt->bindParam(":start_time",$start_time);
        $p_stmt->bindParam(":end_time",$end_time);
        $p_stmt->execute();
        // var_dump($start_time);
        // var_dump($end_time);
        if($p_stmt->rowCount()>0)
        {
          $result = array("status"=>1,"subscription_status"=>$s_order_status);
          $result['msg']=fetch_s_order_subscription_status_msg($s_order_status);
          return $response->withStatus(200)->write(json_encode($result));
        }
        else
        {
          $result = array("status"=>0);
          $result['msg']=fetch_e_order_subscription_status_msg($s_order_status);
          return $response->withStatus(200)->write(json_encode($result));
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
function set_user(Request $request, Response $response)
{
  date_default_timezone_set('Asia/Kolkata');
  global $container;
  $container['logger']->addInfo("verify_user");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['uid']) && isset($request_params['verified_status']))
  {
    $uid=intval(trim($request_params['uid']));
    $verified_status=intval(trim($request_params['verified_status']));
    $sql="update `user` set `verified_status`=:verified_status where `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":verified_status",$verified_status);
        $p_stmt->bindParam(":uid",$uid);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          if($verified_status==1)
          $result = array("status"=>1,"msg"=>"user verified succesfully");
          else
          $result = array("status"=>1,"msg"=>"user unverified succesfully");
          return $response->withStatus(200)->write(json_encode($result));
        }
        else {

          if($verified_status==1)
          $result = array("status"=>1,"msg"=>"user alerady verified ");
          else
          $result = array("status"=>1,"msg"=>"user alerady unverified ");
          return $response->withStatus(200)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='Please specify all fields';
    return $response->withStatus(500)->write(json_encode($result));
    }
}

function fetch_s_order_status($order_status)
{
  if($order_status==0)
  $msg="Order Cancelled Succesfully";
  if($order_status==1)
  $msg="Order Placed Succesfully";
  if($order_status==2)
  $msg="Order Delivered Succesfully";
  return $msg;
}

function fetch_e_order_status($order_status)
{
  if($order_status==0)
  $msg="error in cancelling order";
  if($order_status==1)
  $msg="error in placing order";
  if($order_status==2)
  $msg="error in delvering order";
  return $msg;
}
function fetch_s_subscription_status($subscription_status)
{
  if($subscription_status==0)
  $msg="subscription Cancelled Succesfully";
  if($subscription_status==1)
  $msg="subscription started Succesfully";
  if($subscription_status==2)
  $msg="subscription stopped Succesfully";
  return $msg;
}
function fetch_e_subscription_status($subscription_status)
{
  if($subscription_status==0)
  $msg="Already has cancelling subscription";
  if($subscription_status==1)
  $msg="Already has starting  subscription";
  if($subscription_status==2)
  $msg="Already has stopping subscription";
  return $msg;
}
function fetch_s_order_subscription_status_msg($subscription_status)
{
  if($subscription_status==0)
  $msg="subscription Amount Cancelled";
  if($subscription_status==1)
  $msg="subscription Amount set Pending ";
  if($subscription_status==2)
  $msg="subscription Amount set Paid ";
  return $msg;
}
function fetch_e_order_subscription_status_msg($subscription_status)
{
  if($subscription_status==0)
  $msg="Already has cancelled status";
  if($subscription_status==1)
  $msg="Already has  Pending status";
  if($subscription_status==2)
  $msg="Already has Paid Status";
  return $msg;
}
?>
