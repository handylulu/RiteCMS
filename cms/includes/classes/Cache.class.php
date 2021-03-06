<?php
class Cache
 {
  public $doCaching = true;
  public $autoClear = true;
  public $cacheId = false;
  private $_cacheDir;
  private $_settings;

  public function __construct($cacheDir, $settings)
   {
    $this->_cacheDir = $cacheDir;
    $this->_settings = $settings;
   }

  public function createCacheContent($content, $content_type, $charset)
   {
    $cacheContent = '<?php
header(\'Last-Modified: ' . gmdate("D, d M Y H:i:s",time()) . ' GMT\');
header(\'Cache-Control: public\');
if(isset($_SERVER[\'HTTP_IF_MODIFIED_SINCE\']) && '.time().' <= strtotime($_SERVER[\'HTTP_IF_MODIFIED_SINCE\']))
{
header(\'HTTP/1.1 304 Not Modified\');
exit;
}
else
{
';
    if(function_exists('xgzencode')) $cacheContent .= 'if(isset($_SERVER[\'HTTP_ACCEPT_ENCODING\']) && strpos($_SERVER[\'HTTP_ACCEPT_ENCODING\'], \'gzip\')!==false)
{
header(\'Content-Encoding: gzip\');
header(\'Content-Type: '.$content_type.'; charset='.$charset.'\'); ?'.'>'.gzencode($content, 9).'<?php
}
else
{
header(\'Content-Type: '.$content_type.'; charset='.$charset.'\'); ?'.'>'.$content.'<?php
}';
    else $cacheContent .= 'header(\'Content-Type: '.$content_type.'; charset='.$charset.'\'); ?'.'>'.$content.'<?php
';
$cacheContent .= '}
?>';
    return $cacheContent;
   }

  public function createChacheFile($content)
   {
    if($this->cacheId && $this->doCaching)
     {
      $cacheFile = $this->_cacheDir . rawurlencode(strtolower($this->cacheId)).'.cache';
      if(!file_exists($cacheFile))
       {
        $content = str_replace('<?xml','<?php echo \'<?xml\'; ?>', $content);
        $fp = @fopen($cacheFile, 'w');
        @flock($fp, 2);
        @fwrite($fp, $content);
        @flock($fp, 3);
        @fclose($fp);
       }
     }

   if(!file_exists($this->_cacheDir.'settings.php'))
    {
     $this->_createCacheSettingsFile();
    }
   }

  private function _createCacheSettingsFile()
   {
    $content  = "<?php\n";
    $content .= '$settings[\'session_prefix\'] = \''.$this->_settings['session_prefix'].'\';'."\n";
    $content .= '$settings[\'index_page\'] = \''.$this->_settings['index_page'].'\';'."\n";
    $content .= '?'.'>';
    $fp = @fopen($this->_cacheDir.'settings.php', 'w');
    @flock($fp, 2);
    @fwrite($fp, $content);
    @flock($fp, 3);
    @fclose($fp);
   }

  public function clear($page=false)
   {
    if(!$page)
     {
      // delete all cache files (settings.php and *.cache):
      foreach(glob($this->_cacheDir.'{settings.php,*.cache}', GLOB_BRACE) as $cacheFile)
       {
        @unlink($cacheFile);
       }
     }
    else
     {
      // delete cache files of a specifid page:
      $page = rawurlencode(strtolower($page));
      // select page.cache and page,*.cahe
      foreach(glob($this->_cacheDir.'{'.$page.'.cache,'.$page.'%2C*.cache}', GLOB_BRACE) as $cacheFile) // "%2C" = ","
       {
        @unlink($cacheFile);
       }
     }
   }

  public function clearPhoto($id)
   {
    // select *,photo,[id].cache and *,photo,[id],*.cache
    foreach(glob($this->_cacheDir.'{*%2C'.IMAGE_IDENTIFIER.'%2C'.$id.'.cache,*%2C'.IMAGE_IDENTIFIER.'%2C'.$id.'%2C*.cache}', GLOB_BRACE) as $cacheFile)
     {
      @unlink($cacheFile);
     }
   }

  function clearRelated($page)
   {
    $dbr = Database::$content->prepare("SELECT include_page FROM ".Database::$db_settings['pages_table']." WHERE lower(page)=lower(:page) LIMIT 1");
    $dbr->bindParam(':page', $page, PDO::PARAM_STR);
    $dbr->execute();
    $data = $dbr->fetch();
    if(isset($data['include_page']))
     {
      $dbr2 = Database::$content->prepare("SELECT page, type FROM ".Database::$db_settings['pages_table']." WHERE id=:id LIMIT 1");
      $dbr2->bindParam(':id', $data['include_page'], PDO::PARAM_INT);
      $dbr2->execute();
      $data2 = $dbr2->fetch();
      if(isset($data2['page']))
       {
          $this->clear($data2['page']);
       }
     }
   }
 }
?>
