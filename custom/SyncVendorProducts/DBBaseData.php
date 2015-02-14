<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 10.02.15
 * Time: 19:29
 */

class DBBaseData implements IBaseDataProvider{

    private $config;

    public function __construct($syncConfig)
    {
        $this->config = $syncConfig;
        if ($syncConfig['base-recreate-table'] == true)
        {
            SyncVendor::log("Database table creation started.");
            $columns[] = 'item_id';
            db_query("DROP TABLE IF EXISTS " . $syncConfig['base-table']);
            $query = "CREATE TABLE " . $syncConfig['base-table'] . " ( item_id VARCHAR(50) NOT NULL," ;
            foreach ($syncConfig['vendors'] as $vendor)
            {
                $query .= $vendor['base-data-column-name'] . " VARCHAR(50) NOT NULL,";
                $columns[] = $vendor['base-data-column-name'];
            }
            $query = rtrim($query, ",");
            $query .= ") ENGINE=InnoDB   DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;";
            db_query($query);

            $excelBaseData = new ExcelBaseData(DIR_SYNC_VENDORS . $syncConfig['base-file']);
            $excel = $excelBaseData->objPHPExcel;
            $lastRow = $excel->getActiveSheet()->getHighestRow();
            for ($row = 2; $row <= $lastRow; $row++) {
                $query = "INSERT INTO " . $syncConfig['base-table'] . " ";
                $values = array();
                $values['item_id'] = "'" . $excel->getActiveSheet()->getCell("A".$row)->getValue() . "'";
                foreach ($syncConfig['vendors'] as $vendor)
                {
                    $values[] = "'" . $excel->getActiveSheet()->getCell($vendor["base-data-column"].$row)->getValue() . "'";
                    //$values[] = $value ? $value : "''";
                }
                $query .= "(" . implode(",", $columns) . ") VALUES (" . implode(",", $values) . ")";
                db_query($query);
            }
            SyncVendor::log("Database table creation ended.");
        }
    }

    public function getItemIds($vendorItemId, $column)
    {
        $query = "SELECT item_id FROM " .  $this->config['base-table'] . " WHERE " . $column['base-data-column-name'] . " LIKE '%" . $vendorItemId . "%'";
        return db_get_fields($query);
    }

}