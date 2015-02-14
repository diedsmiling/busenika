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
        $sheetname = $worksheetList[2];
        $this->objReader->setLoadSheetsOnly($sheetname);
        $this->objReader->setReadDataOnly(true);
        SyncVendor::log("Begin loading " . $this->fileName . " base file.");
        $this->objPHPExcel = $this->objReader->load($this->fileName);
        SyncVendor::log("Base excel file loaded.");
    }

    //note - $column is an array with 2 members: base-data-column, base-data-column-name
    public function getItemIds($vendorItemId, $column)
    {
        $itemIds = array();
        $lastRow = $this->objPHPExcel->getActiveSheet()->getHighestRow();
        for ($row = 2; $row <= $lastRow; $row++) {
            $cell = $this->objPHPExcel->getActiveSheet()->getCell($column['base-data-column'].$row)->getValue();
            if ($cell == $vendorItemId)
            {
                $itemIds[] = $this->objPHPExcel->getActiveSheet()->getCell("A".$row)->getValue();
            }
        }
        return $itemIds;
    }

} 