<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 03.03.15
 * Time: 22:26
 */

class FileSupplier {
    private $vendorConfig;
    private $endReached;
    private $priceSheets;
    private $priceSheetPosition = 0;
    private $currentSheet;
    private $currentSheetConfig;
    private $filesFromRar;
    private $rarFilePosition = 0;

    public function __construct($vendorConfig){
        $this->vendorConfig = $vendorConfig;
        $this->priceSheets = $this->vendorConfig['price-sheets'];
        $this->endReached = false;
        $this->feedNextSheet();
    }

    public function nextFile()
    {
        if (pathinfo($this->currentSheet, PATHINFO_EXTENSION) == 'rar')
        {
            if (!isset($this->filesFromRar))
            {
                $this->parseFilesFromRar();
            }

            $filename = $this->filesFromRar[$this->rarFilePosition];
            $this->rarFilePosition++;

            if (!isset($this->filesFromRar[$this->rarFilePosition]))
            {
                $this->rarFilePosition = 0;
                unset($this->filesFromRar);
                $this->feedNextSheet();
            }

            if (pathinfo($filename, PATHINFO_EXTENSION) != "xls")
            {
                return $this->nextFile();
            }
            else
            {
                return DIR_PRICE_SHEETS_FOLDER . pathinfo($this->currentSheet, PATHINFO_FILENAME) . "/" .$filename;
            }
        }
        else
        {
            $filename = DIR_PRICE_SHEETS_FOLDER . $this->currentSheet;
        }

        $this->feedNextSheet();
        return $filename;
    }

    public function getCurrentSheetConfig()
    {
        return $this->currentSheetConfig;
    }

    public function endReached()
    {
        return $this->endReached;
    }

    private function feedNextSheet()
    {
        $this->currentSheetConfig = $this->priceSheets[$this->priceSheetPosition];
        $this->currentSheet = $this->currentSheetConfig['file-name'];
        $this->priceSheetPosition++;

        if (!isset($this->currentSheet))
        {
            $this->endReached = true;
        }
    }

    private function parseFilesFromRar()
    {
        $this->filesFromRar = array_values(preg_grep('/^([^.])/', scandir(DIR_PRICE_SHEETS_FOLDER . pathinfo($this->currentSheet, PATHINFO_FILENAME))));
    }
} 