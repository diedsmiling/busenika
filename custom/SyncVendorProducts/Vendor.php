<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 04.02.15
 * Time: 17:52
 */
include_once "ChunkReadFilter.php";


class Vendor {

    private $config;
    private $currentDocument = -1;
    private $currentLine = 1;
    private $lastRow;
    private $endReached = false;
    private $endOfFile = true;
    private $objReader;
    private $objPHPExcel;

    public function __construct($config)
    {
        $this->config = $config;
        $inputFileType = 'Excel5';

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $this->objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $this->objReader->setReadDataOnly(true);
    }

    public function getNextLine()
    {
        if ($this->endOfFile){
            if (isset($this->config['price-sheets'][$this->currentDocument+1])){
                $this->currentDocument++;
                SyncVendor::log("Loading " . $this->config['price-sheets'][$this->currentDocument]['file-name'] . "  ...", false);
                $this->objPHPExcel = $this->objReader->load(DIR_PRICE_SHEETS_FOLDER . $this->config['price-sheets'][$this->currentDocument]['file-name']);
                SyncVendor::log(" done.");

                $this->lastRow = $this->objPHPExcel->getActiveSheet()->getHighestRow();
                $this->currentLine = $this->config['price-sheets'][$this->currentDocument]['first-row'];
                $this->endOfFile = false;
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
        $returnData['price'] = $this->objPHPExcel->getActiveSheet()->getCell($priceColumn . $this->currentLine)->getValue();
        $returnData['qty'] = $this->objPHPExcel->getActiveSheet()->getCell($qtyColumn . $this->currentLine)->getValue();
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