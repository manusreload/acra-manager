<?php
class App
{
    var $name, $appId, $secureId, $author, $pushes, $pkg, $error_pkg, $push, $notify;
    var $core;
    
    
    public function __construct() {
        $this->core = new CDMCore();
    }
   
    public function setData($arr) {
        $this->name = $arr['name'];
        $this->appId = $arr['appId'];
        $this->secureId = $arr['secureId'];
        $this->author = $arr['author'];
        $this->pushes = $arr['pushes'];
        $this->push = $arr['push'];
        $this->pkg = $arr['pkg'];
        $this->notify = $arr['notify'];
        if($arr['error_pkg'] == "")
        {
            $this->error_pkg = $arr['pkg'];
        }
        else
        {
            $this->error_pkg = $arr['error_pkg'];
        }
    }
    
    public function  getName()
    {
        return $this->name;
    }

    public function getAppId()
    {
        return $this->appId;
    }
    public function getSecureId()
    {
        return $this->secureId;
    }
    public function getNotify()
    {
        return $this->notify;
    }
    
    public function listVersions($pkg)
    {
        $res = $this->core->getVersions($pkg);
        $ret = array();
        while($row = mysql_fetch_array($res))
        {
            $version['version'] = $row[1];
            $version['vercode'] = $row[0];
            $version['count'] = $row[2];
            $ret[] = $version;
        }
        return $ret;
    }
    
    public function listBackups()
    {
        return $this->core->getBackups($this->appId);
    }
    
    public function saveBackup($date)
    {
        $this->core->insertBackup($this->appId, $date);
    }
    
    public function download_backup($time, $item)
    {
        $folder = "backups/" . $this->getAppId();
        
        $date = date("ymd-H:i", $time);
        if($item == "filesystem")
        {
            $file = $folder . "/filesystem-" . $date . ".zip";
        }
        else
        {
            $file = $folder . "/sql-" . $date . ".sql";
        }
        echo $date;
        if(file_exists($file))
        {
            header('Content-Description: ' . basename($file));
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename=\"' . basename($file) . "\"");
            header('Content-Transfer-Encoding: binary');
            header('Expires: 0');
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            ob_clean();
            flush();
            readfile($file);
        }
        return false;
        
        
    }
}
?>
