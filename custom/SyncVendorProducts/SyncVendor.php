<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 04.02.15
 * Time: 17:24
 */
include_once("IBaseDataProvider.php");
include_once("IMasterData.php");
include_once("ExcelMasterData.php");
include_once("Vendor.php");
include_once("ExcelBaseData.php");
include_once("ExcelMasterData.php");
include_once("DBBaseData.php");
include_once("DBMasterData.php");
include_once("PriceUpdater.php");


class SyncVendor {
    private $config;
    private $masterData;
    private $baseData;

    public function __construct(){
        fn_start_scroller();
        self::clearLog();
        self::log('Sync Vendor Prices started');
        $this->config = json_decode(file_get_contents(SYNC_VENDORS_CONFIG), true);
        $this->masterData = self::getMasterDataProvider($this->config);
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
        $this->masterData->finish();
        self::log('Sync Vendor Prices ended.');
        self::log('Updating prices ...');
        $updater = new PriceUpdater($this->config);
        $updater->updatePrices();
        self::log('Finished updating prices.');
        fn_stop_scroller();
    }

    public static function getBaseDataProvider($config){
        switch ($config['base-data']){
            case 'excel':
                return new ExcelBaseData($config);
                break;
            case 'db':
                return new DBBaseData($config);
                break;
        }
    }
    public static function getMasterDataProvider($config){
        switch ($config['master-data-type']){
            case 'excel':
                return new ExcelMasterData(DIR_SYNC_VENDORS . $config['base-file']);
                break;
            case 'db':
                return new DBMasterData($config);
                break;
        }
    }

    public function downloadSheets(){
        self::log('Download started.');
        foreach($this->config['vendors'] as $vendorKey => $vendor){
            foreach ($vendor['price-sheets'] as $priceSheet)
            {
                if ($priceSheet['download'] == 'url')
                {
                    // Handle souzplastic vendor
                    if ($vendorKey == 'souzplastic')
                    {
                        $priceSheet['file-name'] = $this->adjustSheetNameForSouz($priceSheet['file-name'], 0);
                        $priceSheet['url'] = $this->adjustSheetNameForSouz($priceSheet['url'], 0);
                        for ($i=1; $i<=7; $i++)
                        {
                            $try = $this->downloadSheet($priceSheet['url'], $priceSheet['file-name']);
                            if (!$try)
                            {
                                $priceSheet['file-name'] = $this->adjustSheetNameForSouz($priceSheet['file-name'], $i);
                                $priceSheet['url'] = $this->adjustSheetNameForSouz($priceSheet['url'], $i);
                            }
                            else
                            {
                                $this->config['vendors']['souzplastic']['price-sheets'][0]['file-name'] = $priceSheet['file-name'];
                                $this->config['vendors']['souzplastic']['price-sheets'][0]['url'] = $priceSheet['url'];
                                file_put_contents(SYNC_VENDORS_CONFIG, json_encode($this->config/*, JSON_PRETTY_PRINT*/));
                                break;
                            }
                        }

                    }
                    // Handle all other normal vendors
                    else
                    {
                        $result = $this->downloadSheet($priceSheet['url'], $priceSheet['file-name']);
                        if (!isset($result))
                        {
                            if (is_readable(DIR_PRICE_SHEETS_FOLDER . $priceSheet['file-name']))
                            {
                                self::log(' Using old file.');
                            }
                            else
                            {
                                self::log(' File skipped.');
                            }
                        }
                    }
                }
                else if (($priceSheet['download'] == 'file') && !is_readable(DIR_PRICE_SHEETS_FOLDER . $priceSheet['file-name']))
                {
                    self::log('WARNING: File ' . $priceSheet['file-name'] . ' not found');
                }
                else
                {
                    self::log('Using file ' . $priceSheet['file-name'] . ' on server.');
                }
            }
        }
        self::log('Download ended.');
    }

    private function adjustSheetNameForSouz($fileName, $offset)
    {
        $result = substr($fileName, 0, -8);
        $result .= date("dm", strtotime("-" . $offset . " days")) . ".xls";
        return $result;
    }

    private function downloadSheet($url, $file)
    {
        $download = fopen($url, 'r');
        if (!$download)
        {
            self::log('WARNING: Failed to download ' . $file . ' using ' . $url);
            return false;
        }
        else
        {
            $result = file_put_contents(DIR_PRICE_SHEETS_FOLDER . $file, $download);
            self::log('File ' . $file . ' downloaded (' . $result . ' bytes)');
            return $result;
        }
    }

    public static function log($message, $lineFeed = true)
    {

        $endLine = $lineFeed ? "\r\n" : "";
        $date = $lineFeed ? date("H:i:s") : "";
        fn_echo($message . "<br>");
        file_put_contents(DIR_SYNC_VENDORS . "syncVendor.log", $endLine .  $date . ":" . $message, FILE_APPEND);
    }

    private static function clearLog()
    {
        file_put_contents(DIR_SYNC_VENDORS . "syncVendor.log", "");
    }
}
