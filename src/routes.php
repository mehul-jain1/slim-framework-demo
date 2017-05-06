<?php
use Psr\Http\Message\ServerRequestInterface as Request ;
use Psr\Http\Message\ResponseInterface as Response;
// User Process routes
 $app->group("/auth",function() use ($app){
   require_once  __DIR__ .'/../modules/auth/register.php';
   require_once  __DIR__ .'/../modules/auth/login.php';
   require_once  __DIR__ .'/../modules/auth/reset.php';
   $app->post('/register/','register')->add('verify_mobile_unique');
   $app->post('/sendotp/','sendotp')->add('verify_mobile_unique');
   $app->post('/verify/','verify_user')->add('check_mobile_exists');
   $app->post('/login/','fetch_user_data')->add('login')->add('check_verify_status');
   $app->post('/verify_mobile_unique/','verify_mobile_unique_1');
   $app->post('/forget_password/','forget_password')->add('check_verify_status')->add('check_mobile_exists');
   $app->post('/reset_mobile/','reset_mobile')->add('check_mobile_exists')->add('verify_mobile_unique')->setName('reset_mobile');
   $app->post('/update_profile/','update_profile')->add('check_mobile_exists');

 });

// Home page feed routes
$app->group("/feed",function() use ($app){
  require_once  __DIR__ .'/../modules/feed/banner_feed.php';
  require_once  __DIR__ .'/../modules/feed/product_feed.php';
  require_once  __DIR__ .'/../modules/feed/home_feed.php';
  $app->post('/home/','home_feed')->add('product_category_feed')->add('banner_feed');
  $app->post('/products/','products_feed');
});

// order  routes
$app->group("/order",function() use ($app){
  require_once  __DIR__ .'/../modules/auth/login.php';
  require_once  __DIR__ .'/../modules/orders/order.php';
  require_once  __DIR__ .'/../modules/products/product.php';
  $app->post('/make/','place_order')->add('check_product_exist')->add('fetch_uid_from_mobile');
  $app->post('/set/','set_order_status')->add('fetch_uid_from_mobile');
});

// subscription  routes
$app->group("/subscription",function() use ($app){
  require_once  __DIR__ .'/../modules/auth/login.php';
  require_once  __DIR__ .'/../modules/products/product.php';
  require_once  __DIR__ .'/../modules/subscriptions/subscription.php';
  $app->post('/add/','add_subscription')->add('check_product_subscribable')->add('fetch_uid_from_mobile');
  $app->post('/edit/','edit_subscription')->add('check_subscription_exists')->add('fetch_uid_from_mobile');
  $app->post('/set/','set_subscription_status')->add('fetch_uid_from_mobile');
  $app->post('/invoice/','fetch_subscription_invoice');
});

// my orders and subscription routes
$app->group("/orders",function() use ($app){
  require_once  __DIR__ .'/../modules/auth/login.php';
  require_once  __DIR__ .'/../modules/orders/order.php';
  require_once  __DIR__ .'/../modules/products/product.php';
  require_once  __DIR__ .'/../modules/subscriptions/subscription.php';
  $app->post('/placed/','my_orders')->add('placed_subscriptions')->add('placed_orders')->add('fetch_uid_from_mobile');
});
$app->group("/user",function() use ($app){
  require_once  __DIR__ .'/../modules/auth/login.php';
  require_once  __DIR__ .'/../modules/profile/profile.php';
  $app->post('/profile/','fetch_profile_data')->add('orders_count')->add('fetch_uid_from_mobile');
});
?>
