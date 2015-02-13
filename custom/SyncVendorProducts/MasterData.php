<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 05.02.15
 * Time: 8:52
 */

class MasterData {
    private $excel;
    private $config;
    private $baseData;
    private $counter = 1;
    private  $currentVendor;
    public function __construct($config)
    {
        $this->config = $config;
        $this->excel = new PHPExcel();
        $this->excel->getProperties()->setCreator("Lazarev Maxim")
                    ->setLastModifiedBy("Lazarev Maxim")
                    ->setTitle("Vendor Product Compilation")
                    ->setSubject("Vendor Product Compilation")
                    ->setDescription("Compilation of prices for different vendors.")
                    ->setKeywords("vendor prices products compilation sync")
                    ->setCategory("Products");
    }

    public function importData(Vendor $vendor)
    {
        $this->currentVendor = $vendor;
        $baseActiveSheet = $vendor->getActiveSheet();
        while (!$vendor->endReached())
        {
            $vendorItemData = $vendor->getNextLine();
            if (isset($vendorItemData['item']))
            {
                $itemIds = $this->baseData->getItemIds($vendorItemData['item'], $vendor->getBaseDataColumn());
                if (!empty($itemIds)){
                    foreach($itemIds as $itemId)
                    {
                        $itemData = $this->findItem($itemId);
                        if (!isset($itemData['row']))
                        {
                            $itemData['row'] = $this->counter;
                            $this->counter++;
                        }
                        else
                        {
                            $vendorItemData['price'] = ($vendorItemData['price'] > $itemData['price']) ? $vendorItemData['price'] : $itemData['price'];
                            $vendorItemData['qty'] += $itemData['qty'];
                        }
                        $this->excel->setActiveSheetIndex($baseActiveSheet)->setCellValue('A' . $itemData['row'], $itemId);
                        $this->excel->setActiveSheetIndex($baseActiveSheet)->setCellValue($vendor->getConfig()['master-file-item-column'] . $itemData['row'], $vendorItemData['item']);
                        $this->excel->setActiveSheetIndex($baseActiveSheet)->setCellValue($vendor->getConfig()['master-file-price-column'] . $itemData['row'], $vendorItemData['price']);
                        $this->excel->setActiveSheetIndex($baseActiveSheet)->setCellValue($vendor->getConfig()['master-file-qty-column'] . $itemData['row'], $vendorItemData['qty']);
                    }
                }
            }
        }
    }

    public function saveFile()
    {
        $objWriter = PHPExcel_IOFactory::createWriter($this->excel, 'Excel5');
        $objWriter->save(DIR_SYNC_VENDORS . $this->config['price-sheets-folder'] . "/" . $this->config['master-file']['name']);

    }

    public function setBaseDataProvider(IBaseDataProvider $baseDataProvider)
    {
        $this->baseData = $baseDataProvider;
    }

    private function findItem($itemId)
    {
        $lastRow = $this->excel->getActiveSheet()->getHighestRow();
        for ($row = 1; $row <= $lastRow; $row++) {
            $cell = $this->excel->getActiveSheet()->getCell('A'.$row)->getValue();
            if ($cell == $itemId)
            {
                $itemData = array();
                $itemData['row'] = $row;
                $itemData['item'] = $itemId;
                $itemData['price'] = $this->excel->getActiveSheet()->getCell($this->currentVendor->getConfig()['master-file-price-column'].$row)->getValue();
                $itemData['qty'] = $this->excel->getActiveSheet()->getCell($this->currentVendor->getConfig()['master-file-qty-column'].$row)->getValue();
                return $itemData;
            }
        }
    }
}