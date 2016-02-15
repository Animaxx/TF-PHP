# TF-PHP

Archived from google code hosting https://code.google.com/archive/p/php-tf-framework/

### Framework Configuration
In file /TFCore/TFConfig.php

#### Meaning of each parameters

+ **mode** ：Current project mode, it has two option "debug" model or "release" model. In the debug model, it will display all error.
+ **autoload_extname** ：Which file's suffix will be auto loading.
+ **autoload_folder** ：Except TF folders, it store which folders which be auto loading.
+ **autoload_ignore** : Here store folders that not need auto to loading
+ **error_report_line** ：Define how many line source code will be showed around error code.
+ **auto_session** ：Should it auto use auto session 
+ **controller_folder** ：Define the folder name of the controller. 
+ **model_folder** ：Define the folder name of the model. 
+ **view_folder** ：Define the folder name of the view template. 
+ **default_controller** ：Default controller class name.
+ **default_action** ：Default action function name.
+ **url_controller_tag** ：The URL Tag of controller name
+ **url_action_tag** ：The URL Tag of action name
+ **global_calss** ：Define global classes 
+ **global_pretreatment** ：Define a function that will be executed before controller
+ **debug_echo_sql** ：Will it shows debug sql
+ **timezone** ：Define current timezone
+ **root_URL** ：Website path



