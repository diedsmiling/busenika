<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 04.02.15
 * Time: 17:52
 */
include_once("FileSupplier.php");


class Vendor {

    private $config;
    private $currentDocument = -1;
    private $currentLine = 1;
    private $lastRow;
    private $endReached = false;
    private $endOfFile = true;
    private $objPHPExcel;
    private $fileSupplier;
    private $currentFile = "";
    private $currentSheetConfig;

    public function __construct($config)
    {
        $this->config = $config;
        $this->fileSupplier = new FileSupplier($this->config);
    }

    public function getNextLine()
    {
        if ($this->endOfFile){
            if (!$this->fileSupplier->endReached()){
                $this->currentSheetConfig = $this->fileSupplier->getCurrentSheetConfig();
                $this->currentFile = $this->fileSupplier->nextFile();
                SyncVendor::log("Loading " . pathinfo($this->currentFile, PATHINFO_BASENAME) . "...");
                if (file_exists($this->currentFile))
                {
                    $this->objPHPExcel = PHPExcel_IOFactory::load($this->currentFile);
                    SyncVendor::log("Loading done. Parsing ...");
                    $this->lastRow = $this->objPHPExcel->getActiveSheet()->getHighestRow();
                    $this->currentLine = $this->currentSheetConfig['first-row'];
                    $this->endOfFile = false;
                }
                else
                {
                    SyncVendor::log("WARNING: File " . pathinfo($this->currentFile, PATHINFO_BASENAME) . " not found");
                    $this->endOfFile = true;
                    return $this->getNextLine();
                }
            } else {
                $this->endReached = true;
                return false;
            }
        }

        $returnData = array();
        $itemColumn = $this->currentSheetConfig['item-column'];
        $priceColumn = $this->currentSheetConfig['price-column'];
        $qtyColumn = $this->currentSheetConfig['qty-column'];
        $returnData['item'] = $this->objPHPExcel->getActiveSheet()->getCell($itemColumn . $this->currentLine)->getValue();
        if (isset($this->currentSheetConfig['separator'])){
            $returnData['item'] = explode($this->currentSheetConfig['separator'], $returnData['item']);
            $returnData['item'] = $returnData['item'][0];
        }
        $returnData['price'] = $this->objPHPExcel->getActiveSheet()->getCell($priceColumn . $this->currentLine)->getValue() * (100 - $this->config['discount'])/100;
        $returnData['qty'] = (int)$this->objPHPExcel->getActiveSheet()->getCell($qtyColumn . $this->currentLine)->getCalculatedValue();
        if (!$returnData['qty']) $returnData['qty'] = 1;
        if ($this->currentLine > $this->lastRow) //end of file reached
        {
            SyncVendor::log("File " . $this->currentFile . " parsed. " . $this->currentLine . " lines.");
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