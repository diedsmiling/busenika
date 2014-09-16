{* $Id: barcode.tpl 9578 2010-05-24 07:02:51Z  $ *}

<img src="{"image.barcode.draw?id=`$id`&amp;type=`$addons.barcode.type`&amp;width=`$addons.barcode.width`&amp;height=`$addons.barcode.height`&amp;xres=`$addons.barcode.resolution`&amp;font=`$addons.barcode.text_font`"|fn_url}" alt="BarCode" width="{$addons.barcode.width}" height="{$addons.barcode.height}" />
