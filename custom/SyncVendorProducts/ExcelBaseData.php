<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 09.02.15
 * Time: 21:10
 */

class ExcelBaseData implements IBaseDataProvider{

    private $fileName;
    private $objReader;
    public $objPHPExcel;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
        $inputFileType = 'Excel2007';

        /**  Create a new Reader of the type defined in $inputFileType  **/
        $this->objReader = PHPExcel_IOFactory::createReader($inputFileType);
        $worksheetList = $this->objReader->listWorksheetNames($this->fileName);
        $sheets = [$worksheetList[2], $worksheetList[3]];
        $this->objReader->setLoadSheetsOnly($sheets);
        $this->objReader->setReadDataOnly(true);
        SyncVendor::log("Begin loading " . $this->fileName . " base file.");
        $this->objPHPExcel = $this->objReader->load($this->fileName);
        SyncVendor::log("Base excel file loaded.");
    }

    //note - $column is an array with 2 members: base-data-column, base-data-column-name
    public function getItemIds($vendorItemId, $column)
    {
        $itemIds = array();
        $lastRow = $this->objPHPExcel->getActiveSheet(0)->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $cell = $this->objPHPExcel->getActiveSheet(0)->getCell($column['base-data-column'].$row)->getValue();
            if ($cell == $vendorItemId)
            {
                $itemIds[] = $this->objPHPExcel->getActiveSheet(0)->getCell("A".$row)->getValue();
            }
        }
        return $itemIds;
    }

} 