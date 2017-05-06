[1mdiff --git a/composer.json b/composer.json[m
[1mindex 0ec507c..2f57f32 100644[m
[1m--- a/composer.json[m
[1m+++ b/composer.json[m
[36m@@ -2,6 +2,8 @@[m
     "require": {[m
         "slim/slim": "^3.0",[m
         "monolog/monolog": "^1.22",[m
[31m-        "akrabat/rka-ip-address-middleware": "^0.5.0"[m
[32m+[m[32m        "akrabat/rka-ip-address-middleware": "^0.5.0",[m
[32m+[m[32m        "sendotp/sendotp": "^1.0"[m
     }[m
[32m+[m
 }[m
[1mdiff --git a/logs/app.log b/logs/app.log[m
[1mindex cc03d21..98f604c 100644[m
[1m--- a/logs/app.log[m
[1m+++ b/logs/app.log[m
[36m@@ -418,3 +418,734 @@[m
 [2017-03-28 21:52:22] my_logger.INFO: fetching banner data [] [][m
 [2017-03-28 21:52:22] my_logger.INFO: fetching product category feed [] [][m
 [2017-03-28 21:52:22] my_logger.INFO: fetching home feed [] [][m
[32m+[m[32m[2017-04-14 19:50:17] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-14 19:51:09] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-14 19:54:03] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-14 19:54:59] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-14 19:56:03] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-14 19:57:17] my_logger.INFO: fetching banner data [] [][m
[32m+[m[32m[2017-04-14 19:57:17] my_logger.INFO: fetching product category feed [] [][m
[32m+[m[32m[2017-04-14 19:57:17] my_logger.INFO: fetching home feed [] [][m
[32m+[m[32m[2017-04-14 19:57:29] my_logger.INFO: fetching banner data [] [][m
[32m+[m[32m[2017-04-14 19:57:29] my_logger.INFO: fetching product category feed [] [][m
[32m+[m[32m[2017-04-14 19:57:30] my_logger.INFO: fetching home feed [] [][m
[32m+[m[32m[2017-04-14 20:02:04] my_logger.INFO: fetching banner data [] [][m
[32m+[m[32m[2017-04-14 20:02:04] my_logger.INFO: fetching product category feed [] [][m
[32m+[m[32m[2017-04-14 20:02:25] my_logger.INFO: fetching home feed [] [][m
[32m+[m[32m[2017-04-14 20:05:50] my_logger.INFO: fetching banner data [] [][m
[32m+[m[32m[2017-04-14 20:05:51] my_logger.INFO: fetching product category feed [] [][m
[32m+[m[32m[2017-04-14 20:05:52] my_logger.INFO: fetching home feed [] [][m
[32m+[m[32m[2017-04-14 20:06:21] my_logger.INFO: fetching banner data [] [][m
[32m+[m[32m[2017-04-14 20:06:23] my_logger.INFO: fetching product category feed [] [][m
[32m+[m[32m[2017-04-14 20:06:26] my_logger.INFO: fetching home feed [] [][m
[32m+[m[32m[2017-04-14 20:06:42] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-14 20:07:28] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-22 19:05:07] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:05:07] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:05:08] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:08:55] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:08:55] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:08:55] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:09:18] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:09:18] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:09:18] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:11:25] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:11:25] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:11:25] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:12:28] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:12:28] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:12:28] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:12:42] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:12:57] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:14:46] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:15:16] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:15:51] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:17:04] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:18:42] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:19:37] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:20:16] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:21:11] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:21:33] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:21:37] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:21:37] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:21:37] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:21:47] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:21:47] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:21:48] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:22:53] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:23:48] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:24:31] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:25:04] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:26:07] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:29:55] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-22 19:30:21] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-22 19:30:21] my_logger.INFO: login [] [][m
[32m+[m[32m[2017-04-22 19:30:33] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-22 19:30:34] my_logger.INFO: login [] [][m
[32m+[m[32m[2017-04-22 19:30:34] my_logger.INFO: fetching user data [] [][m
[32m+[m[32m[2017-04-22 19:30:45] my_logger.INFO: check_verify_status [] [][m
[32m+[m[32m[2017-04-22 19:30:45] my_logger.INFO: login [] [][m
[32m+[m[32m[2017-04-22 19:31:03] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:32:40] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:32:41] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:32:41] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:33:01] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:33:02] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:33:02] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:34:56] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:34:57] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:34:57] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:35:24] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:35:24] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:35:24] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:35:34] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:35:35] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:35:35] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:36:33] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:36:35] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:36:35] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2017-04-22 19:36:59] my_logger.INFO: fetching uid  from mobile [] [][m
[32m+[m[32m[2017-04-22 19:36:59] my_logger.INFO: check_product_exist [] [][m
[32m+[m[32m[2017-04-22 19:36:59] my_logger.INFO: placing order [] [][m
[32m+[m[32m[2