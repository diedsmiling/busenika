<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 04.02.15
 * Time: 17:52
 */

class Vendor {

    private $config;
    private $currentDocument = -1;
    private $currentLine = 1;
    private $lastRow;
    private $endReached = false;
    private $endOfFile = true;
    private $objPHPExcel;

    public function __construct($config)
    {
        $this->config = $config;
    }

    public function getNextLine()
    {
        if ($this->endOfFile){
            if (isset($this->config['price-sheets'][$this->currentDocument+1])){
                $this->currentDocument++;
                SyncVendor::log("Loading " . $this->config['price-sheets'][$this->currentDocument]['file-name'] . "...");
                if (file_exists(DIR_PRICE_SHEETS_FOLDER . $this->config['price-sheets'][$this->currentDocument]['file-name']))
                {
                    $this->objPHPExcel = PHPExcel_IOFactory::load(DIR_PRICE_SHEETS_FOLDER . $this->config['price-sheets'][$this->currentDocument]['file-name']);
                    SyncVendor::log("Loading done. Parsing ...");
                    $this->lastRow = $this->objPHPExcel->getActiveSheet()->getHighestRow();
                    $this->currentLine = $this->config['price-sheets'][$this->currentDocument]['first-row'];
                    $this->endOfFile = false;
                }
                else
                {
                    SyncVendor::log("WARNING: File " . $this->config['price-sheets'][$this->currentDocument]['file-name'] . " not found");
                    $this->endOfFile = true;
                    return $this->getNextLine();
                }
            } else {
                $this->endReached = true;
                return false;
            }
        }

        $returnData = array();
        $itemColumn = $this->config['price-sheets'][$this->currentDocument]['item-column'];
        $priceColumn = $this->config['price-sheets'][$this->currentDocument]['price-column'];
        $qtyColumn = $this->config['price-sheets'][$this->currentDocument]['qty-column'];
        $returnData['item'] = $this->objPHPExcel->getActiveSheet()->getCell($itemColumn . $this->currentLine)->getValue();
        if (isset($this->config['price-sheets'][$this->currentDocument]['separator'])){
            $returnData['item'] = explode($this->config['price-sheets'][$this->currentDocument]['separator'], $returnData['item']);
            $returnData['item'] = $returnData['item'][0];
        }
        $returnData['price'] = $this->objPHPExcel->getActiveSheet()->getCell($priceColumn . $this->currentLine)->getValue() * (100 - $this->config['discount'])/100;
        $returnData['qty'] = (int)$this->objPHPExcel->getActiveSheet()->getCell($qtyColumn . $this->currentLine)->getCalculatedValue();
        if (!$returnData['qty']) $returnData['qty'] = 1;
        if ($this->currentLine > $this->lastRow) //end of file reached
        {
            SyncVendor::log("File " . $this->config['price-sheets'][$this->currentDocument]['file-name'] . " parsed. " . $this->currentLine . " lines.");
            unset($this->objPHPExcel);
            $this->endOfFile = true;
            return $this->getNextLine();
        }
        $this->currentLine++;
        return $returnData;
    }

    public function endReached()
    {
        return $this->endReached;
    }

    public function getActiveSheet()
    {
        return isset($this->config['base-active-sheet']) ? $this->config['base-active-sheet'] : 0;
    }

    public function getBaseDataColumn()
    {
        $column['base-data-column'] = $this->config['base-data-column'];
        $column['base-data-column-name'] = $this->config['base-data-column-name'];
        return $column;
    }

    public function getConfig()
    {
        return $this->config;
    }
}