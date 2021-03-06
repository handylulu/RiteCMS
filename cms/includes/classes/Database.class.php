<?php
class Database
 {
  private static $_instance = null;
  public static $db_settings;
  public static $complete;
  public static $content;
  public static $entries;
  public static $userdata;

  public function __construct($mode=0,$db_settings)
   {
    self::$_instance = $this;
   self::$db_settings = $db_settings;
    
    switch(self::$db_settings['type'])
     {
      case 'sqlite':
       if($mode==0)
        {
         self::$content = new PDO('sqlite:'.self::$db_settings['db_content_file']);
         self::$entries = new PDO('sqlite:'.self::$db_settings['db_entry_file']);
         self::$content->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         self::$entries->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
       if($mode==1)
        {
         self::$content = new PDO('sqlite:'.self::$db_settings['db_content_file']);
         self::$entries = new PDO('sqlite:'.self::$db_settings['db_entry_file']);
         self::$userdata = new PDO('sqlite:'.self::$db_settings['db_userdata_file']);
         self::$content->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         self::$entries->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
         self::$userdata->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
       break;
      case 'sqlite2':
       if($mode==0)
        {
         self::$content = new PDO('sqlite2:'.self::$db_settings['db_content_file']);
         self::$entries = new PDO('sqlite2:'.self::$db_settings['db_entry_file']);
        }
       if($mode==1)
        {
         self::$content = new PDO('sqlite2:../'.self::$db_settings['db_content_file']);
         self::$entries = new PDO('sqlite2:../'.self::$db_settings['db_entry_file']);
         self::$userdata = new PDO('sqlite2:../'.self::$db_settings['db_userdata_file']);
        }
       break;
      case 'mysql':
       self::$complete = new PDO('mysql:host='.self::$db_settings['host'].';dbname='.self::$db_settings['database'], self::$db_settings['user'], self::$db_settings['password']);
       self::$complete->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
       self::$complete->query("set names utf8");
       self::$content =& self::$complete;
       self::$entries =& self::$complete;
       if($mode==1)
        {
         self::$userdata =& self::$complete;
        }
       break;
    default:
       ?><p>Database type not supported.</p><?php
       exit;
     }
   }

  public static function getInstance()
   {
    return self::$_instance;
   }
 }
?>
