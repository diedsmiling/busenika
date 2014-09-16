<?php
 ini_set('max_execution_time', '320');                			
                $rDB = mysql_connect(
                    '127.0.0.1',
                    'lindero_rel',
                    'c2lVM442') or die( 'error connect to DB' );
    
    mysql_select_db('lindero_rel');
    mysql_query('set names windows-1251'); 
			function _exportToYandexMarket( $f, $rate, $export_product_name )
			{
				$spArray = array(
					'exprtUNIC'=>array(
						'mode' 				=>'toarrays',
						'expProducts' 		=>array()
						)
					);
				$exportCategories = array(array(),array());
				
				_exportBegin( $f );
				_exportAllCategories( $f, $spArray['exprtUNIC']['expProducts'] );
				_exportProducts( $f, $rate, $export_product_name, $spArray['exprtUNIC']['expProducts'] );
				_exportEnd( $f );
			}


			function _deleteHTML_Elements( $str )
			{
				$str = str_replace( "<","&lt;",		$str );
                $str = str_replace( ">","&gt;",		$str );
                $str = str_replace( "&","&amp;",	$str );
                $str = str_replace( "\"","&quot;",	$str );
                $str = str_replace( "'","&apos;",	$str );
				return $str;
			}

			function _exportBegin( $f )
			{
				fputs( $f, "<?xml version=\"1.0\" encoding=\"windows-1251\"?>\n" );
				fputs( $f, "<!DOCTYPE yml_catalog SYSTEM \"shops.dtd\">\n" );
				fputs( $f, "<yml_catalog date=\"".date("Y-m-d H:i")."\">\n" );
				fputs( $f, "    <shop>\n" );
				fputs( $f, "        <name>Lindero</name>\n");
				fputs( $f, "        <company>Lindero</company>\n");
                fputs( $f, "        <url>http://www.lindero.ru</url>\n");
				fputs( $f, "        <currencies>\n");
                fputs( $f, "			<currency id=\"RUR\" rate=\"1\" plus=\"0\"/>\n");
                fputs( $f, "		</currencies>\n");
			}


			function _exportAllCategories( $f, &$_ProductIDs )
			{
				
				$Cats = array();
				$execCats = array();
				$sql = "
					SELECT catt.category_id, cdes.category, catt.parent_id FROM cscart_categories catt
                    LEFT JOIN cscart_category_descriptions cdes on cdes.lang_code=\"RU\" and cdes.category_id=catt.category_id order by catt.parent_id
				";
				$q = mysql_query($sql);
				fputs($f,"		<categories>\n");
                while ($row = mysql_fetch_array($q))
				{
                    if (strlen($row[1])>4){ 
					if(!in_array($row[0], $execCats)){
						
						$execCats[] = $row[0];
					}
					if(!in_array($row[2], $Cats) && $row[2]>1){
						
						$Cats[] = $row[2];
					}
					$row[1] = _deleteHTML_Elements( $row[1] );
                    if ($row[2] <= 1)
					{
						fputs($f,"			<category id=\"".$row[0]."\">".$row[1]."</category>\n");
					}
                    else 
					{
						fputs($f,"			<category id=\"".$row[0]."\" parentId=\"".$row[2]."\">".$row[1]."</category>\n");
					}
                }
				}
				
				while (count($Cats)) {
					
					$sql = "						
                        SELECT catt.category_id, cdes.category, catt.parent_id FROM cscart_categories catt
                        LEFT JOIN cscart_category_descriptions cdes on cdes.lang_code=\"RU\" and cdes.category_id=catt.category_id order by catt.parent_id
						";
					$q = mysql_query($sql);
					$Cats = array();
	                while ($row = mysql_fetch_array($q))
					{
                        if (strlen($row[1])>4){ 
						$Disp = false;
						if(!in_array($row[0], $execCats)){
							
							$execCats[] = $row[0];
							$Disp = true;
						}
						if( !in_array($row[2], $execCats) && !in_array($row[2], $Cats) && $row[2]>1){
							
							$Cats[] = $row[2];
						}
						$row[1] = _deleteHTML_Elements( $row[1] );
	                    if ($row[2] <= 1 && $Disp)
						{
							fputs($f,"			<category id=\"".$row[0]."\">".$row[1]."</category>\n");
						}
	                    elseif($Disp) 
						{
							fputs($f,"			<category id=\"".$row[0]."\" parentId=\"".$row[2]."\">".$row[1]."</category>\n");
						}
                    }
					}
				}
				
                fputs($f,"		</categories>\n");		
			}


			function _exportProducts( $f, $rate, $export_product_name, &$_ProductIDs )
			{

				fputs( $f, "				<offers>\n");

				
				    $clause="";
                    //$clause = " export_visible==1 and amount>0";
					$clause = " cp.amount>0 and cp.is_edp=1";




				//выбрать товары
				//$proCount = count($_ProductIDs);
				//$iter = 0;
				//for (; $iter<$proCount;$iter+=100){
					
                
                $sql = "select cp.product_id, cpd.product, cp.list_price, cpc.category_id, ci.*, cp.amount from cscart_products cp
                left join cscart_product_descriptions cpd on cpd.product_id=cp.product_id and cpd.lang_code=\"RU\"
                left join cscart_products_categories cpc on cpc.product_id=cp.product_id 
                left join cscart_images_links cil on cil.object_id=cp.product_id 
                left join cscart_images ci on ci.image_id=cil.image_id
					where ".$clause;
                    
				$q = mysql_query($sql);

				$store_url = "http://www.lindero.ru";

				while ($product = mysql_fetch_assoc($q))
				{
                    $product["product"]=str_replace("&","&amp;",$product["product"]);
					fputs( $f, "					<offer available=\"".(($product['amount'])?'true':'false')."\" id=\"".$product["product_id"]."\">\n");
					fputs( $f, "						<url>http://lindero.ru/index.php?dispatch=products.view&amp;product_id=".$product["product_id"]."&amp;from=ya</url>\n" );
					fputs( $f, "						<price>".preg_replace("/\..*/","",$product["list_price"])."</price>\n" );
					fputs( $f, "						<currencyId>RUR</currencyId>\n" );
					fputs( $f, "						<categoryId>".$product["category_id"]."</categoryId>\n" );

					

            
					$q1 = mysql_query("select ci.image_path as filename from cscart_images_links cil 
                    join cscart_images ci on ci.image_id=cil.image_id
                    and cil.object_id=".$product["product_id"]);
					$pic_row = mysql_fetch_assoc($q1);
                    $pix=glob("/home/aqq7507/public_html/lindero.ru/images/*/*/*/".$pic_row["filename"]);
                    $pix[0]=str_replace("&","&amp;",$pix[0]);  
                    //var_dump($pix);
                    //echo " ".$product["product_id"]." ".$pic_row["filename"]."  ".$pix[0]."<br>\n";
                   // var_dump(str_replace('/home/aqq7507/public_html/', 'http://',$pix[0]));exit();
					if ($pic_row) //экспортировать фотографию
					{
						//if ( strlen($pic_row["filename"]) && file_exists("./products_pictures/".$pic_row["filename"]) )
							fputs( $f, "						<picture>".str_replace('/home/aqq7507/public_html/', 'http://',$pix[0])."</picture>\n" );
				
					}

                   

					//$product["product"]		= $_NameAddi._deleteHTML_Elements( $product["product"] );
					
					fputs( $f, "						<name>".$product["product"]."</name>\n" );

					if ( strlen($dsc)>0 )
					{
//						$product[$dsc] = _deleteHTML_Elements( $product[$dsc] );
						$product["product"] = strip_tags( $product["product"] );
						fputs( $f, "						<description>".$product["product"].
														"</description>\n" );
					}
					else
					{
						fputs( $f, "						<description></description>\n" );
					}

					fputs( $f, "					</offer>\n");

				}
				
				//}
				fputs( $f, "				</offers>\n");
			}

			function _exportEnd( $f )
			{
				fputs( $f, "			</shop>\n" );
				fputs( $f, "		</yml_catalog>\n" );
			}





			$rurrate = 30;
			$yandex_export_product_name = 'only_name';
			

				$f = fopen("/home/aqq7507/public_html/lindero.ru/temp/yandex.xml","w");
				if ($f)
				{
					_exportToYandexMarket( $f, $rurrate, $yandex_export_product_name );
					fclose($f);

				}




?>
