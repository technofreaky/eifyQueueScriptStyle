eifyQueueScriptStyle
====================

This is class is heavily based on wordpress wp_enqueue_script / wp_enqueue_style


How To Use It
=============

Step 1 : Include the file in your application [bootstrap / load / anywhere you need ] and initiate the class 
```php
require('class-eifyQueueScriptStyle.php');
$queue = new eify_QueueScriptStyle;
```

### Step 2 : Add A Script To Queue
```
Options [
     HandlerName : Some Name For The Enqued Script
     SRC : File URL
     Version : Script Version
     Footer : if need to show in footer { TRUE | FALSE }
]

script_enqueue($handle,$src,$version,$footer = false)

```

```php
$queue->script_enqueue('jQuery','jQuery.js','1.0',true);
$queue->style_enqueue('jQueryCss','jQuery.css','1.0',false);
```

### Step 3 : Generate HTML 
```
Options [
     footer : True / False [ Get Only Footer Script / Styles]
]
```

```php
echo $queue->get_script();
echo $queue->get_style(true) ;
```

Other Options
=============

### Check For Existing In Script / Style
```php
var_dump($queue->has_script('jQuery'));
var_dump($queue->has_script('jQueryCss'));
```

### Remove A Script / Style From Queue 
```php
$queue->dequeue_script('jQuery);
$queue->dequeue_style(jQueryCss);
```



## If You Need Help
Please submit all issues and questions using GitHub issues and I will try to help you :)
