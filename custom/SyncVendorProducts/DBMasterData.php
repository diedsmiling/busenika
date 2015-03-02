<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 22.02.15
 * Time: 18:28
 */

class DBMasterData implements IMasterData{

    private $config;
    private $baseData;

    function __construct($config)
    {
        $this->config = $config;
        if ($this->config['base-recreate-table'] == true)
        {
            db_query("DROP TABLE IF EXISTS " . $this->config['master-table']);
            $query = "CREATE TABLE " . $this->config['master-table'] . " ( item_id VARCHAR(50) NOT NULL PRIMARY KEY, " ;
            foreach ($this->config['vendors'] as $vendor)
            {
                $query .= $vendor['master-file-item-column-name'] . " VARCHAR(50) NOT NULL,";
                $query .= $vendor['master-file-price-column-name'] . " DECIMAL(12,2) NOT NULL DEFAULT 0.00,";
                $query .= $vendor['master-file-qty-column-name'] . " MEDIUMINT(8) NOT NULL DEFAULT 0,";

            }
            $query = rtrim($query, ",");
            $query .= ") ENGINE=InnoDB   DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;";
            db_query($query);

        }
    }

    public function importData(Vendor $vendor)
    {
        $vendorConfig = $vendor->getConfig();
        while (!$vendor->endReached())
        {
            $vendorItemData = $vendor->getNextLine();
            if (isset($vendorItemData['item']) && $vendorItemData['item'] != "")
            {
                $itemIds = $this->baseData->getItemIds($vendorItemData['item'], $vendor->getBaseDataColumn());
                if (!empty($itemIds)){
                    foreach($itemIds as $itemId)
                    {
                        $query = "INSERT INTO " . $this->config['master-table'] . " (" .
                            "item_id," .
                            $vendorConfig['master-file-item-column-name'] ."," .
                            $vendorConfig['master-file-price-column-name'] ."," .
                            $vendorConfig['master-file-qty-column-name'] . ") VALUES (" .
                            "'" . $itemId . "'," .
                            "'". $vendorItemData['item'] . "'," .
                            $vendorItemData['price'] . "," .
                            $vendorItemData['qty'] . ") ON DUPLICATE KEY UPDATE " .
                            $vendorConfig['master-file-item-column-name'] . "='" . $vendorItemData['item'] . "'," .
                            $vendorConfig['master-file-price-column-name'] . "= IF (" . $vendorConfig['master-file-price-column-name'] . "<" . $vendorItemData['price'] . "," . $vendorItemData['price'] . "," . $vendorConfig['master-file-price-column-name'] . ")," .
                            $vendorConfig['master-file-qty-column-name'] . "=" . $vendorConfig['master-file-qty-column-name'] ."+" . $vendorItemData['qty'];
                        db_query($query);
                    }
                }
            }
        }
    }

    public function finish()
    {
        // TODO: Implement finish() method.
    }

    public function setBaseDataProvider(IBaseDataProvider $baseDataProvider)
    {
        $this->baseData = $baseDataProvider;
    }
}