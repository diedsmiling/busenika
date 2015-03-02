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
    private $updateType;
    public function __construct($config)
    {
        $this->config = $config;
        $this->updateType = $config['master-data-type'];
        $inputFileType = 'Excel5';

        if ($this->updateType == 'excel')
        {
            /**  Create a new Reader of the type defined in $inputFileType  **/
            $objReader = PHPExcel_IOFactory::createReader($inputFileType);
            $objReader->setReadDataOnly(true);
            $this->excel = $objReader->load(DIR_PRICE_SHEETS_FOLDER . $this->config['master-file']['name']);
        }
    }

    public function updatePrices()
    {
        if ($this->updateType == 'excel')
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
        elseif ($this->updateType == 'db')
        {
            foreach($this->config['vendors'] as $vendor)
            {
                $id_columns[] = $vendor['master-file-item-column-name'];
                $price_columns[] = $vendor['master-file-price-column-name'];
                $qty_columns[] = $vendor['master-file-qty-column-name'];
            }
            db_get_array(
                "UPDATE cscart_products, vendor_prices, vendor_items SET temp_price=GREATEST(0, " . implode(",",$price_columns) . ") * (vendor_items.interest/100 + 1), temp_qty=". implode("+", $qty_columns) ." WHERE product_code= vendor_prices.item_id AND vendor_items.item_id=product_code"
            );
        }

    }

} 