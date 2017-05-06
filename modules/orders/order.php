<?php
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
function place_order(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("placing order");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if((isset($request_params['uid']) || !is_null($request->getAttribute('uid'))) && isset($request_params['product_id']))
  {
      $order_columns=array();
      // process order data
      if(isset($request_params['uid']) && $request_params['uid'] !='')
        $uid=intval($request_params['uid']);
      else
        $uid=intval($request->getAttribute('uid'));
      if(isset($request_params['product_id']) && $request_params['product_id'] !='')
        $product_id=intval($request_params['product_id']);

      if(isset($request_params['mobile_no']) && $request_params['mobile_no'] !='')
      $mobile_no=trim($request_params['mobile_no']);

      if(isset($request_params['product_quantity']) && $request_params['product_quantity']!='' )
        $product_quantity=intval($request_params['product_quantity']);
      else
        $product_quantity=1; // default quantity single
      $time=time();
      if(isset($uid))
      {
        try{
            // Get DB Object
            $db = $container['db'];
            $ins_q=$db->prepare("INSERT INTO `orders`(`uid`,`product_id`,`initated_time`,`updated_time`,`product_quantity`,`mobile_no`) VALUES(:uid,:product_id,
            :initated_time,:updated_time,:product_quantity,:mobile_no)");
            $order_value=array(':uid'=>$uid,
                             ':product_id'=>$product_id,
                             ':initated_time'=>$time,
                             ':updated_time'=>$time,
                             ':product_quantity'=>$product_quantity,
                             ':mobile_no'=>$mobile_no
                            );
            $ins_q->execute($order_value);
            if($ins_q->rowCount()>0)
            {
                $order_id=$db->lastInsertID();
                $result = array("status"=>1,"order_id"=>$order_id,"msg"=>"Order Placed Succesfully !!");
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
}
function placed_orders(Request $request, Response $response ,$next)
{
  global $container;
  date_default_timezone_set('Asia/Kolkata');
  $container['logger']->addInfo("Fetching My Orders");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['uid']) || !is_null($request->getAttribute('uid')))
  {
    $placed_orders=array();
    if(isset($request_params['uid']) && $request_params['uid'] !='')
      $uid=intval($request_params['uid']);
    else
      $uid=intval($request->getAttribute('uid'));
      $sql="select o.`order_id`, o.`mobile_no`,o.`product_id` ,o.`initated_time`,o.`updated_time`,o.`order_status`,o.`product_quantity`,p.`product_name`,p.`product_img_url`,p.`product_price`,p.`product_price_quantity`,p.`product_quantity_unit` from `orders` o, `products` p where `uid`=:uid  and o.`product_id`=p.`product_id` order by `updated_time` desc";
      try{
          // Get DB Object
          $db = $container['db'];
          $p_stmt= $db->prepare($sql);
          $p_stmt->bindParam(":uid",$uid);
          $p_stmt->execute();
          if($p_stmt->rowCount()>0)
          {
            while($order_row=$p_stmt->fetch(PDO::FETCH_ASSOC))
            {
              // calculate total price and  quantity of product by quantity chosen
              if(isset($order_row['product_quantity']) && isset($order_row['product_price']) )
              {
                $invoice_details=array();
                $product_quantity=intval($order_row['product_quantity']);
                $product_price=intval($order_row['product_price']);
                $product_price_quantity=(isset($order_row['product_price_quantity'])? intval($order_row['product_price_quantity']):'');
                $product_quantity_unit=(isset($order_row['product_quantity_unit'])? trim($order_row['product_quantity_unit']):'');
                $invoice_details=fetch_invoice_details($product_quantity,$product_price,$product_price_quantity,$product_quantity_unit);
                $order_row=array_merge($order_row,$invoice_details);
                // check updated_time if exist else take initated_time
                if(isset($order_row['updated_time']) && $order_row['updated_time']!='')
                $order_row['time']=$order_row['updated_time'];
                else
                $order_row['time']=$order_row['initated_time'];
                $order_row['time']=date("F j, Y, g:i a",$order_row['time']);
                //unset unwanted vars
                unset($order_row['product_price_quantity']);
                unset($order_row['product_quantity_unit']);
                unset($order_row['initated_time']);
                unset($order_row['updated_time']);
              }
              $placed_orders[]=$order_row;
            }
            // $result = array("status"=>1,"msg"=>" Placed Orders!","uid"=>$uid,"orders"=>$placed_orders);
            // return $response->withStatus(200)->write(json_encode($result));
          }

          $request=$request->withAttribute('orders',$placed_orders);
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
function set_order_status(Request $request, Response $response,$next)
{
  global $container;
  $container['logger']->addInfo("setting  order status");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if((isset($request_params['uid']) || !is_null($request->getAttribute('uid'))) && isset($request_params['order_id']) && isset($request_params['order_status']))
  {
    if(isset($request_params['uid']) && $request_params['uid'] !='')
      $uid=intval($request_params['uid']);
    else
      $uid=intval($request->getAttribute('uid'));
    $time=time();
    $order_id=intval($request_params['order_id']);
    $order_status=intval($request_params['order_status']);
    $sql="update `orders` set `order_status`=:order_status ,`updated_time`=:updated_time where `order_id`=:order_id and `uid`=:uid";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":order_status",$order_status);
        $p_stmt->bindParam(":updated_time",$time);
        $p_stmt->bindParam(":order_id",$order_id);
        $p_stmt->bindParam(":uid",$uid);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
          $result = array("status"=>1,"order_status"=>$order_status,"uid"=>$uid);
          $result['msg']=fetch_s_order_status_msg($order_status);
          return $response->withStatus(200)->write(json_encode($result));
        }
        else {
          $result = array("status"=>0);
          $result['msg']=fetch_e_order_status_msg($order_status);
          return $response->withStatus(302)->write(json_encode($result));
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

function fetch_invoice_details($product_quantity,$product_price,$product_price_quantity,$product_quantity_unit){
  $invoice_details=array();
  // calculate total price
    $total_price=$product_quantity*$product_price;
    //discount if present
    // $total_price-=$discount;
    //delivery charge if present
    // $total_price+=$delivery_charge;
    // Add taxes if present
    // $total_price+=$service_tax;

  // calculate total quantity of product to be delivered
    if(isset($product_price_quantity) && $product_price_quantity!='')
    {
      $total_quantity=$product_price_quantity*$product_quantity;
      if($product_quantity_unit=='litre')
      $total_quantity.=" Litre";
      else if($product_quantity_unit=='gm')
      $total_quantity.=" Grams";
      else
      $total_quantity.="";
    }
    $invoice_details=array("total_price"=>$total_price,
                            "total_quantity"=>$total_quantity
                          );

    return $invoice_details;
}
function fetch_s_order_status_msg($order_status)
{
  if($order_status==0)
  $msg="Order Cancelled Succesfully";
  if($order_status==1)
  $msg="Order Placed Succesfully";
  if($order_status==2)
  $msg="Order Delivered Succesfully";
  return $msg;
}
function fetch_e_order_status_msg($order_status)
{
  if($order_status==0)
  $msg="error in cancelling order";
  if($order_status==1)
  $msg="error in placing order";
  if($order_status==2)
  $msg="error in delvering order";
  return $msg;
}
?>
