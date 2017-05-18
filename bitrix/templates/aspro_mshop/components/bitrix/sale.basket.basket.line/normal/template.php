<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

global $isBasketPage, $arBasketItems;

$cartStyle = 'bx-basket';
$cartId = "bx_basket".$component->getNextNumber();
$arParams['cartId'] = $cartId;

if(!function_exists('declOfNum')){
	function declOfNum($number, $titles){
		$cases = array (2, 0, 1, 1, 1, 2); 
		return sprintf($titles[($number % 100 > 4 && $number % 100 < 20) ? 2 : $cases[min($number % 10, 5)]], $number);
		}
	}

$count = $delayCount =  $summ = 0;
$arBasketIDs=array();

if ($arParams["SHOW_PRODUCTS"] == "Y"/* && $arResult['NUM_PRODUCTS'] > 0*/){
	foreach ($arResult["CATEGORIES"] as $category => $items){
		if (empty($items))
			continue;
		if($category=="READY"){
			foreach($items as $arItem){
				++$count;
				$price=((isset($arItem["~PRICE"]) && $arItem["~PRICE"]) ? $arItem["~PRICE"] : $arItem["PRICE"] );
				if(0>$price){
					$arBasketItemPrice = CSaleBasket::GetList(
						array(),
						array("ID" => $arItem["ID"]),
						false,
						false,
						array("PRICE", "ID")
					)->Fetch();
					$price=$arBasketItemPrice["PRICE"];
					$arItem["PRICE"]=$price;
				}
				$summ += $price*$arItem["QUANTITY"];
				if(!CSaleBasketHelper::isSetItem($arItem))
					$arBasketIDs[$arItem["ID"]]=$arItem;
			}
		}elseif($category=="DELAY"){
			foreach($items as $arItem){
				++$delayCount;
			}
		}
	}
	$cur = CCurrencyLang::GetCurrencyFormat(CSaleLang::GetLangCurrency(SITE_ID));
	$summ_formated = FormatCurrency($summ, $cur["CURRENCY"]);
}else{
	$summ_formated=$arResult["TOTAL_PRICE"];
	$count=$arResult["NUM_PRODUCTS"];
}

