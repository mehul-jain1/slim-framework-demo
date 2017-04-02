<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;

function products_feed(Request $request, Response $response)
{
  global $container;
  $container['logger']->addInfo("fetching Products data");
  $result = array("status"=>0,"msg"=>"<strong> Oh Snap !</strong>Something went wrong !!");
  $request_params = $request->getParsedBody();
  if(isset($request_params['category_id']))
  {
    $category_id=(int)trim($request_params['category_id']);
    $product_status=1; // fetch live products only
    $product_feed=array();
    $sql="select *  from `products` where `category_id`=:category_id and `product_status`=:product_status";
    try{
        // Get DB Object
        $db = $container['db'];
        $p_stmt= $db->prepare($sql);
        $p_stmt->bindParam(":category_id",$category_id);
        $p_stmt->bindParam(":product_status",$product_status);
        $p_stmt->execute();
        if($p_stmt->rowCount()>0)
        {
            while($product_row=$p_stmt->fetch(PDO::FETCH_ASSOC))
            {
              $product_feed[]=$product_row;
            }
              $result = array("status"=>1,"msg"=>"product data feched!","products_data"=>$product_feed);
              return $response->withStatus(200)->write(json_encode($result));
              return $response;
              // $request=$request->withAttribute('product_feed',$product_feed);
              // $response=$next($request, $response);
              // return $response;
        }
        else {
          $result['msg']='No products  present';
          return $response->withStatus(302)->write(json_encode($result));
        }
    }catch(PDOException $e){
      $result['msg']=$e->getMessage();
      return $response->withStatus(403)->write(json_encode($result));
    }
  }
  else {
    $result['msg']='please specify the category id to fetch products!';
    return $response->withStatus(403)->write(json_encode($result));
  }
}
?>
