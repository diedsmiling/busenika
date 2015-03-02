<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 22.02.15
 * Time: 18:19
 */

interface IMasterData {
    public function importData(Vendor $vendor);
    public function finish();
    public function setBaseDataProvider(IBaseDataProvider $baseDataProvider);
}