<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 04.02.15
 * Time: 17:24
 */
include_once("MasterData.php");
include_once("Vendor.php");
include_once("IBaseDataProvider.php");
include_once("ExcelBaseData.php");
include_once("DBBaseData.php");


class SyncVendor {
    private $config;
    private $masterData;
    private $baseData;

    public function __construct(){
        $this->config = json_decode(file_get_contents(DIR_SYNC_VENDORS . "config_test.json"), true);
        $this->masterData = new MasterData($this->config);
        $this->baseData = self::getBaseDataProvider($this->config);
        $this->masterData->setBaseDataProvider($this->baseData);
    }

    public function run(){
        foreach ($this->config['vendors'] as $vendorConfig)
        {
            $vendor = new Vendor($vendorConfig);
            $this->masterData->importData($vendor);
            //break;//only one vendor for now
        }
        $this->masterData->saveFile();
    }

    public static function getBaseDataProvider($config){
        switch ($config['base-data']){
            case 'excel':
                return new ExcelBaseData($config['base-file']);
                break;
            case 'db':
                return new DBBaseData($config);
                break;
        }
    }
}
