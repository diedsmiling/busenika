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
include_once("PriceUpdater.php");


class SyncVendor {
    private $config;
    private $masterData;
    private $baseData;

    public function __construct(){
        self::clearLog();
        self::log('Sync Vendor Prices started');
        $this->config = json_decode(file_get_contents(SYNC_VENDORS_CONFIG), true);
        $this->masterData = new MasterData($this->config);
        $this->baseData = self::getBaseDataProvider($this->config);
        $this->masterData->setBaseDataProvider($this->baseData);
        define("DIR_PRICE_SHEETS_FOLDER", DIR_SYNC_VENDORS . $this->config['price-sheets-folder']);
    }

    public function run(){
        foreach ($this->config['vendors'] as $vendorConfig)
        {
            $vendor = new Vendor($vendorConfig);
            $this->masterData->importData($vendor);
            //break;//only one vendor for now
        }
        $this->masterData->saveFile();
        self::log('Sync Vendor Prices ended');
        self::log('Updating prices ...');
        $updater = new PriceUpdater($this->config);
        $updater->updatePrices();
        self::log('Finished updating prices.');


    }

    public static function getBaseDataProvider($config){
        switch ($config['base-data']){
            case 'excel':
                return new ExcelBaseData(DIR_SYNC_VENDORS . $config['base-file']);
                break;
            case 'db':
                return new DBBaseData($config);
                break;
        }
    }

    public static function log($message, $lineFeed = true)
    {
        $endLine = $lineFeed ? "\r\n" : "";
        $date = !$lineFeed ? date("H:i:s") : "";
        file_put_contents(DIR_SYNC_VENDORS . "syncVendor.log", $date . ":" . $message . $endLine, FILE_APPEND);
    }

    private static function clearLog()
    {
        file_put_contents(DIR_SYNC_VENDORS . "syncVendor.log", "");
    }
}
