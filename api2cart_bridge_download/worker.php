<?php
defined('ABSPATH') or die("Cannot access pages directly.");

class API2CartWorker
{
  var $root = '';
  var $a2cBridgePath = '';
  var $errorMessage = '';
  var $configFilePath = '/config.php';
  var $api2cartBridgePath = 'http://beta-api.api2cart.com/v1.0/bridge.download.file';

  public function __construct()
  {
    $this->root = ABSPATH;
    $this->a2cBridgePath = $this->root . '/bridge2cart';
  }

  /**
   * @return bool
   */
  public function isBridgeExist()
  {
    if (is_dir($this->a2cBridgePath)
      && file_exists($this->a2cBridgePath . '/bridge.php')
      && file_exists($this->a2cBridgePath . '/config.php')
    ) {
      return true;
    }

    return false;
  }

  /**
   * @return bool
   */
  public function installBridge()
  {
    if ($this->isBridgeExist()) {
      return true;
    }

    file_put_contents("bridge.zip", file_get_contents($this->api2cartBridgePath));
    $zip = new ZipArchive;

    $res = $zip->open("bridge.zip");
    if ($res === true) {
      $zip->extractTo(dirname(__FILE__));
      $zip->close();
    }

    $res =  $this->xcopy(dirname(__FILE__) . '/bridge2cart/', $this->root . '/bridge2cart/');
    $this->deleteDir(dirname(__FILE__) . '/bridge2cart/');

    return $res;
  }

  /**
   * @return bool
   */
  public function unInstallBridge()
  {
    if (!$this->isBridgeExist()) {
      return true;
    }

    return $this->deleteDir($this->a2cBridgePath);
  }

  /**
   * @param $token
   *
   * @return bool
   */
  public function updateToken($token)
  {
    $config = @fopen($this->a2cBridgePath . $this->configFilePath, 'w');
    $writed = fwrite($config, "<?php define('M1_TOKEN', '" . $token . "');");

    if (($config === false) || ($writed === false) || (fclose($config) === false)) {
      $this->errorMessage .= ' Could not update token';
      return false;
    }

    return true;
  }

  /**
   * @param $dirPath
   *
   * @return bool
   */
  private function deleteDir($dirPath)
  {
    if (is_dir($dirPath)) {
      $objects = scandir($dirPath);

      foreach ($objects as $object) {
        if ($object != "." && $object != "..") {
          if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
            $this->deleteDir($dirPath . DIRECTORY_SEPARATOR . $object);
          } elseif (!unlink($dirPath . DIRECTORY_SEPARATOR . $object)) {
            return false;
          }
        }
      }

      reset($objects);

      if (!rmdir($dirPath)) {
        return false;
      }
    } else {
      return false;
    }

    return true;
  }

  /**
   * @param $src
   * @param $dst
   *
   * @return bool
   */
  private function xcopy($src, $dst)
  {
    $dir = opendir($src);

    if (!$dir || !mkdir($dst)) {
      return false;
    }

    while (false !== ($file = readdir($dir))) {
      if (($file != '.') && ($file != '..')) {
        if (is_dir($src . '/' . $file)) {
          $this->xcopy($src . '/' . $file, $dst . '/' . $file);
        } elseif (!copy($src . '/' . $file, $dst . '/' . $file)) {
          $this->deleteDir($dst);
          return false;
        }

        chmod($dst . $file, 0755);
        chmod($dst, 0755);
      }
    }

    closedir($dir);
    return true;
  }

}