if($arBasketIDs){
	$propsIterator = CSaleBasket::GetPropsList(
		array('BASKET_ID' => 'ASC', 'SORT' => 'ASC', 'ID' => 'ASC'),
		array('BASKET_ID' => array_keys($arBasketIDs))
	);
	while ($property = $propsIterator->GetNext()){
		$property['CODE'] = (string)$property['CODE'];
		if ($property['CODE'] == 'CATALOG.XML_ID' || $property['CODE'] == 'PRODUCT.XML_ID')
			continue;
		if (!isset($arBasketIDs[$property['BASKET_ID']]))
			continue;
		$arBasketIDs[$property['BASKET_ID']]['PROPS'][] = $property;
	}
}
usort($arBasketIDs, 'CMShop::cmpByID');
global $TEMPLATE_OPTIONS;
if($TEMPLATE_OPTIONS["BASKET"]["CURRENT_VALUE"]!="FLY"){?>

<?$frame = $this->createFrame()->begin('');?>
<div class="basket_normal cart <?=(!$count || $isBasketPage || $_POST["ACTION"]=='top' ? ' empty_cart ' : '')?> <?=(!$count && $isBasketPage ? 'ecart' : '');?> <?=($isBasketPage || $_POST["ACTION"]=='top' ? 'bcart' : '');?>">
	<!--noindex-->
		<div class="wraps_icon_block delay <?=($delayCount ? '' : 'ndelay' );?>">
			<a href="<?=$arParams["PATH_TO_BASKET"];?>#tab_DelDelCanBuy" class="link" <?=($delayCount ? '' : 'style="display: none;"' );?> title="<?=GetMessage("BASKET_DELAY_LIST");?>"></a>
			<div class="count">
				<span>
					<span class="items">
						<span class="text"><?=$delayCount;?></span>
					</span>
				</span>
			</div>
		</div>
		<div class="basket_block f-left">
			<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="link" title="<?=GetMessage("BASKET_LIST");?>"></a>
			<div class="wraps_icon_block basket">
				<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="link" title="<?=GetMessage("BASKET_LIST");?>"></a>
				<div class="count">
					<span>
						<span class="items">
							<a href="<?=$arParams["PATH_TO_BASKET"]?>"><?=$count;?></a>
						</span>
					</span>
				</div>
			</div>
			<div class="text f-left">
				<div class="title"><?=GetMessage("BASKET");?></div>
				<div class="value">
					<?if($count){?>
						<?=$summ_formated?>
					<?}else{?>
						<?=GetMessage("EMPTY_BASKET");?>
					<?}?>
				</div>
			</div>
			<div class="card_popup_frame popup">
				<div class="basket_popup_wrapper">
					<div class="basket_popup_wrapp" <?=($_SERVER["REQUEST_METHOD"] == "POST" && $_POST["ACTION"]=='del' && $_POST["ACTION"]!='top' ? "style='display: block;'" : "");?>>
						<div class="cart_wrapper" <?if ($count>3) { echo 'style="overflow-y:scroll;height:280px;"';};?>>
							<table class="cart_shell">
								<tbody>
									<?
									if($arParams["CACHE_TYPE"] != "N"){
										$cache = new CPHPCache();
										$cache_time = 100000;
										$cache_path = SITE_DIR.'mshop_basket/';
									}
									$i = 0;
									foreach($arBasketIDs as $arItem){
										if(($arItem["CAN_BUY"] == "Y") && ($arItem["DELAY"] == "N")){
											++$i;
											//if($i > 3) break;
											$cache_id = 'aspro_basket_'.$arItem["PRODUCT_ID"];
											if($arParams["CACHE_TYPE"] != "N" && $cache_time > 0 && $cache->InitCache($cache_time, $cache_id, $cache_path)){
												$res = $cache->GetVars();
												$item = $res["item"];
											}
											else{
												if($objRes = CIBlockElement::GetList(array(), array("ID" => $arItem["PRODUCT_ID"]))->GetNextElement()){
													$item = $objRes->GetFields();
													$item["PROPERTIES"] = $objRes->GetProperties();
													$arSelect = array("PREVIEW_PICTURE", "DETAIL_PICTURE", "ID", "DETAIL_PAGE_URL");
													if($item["PROPERTIES"]["CML2_LINK"]["VALUE"]){
														if($itemLink = CIBlockElement::GetList(array(), array("ID" => $item["PROPERTIES"]["CML2_LINK"]["VALUE"]), false, false, $arSelect)->GetNext()){
															if($item["IBLOCK_ID"] != $itemLink["IBLOCK_ID"]){
																$item["IBLOCK_ID"]= $itemLink["IBLOCK_ID"];
															}
															$item["ID"] = $itemLink["ID"];
															$item["DETAIL_PAGE_URL"] = $itemLink["DETAIL_PAGE_URL"];
															if(!$item["PREVIEW_PICTURE"] && $itemLink["PREVIEW_PICTURE"]){
																$item["PREVIEW_PICTURE"] = $itemLink["PREVIEW_PICTURE"];
															}
															if(!$item["DETAIL_PICTURE"] && $itemLink["DETAIL_PICTURE"]){
																$item["DETAIL_PICTURE"] = $itemLink["DETAIL_PICTURE"];
															}
														}
													}
													if($item["PREVIEW_PICTURE"]){
														$item["PREVIEW_PICTURE"] = CFile::ResizeImageGet($item["PREVIEW_PICTURE"], array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
													}
													elseif($item["DETAIL_PICTURE"]){
														$item["DETAIL_PICTURE"] = CFile::ResizeImageGet($item["DETAIL_PICTURE"], array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
													}
													$tmp_prop=$item["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"];
													unset($item["PROPERTIES"]);
													unset($item["~PROPERTIES"]);
													unset($item["SEARCHABLE_CONTENT"]);
													unset($item["~SEARCHABLE_CONTENT"]);
													$item["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"]=$tmp_prop;

													if($arParams["CACHE_TYPE"] != "N" && $cache_time > 0){
														$cache->StartDataCache($cache_time, $cache_id, $cache_path);
														$cache->EndDataCache(array("item" => $item));
													}
												}
											}
											?>
											<tr class="catalog_item" product-id="<?=$arItem["ID"]?>" data-iblockid="<?=$item["IBLOCK_ID"];?>" data-offer="<?=( $item["ID"]!=$item["~ID"] ? "Y" : "N" );?>" catalog-product-id="<?=( $item["ID"]!=$item["~ID"] ? $item["~ID"] : $item["ID"] );?>">
												<td class="thumb-cell">
													<a href="<?=$arItem["DETAIL_PAGE_URL"]?>">
														<?if($item["PREVIEW_PICTURE"]):?>
															<img src="<?=$item["PREVIEW_PICTURE"]["src"]?>" alt="<?=($item["PREVIEW_PICTURE"]["alt"] ? $item["PREVIEW_PICTURE"]["alt"] : $arItem["NAME"]);?>" title="<?=($item["PREVIEW_PICTURE"]["title"] ? $item["PREVIEW_PICTURE"]["title"] : $arItem["NAME"]);?>" />
														<?elseif($item["DETAIL_PICTURE"]):?>
															<img src="<?=$item["DETAIL_PICTURE"]["src"]?>" alt="<?=($item["PREVIEW_PICTURE"]["alt"] ? $item["PREVIEW_PICTURE"]["alt"] : $arItem["NAME"]);?>" title="<?=($item["PREVIEW_PICTURE"]["title"] ? $item["PREVIEW_PICTURE"]["title"] : $arItem["NAME"]);?>" />
														<?else:?>
															<img border="0" src="<?=SITE_TEMPLATE_PATH?>/images/no_photo_medium.png" alt="<?=($item["PREVIEW_PICTURE"]["alt"] ? $item["PREVIEW_PICTURE"]["alt"] : $arItem["NAME"]);?>" title="<?=($item["PREVIEW_PICTURE"]["title"] ? $item["PREVIEW_PICTURE"]["title"] : $arItem["NAME"]);?>" />
														<?endif;?>
													</a>
												</td>
												<td class="item-title">
													<a href="<?=$arItem["DETAIL_PAGE_URL"]?>" class="clearfix"><span><?=$arItem["NAME"]?></span></a>
													<?if($arItem["PROPS"]){?>
														<div class="props">
															<?foreach($arItem["PROPS"] as $arProp){?>
																<div class="item_prop"><span class="title"><?=$arProp["NAME"];?>:</span><span class="value"><?=$arProp["VALUE"];?></span></div>
															<?}?>
														</div>
													<?}?>
													<?$price=((isset($arItem["~PRICE"]) && $arItem["~PRICE"]) ? $arItem["~PRICE"] : $arItem["PRICE"] );?>
													<div class="one-item">
														<?
														$arBasketMore=array();
														$arBasketMore = CSaleBasket::GetList(array("ID" => "ASC"), array("ID" => $arItem["ID"]), false, false, array("ID", "MEASURE_NAME", "MEASURE_CODE", "TYPE", "SET_PARENT_ID"))->Fetch();
														?>
														<span class="value"><?=SaleFormatCurrency($price, $arItem["CURRENCY"]);?></span>
														<span class="measure">x <span><?=(float)$arItem["QUANTITY"];?></span> <?=($arBasketMore["MEASURE_NAME"] ? $arBasketMore["MEASURE_NAME"] : ( $item["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"] ? $item["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"] : GetMessage("CML2_BASE_UNIT")));?>.</span>
													</div>
													<div class="cost-cell">														
														<input type="hidden" name="item_one_price_<?=$arItem["ID"]?>" value="<?=$arItem["~PRICE"];?>">
														<input type="hidden" name="item_one_price_discount_<?=$arItem["ID"]?>" value="<?=$arItem["DISCOUNT_PRICE"]?>">
														<input type="hidden" name="item_price_<?=$arItem["ID"]?>" value="<?=($price * $arItem["QUANTITY"])?>">
														<input type="hidden" name="item_price_discount_<?=$arItem["ID"]?>" value="<?=$arItem["DISCOUNT_PRICE"]?>">														
														<span class="price"><?=FormatCurrency($price * $arItem["QUANTITY"], $arItem["CURRENCY"]);?></span>
													</div>
													<div class="clearfix"></div>
													<!--noindex-->
														<div class="remove-cell">
															<span class="remove" data-id="<?=$arItem["ID"]?>" rel="nofollow" href="<?=SITE_DIR?>basket/?action=delete&id=<?=$arItem["ID"]?>" title="<?=GetMessage("SALE_DELETE_PRD")?>"><i></i></span>
														</div>
													<!--/noindex-->
												</td>
											</tr>
										<?}?>
									<?}?>
								</tbody>
							</table>
						</div>
						<div class="basket_empty clearfix">
							<table>
								<tr>
									<td class="image"><div></div></td>
									<td class="description"><div class="basket_empty_subtitle"><?=GetMessage("BASKET_EMPTY_SUBTITLE")?></div><div class="basket_empty_description"><?=GetMessage("BASKET_EMPTY_DESCRIPTION")?></div></td>
								</tr>
							</table>
						</div>
						<div class="total_wrapp clearfix">
							<div class="total"><span><?=GetMessage("TOTAL_SUMM_TITLE")?>:</span><span class="price"><?=$summ_formated?></span><div class="clearfix"></div></div>
							<input type="hidden" name="total_price" value="<?=$summ?>" />
							<input type="hidden" name="total_count" value="<?=$count;?>" />
							<input type="hidden" name="delay_count" value="<?=$delayCount;?>" />
							<div class="but_row1">
								<a href="<?=$arParams["PATH_TO_BASKET"]?>" class="button short"><span class="text"><?=GetMessage("GO_TO_BASKET");?></span></a>
							</div>
						</div>
						<?$paramsString = urlencode(serialize($arParams));?>
						<input id="top_basket_params" type="hidden" name="PARAMS" value='<?=$paramsString?>' />
					</div>
				</div>
			</div>
		</div>
	<script type="text/javascript">
	$('.card_popup_frame').ready(function(){
		$('.card_popup_frame span.remove').click(function(e){
			e.preventDefault();
			if(!$(this).is(".disabled")){
				var row = $(this).parents("tr").first();
				row.fadeTo(100 , 0.05, function() {});
				delFromBasketCounter($(this).closest('tr').attr('catalog-product-id'));
				reloadTopBasket('del', $('#basket_line'), 200, 2000, 'N', $(this));
				markProductRemoveBasket($(this).closest('.catalog_item').attr('catalog-product-id'));
			}
		});
	});
	</script>
</div>

<?$frame->end();?>
<?}?>