<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 19.02.15
 * Time: 23:21
 */

class PriceUpdater {
    private $config;
    private $excel;
    public function __construct($config)
    {
        $this->config = $config;
        $inputFileType = 'Excel5';

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $objReader->setReadDataOnly(true);
        $this->excel = $objReader->load(DIR_PRICE_SHEETS_FOLDER . $this->config['master-file']['name']);
    }

    public function updatePrices()
    {
        $lastRow = $this->excel->getActiveSheet()->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $itemId = $this->excel->getActiveSheet()->getCell('A'.$row)->getValue();
            $finalPrice = 0;
            $qty = 0;
            foreach($this->config['vendors'] as $vendor)
            {
                $price = $this->excel->getActiveSheet()->getCell($vendor["master-file-price-column"].$row)->getValue();
                if ($finalPrice < $price)
                    $finalPrice = $price;
                $qty += $this->excel->getActiveSheet()->getCell($vendor["master-file-qty-column"].$row)->getValue();
            }
            db_query("UPDATE ?:products, vendor_items SET temp_price= ?i * (vendor_items.interest/100 + 1), temp_qty = ?i WHERE product_code= ?s AND vendor_items.item_id= '" . $itemId . "'", $finalPrice, $qty,  $itemId);
        }
    }

} 