<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;

function fetch_profile_data(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("fetching user data");
  $request_params = $request->getParsedBody();
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  if(isset($request_params['uid']) && $request_params['uid'] !='')
    $uid=intval($request_params['uid']);
  else
    $uid=intval($request->getAttribute('uid'));
  if(isset($uid))
  {
    $user_data=array();;
    $user_data_sql="select `uid`,`name`,`mobile_no`,`address`,`city`,`pincode` from `user` where `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($user_data_sql);
        $p_stmt->bindParam(":uid",$uid);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            $user_data=$p_stmt->fetch(PDO::FETCH_ASSOC);
            $subscription_count=$request->getAttribute('subscription_count');
            $order_count=$request->getAttribute('order_count');
            $result = array("status"=>1,"msg"=>"profile info!" ,"user_data"=>$user_data,"subscription_count"=>$subscription_count,"order_count"=>$order_count);
            return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result['msg']='This mobile no. is not registered!';
          return $response->withStatus(404)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='sorry, an error ocurred while fetching profile info..';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
function orders_count(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("fetching order count");
  $request_params = $request->getParsedBody();
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  if(isset($request_params['uid']) && $request_params['uid'] !='')
    $uid=intval($request_params['uid']);
  else
    $uid=intval($request->getAttribute('uid'));
  if(isset($uid))
  {
    $user_data=array();
    $sub_data_sql="select count(`subscription_id`) as subscription_count from `subscriptions` where `subscription_status`!=0  and  `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sub_data_sql);
        $p_stmt->bindParam(":uid",$uid);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            $q_res=$p_stmt->fetch(PDO::FETCH_ASSOC);
            $subscription_count=$q_res['subscription_count'];
        }
        else {
          $subscription_count=0;
        }
        $request=$request->withAttribute('subscription_count',$subscription_count);
    }
    catch(PDOException $e)
    {
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }

    $order_data_sql="select count(`order_id`) as order_count from `orders` where `order_status`!=0  and  `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($order_data_sql);
        $p_stmt->bindParam(":uid",$uid);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            $q_res=$p_stmt->fetch(PDO::FETCH_ASSOC);
            $order_count=$q_res['order_count'];
        }
        else {
          $order_count=0;
        }
        $request=$request->withAttribute('order_count',$order_count);
        $response=$next($request, $response);
        return $response;
    }
    catch(PDOException $e)
    {
      $result['msg']=$e->getMessage();
      return $response->withStatus(500)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='sorry, an error ocurred while fetching order count in..';
    return $response->withStatus(500)->write(json_encode($result));
  }
}
?>
