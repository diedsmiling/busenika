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
                $this->objPHPExcel = $this->objReader->load(DIR_SYNC_VENDORS .$this ->config['price-sheets'][$this->currentDocument]['file-name']);
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
        if (!isset($returnData['item'])) //end of file reached
        {
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