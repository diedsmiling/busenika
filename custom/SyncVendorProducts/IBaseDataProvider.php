<?php
/**
 * Created by PhpStorm.
 * User: Максим
 * Date: 10.02.15
 * Time: 19:21
 */

interface IBaseDataProvider {
    public function getItemIds($vendorItemId, $column);
} 