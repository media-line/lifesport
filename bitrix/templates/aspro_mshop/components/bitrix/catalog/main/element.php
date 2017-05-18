<?
if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();
$this->setFrameMode(true);

use Bitrix\Main\Loader;
use Bitrix\Main\ModuleManager;

CModule::IncludeModule("iblock");
global $TEMPLATE_OPTIONS, $MShopSectionID;
$arParams["ADD_ELEMENT_CHAIN"] = (isset($arParams["ADD_ELEMENT_CHAIN"]) ? $arParams["ADD_ELEMENT_CHAIN"] : "N");

// get current section ID
$section = array();
if($arResult["VARIABLES"]["SECTION_ID"] > 0){
	$section=CMshopCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CMshopCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arResult["VARIABLES"]["SECTION_ID"]), false, array("ID"));
}
elseif(strlen(trim($arResult["VARIABLES"]["SECTION_CODE"])) > 0){
	$section=CMshopCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CMshopCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "=CODE" => $arResult["VARIABLES"]["SECTION_CODE"]), false, array("ID"));
}

if($arResult["VARIABLES"]["ELEMENT_ID"] > 0){
	$arElement = CMshopCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CMshopCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "ID" => $arResult["VARIABLES"]["ELEMENT_ID"]), false, false, array("ID", "IBLOCK_SECTION_ID", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
}
elseif(strlen(trim($arResult["VARIABLES"]["ELEMENT_CODE"])) > 0){
	$arElement = CMshopCache::CIBLockElement_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CMshopCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("IBLOCK_ID" => $arParams["IBLOCK_ID"], "ACTIVE"=>"Y", "=CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"]), false, false, array("ID", "IBLOCK_SECTION_ID", "PREVIEW_TEXT", "PREVIEW_PICTURE", "DETAIL_PICTURE"));
}

if(!$section["ID"]){
	if($arElement["IBLOCK_SECTION_ID"] && !$section){
		$section=CMshopCache::CIBlockSection_GetList(array('CACHE' => array("MULTI" =>"N", "TAG" => CMshopCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array('GLOBAL_ACTIVE' => 'Y', "ID" => $arElement["IBLOCK_SECTION_ID"]), false, array("ID"));
	}
}
$MShopSectionID = $section["ID"];
?>
<?CMShop::AddMeta(
	array(
		'og:description' => $arElement['PREVIEW_TEXT'],
		'og:image' => (($arElement['PREVIEW_PICTURE'] || $arElement['DETAIL_PICTURE']) ? CFile::GetPath(($arElement['PREVIEW_PICTURE'] ? $arElement['PREVIEW_PICTURE'] : $arElement['DETAIL_PICTURE'])) : false),
	)
);?>
<div class="catalog_detail"  itemscope itemtype="http://schema.org/Product">
	<?$ElementID = $APPLICATION->IncludeComponent(
		"bitrix:catalog.element",
		"main",
		Array(
			"TYPE_SKU" => $TEMPLATE_OPTIONS["TYPE_SKU"]["CURRENT_VALUE"],
			"SKU_DETAIL_ID" => $arParams["SKU_DETAIL_ID"],
			"SEF_URL_TEMPLATES" => $arParams["SEF_URL_TEMPLATES"],
			"IBLOCK_REVIEWS_TYPE" => $arParams["IBLOCK_REVIEWS_TYPE"],
			"IBLOCK_REVIEWS_ID" => $arParams["IBLOCK_REVIEWS_ID"],
			"SHOW_ONE_CLICK_BUY" => $arParams["SHOW_ONE_CLICK_BUY"],
			"SEF_MODE_BRAND_SECTIONS" => $arParams["SEF_MODE_BRAND_SECTIONS"],
			"SEF_MODE_BRAND_ELEMENT" => $arParams["SEF_MODE_BRAND_ELEMENT"],
			"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
			"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
			"IBLOCK_ID" => $arParams["IBLOCK_ID"],
			"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
			"META_KEYWORDS" => $arParams["DETAIL_META_KEYWORDS"],
			"META_DESCRIPTION" => $arParams["DETAIL_META_DESCRIPTION"],
			"BROWSER_TITLE" => $arParams["DETAIL_BROWSER_TITLE"],
			"BASKET_URL" => $arParams["BASKET_URL"],
			"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
			"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
			"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
			"DISPLAY_PANEL" => $arParams["DISPLAY_PANEL"],
			"CACHE_TYPE" => $arParams["CACHE_TYPE"],
			"CACHE_TIME" => $arParams["CACHE_TIME"],
			"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
			"CACHE_FILTER" => "Y",
			"SET_TITLE" => $arParams["SET_TITLE"],
			"SET_CANONICAL_URL" => $arParams["DETAIL_SET_CANONICAL_URL"],
			"SET_LAST_MODIFIED" => "Y",
			"SET_STATUS_404" => $arParams["SET_STATUS_404"],
			"MESSAGE_404" => $arParams["MESSAGE_404"],
			"SHOW_404" => $arParams["SHOW_404"],
			"FILE_404" => $arParams["FILE_404"],
			"PRICE_CODE" => $arParams["PRICE_CODE"],
			"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
			"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
			"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
			"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
			"LINK_IBLOCK_TYPE" => $arParams["LINK_IBLOCK_TYPE"],
			"LINK_IBLOCK_ID" => $arParams["LINK_IBLOCK_ID"],
			"LINK_PROPERTY_SID" => $arParams["LINK_PROPERTY_SID"],
			"LINK_ELEMENTS_URL" => $arParams["LINK_ELEMENTS_URL"],
			"USE_ALSO_BUY" => $arParams["USE_ALSO_BUY"],
			'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
			'RELATIVE_QUANTITY_FACTOR' => (isset($arParams['RELATIVE_QUANTITY_FACTOR']) ? $arParams['RELATIVE_QUANTITY_FACTOR'] : ''),
			'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
			"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
			"OFFERS_FIELD_CODE" => $arParams["DETAIL_OFFERS_FIELD_CODE"],
			"OFFERS_PROPERTY_CODE" => $arParams["DETAIL_OFFERS_PROPERTY_CODE"],
			"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
			"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
			"SKU_DISPLAY_LOCATION" => $arParams["SKU_DISPLAY_LOCATION"],
			"ELEMENT_ID" => $arResult["VARIABLES"]["ELEMENT_ID"],
			"ELEMENT_CODE" => $arResult["VARIABLES"]["ELEMENT_CODE"],
			"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
			"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
			"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
			"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
			"ADD_SECTIONS_CHAIN" => $arParams["ADD_SECTIONS_CHAIN"],
			"ADD_ELEMENT_CHAIN" => $arParams["ADD_ELEMENT_CHAIN"],
			"USE_STORE" => $arParams["USE_STORE"],
			"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
			"USE_STORE_SCHEDULE" => $arParams["USE_STORE_SCHEDULE"],
			"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
			"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
			"STORE_PATH" => $arParams["STORE_PATH"],
			"MAIN_TITLE" => $arParams["MAIN_TITLE"],
			"USE_PRODUCT_QUANTITY" => $arParams["USE_PRODUCT_QUANTITY"],
			"CHECK_SECTION_ID_VARIABLE" => (isset($arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"]) ? $arParams["DETAIL_CHECK_SECTION_ID_VARIABLE"] : ''),		
			"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
			"IBLOCK_STOCK_ID" => $arParams["IBLOCK_STOCK_ID"],
			"SEF_MODE_STOCK_SECTIONS" => $arParams["SEF_MODE_STOCK_SECTIONS"],
			"SHOW_QUANTITY" => $arParams["SHOW_QUANTITY"],
			"SHOW_QUANTITY_COUNT" => $arParams["SHOW_QUANTITY_COUNT"],
			"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
			"CURRENCY_ID" => $arParams["CURRENCY_ID"],
			'HIDE_NOT_AVAILABLE' => $arParams["HIDE_NOT_AVAILABLE"],
			'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
			'SHOW_DEACTIVATED' => $arParams['SHOW_DEACTIVATED'],
			"USE_ELEMENT_COUNTER" => $arParams["USE_ELEMENT_COUNTER"],
			"USE_RATING" => $arParams["USE_RATING"],
			"USE_REVIEW" => $arParams["USE_REVIEW"],
			"FORUM_ID" => $arParams["FORUM_ID"],
			"MAX_AMOUNT" => $arParams["MAX_AMOUNT"],
			"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
			"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
			"DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
			"SHOW_BRAND_PICTURE" => $arParams["SHOW_BRAND_PICTURE"],
			"PROPERTIES_DISPLAY_LOCATION" => $arParams["PROPERTIES_DISPLAY_LOCATION"],
			"PROPERTIES_DISPLAY_TYPE" => $arParams["PROPERTIES_DISPLAY_TYPE"],
			"SHOW_ADDITIONAL_TAB" => $arParams["SHOW_ADDITIONAL_TAB"],
			"SHOW_ASK_BLOCK" => $arParams["SHOW_ASK_BLOCK"],
			"ASK_FORM_ID" => $arParams["ASK_FORM_ID"],
			"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
			"SHOW_HINTS" => $arParams["SHOW_HINTS"],
			"OFFER_HIDE_NAME_PROPS" => $arParams["OFFER_HIDE_NAME_PROPS"],
			"SHOW_KIT_PARTS" => $arParams["SHOW_KIT_PARTS"],
			"SHOW_KIT_PARTS_PRICES" => $arParams["SHOW_KIT_PARTS_PRICES"],
			"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
			"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
			'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
			'ADD_DETAIL_TO_SLIDER' => (isset($arParams['DETAIL_ADD_DETAIL_TO_SLIDER']) ? $arParams['DETAIL_ADD_DETAIL_TO_SLIDER'] : ''),
			"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
			"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
			"USER_FIELDS" => $arParams['USER_FIELDS'],
			"FIELDS" => $arParams['FIELDS'],
			"STORES" => $arParams['STORES'],
			"BIG_DATA_RCM_TYPE" => $arParams['BIG_DATA_RCM_TYPE'],
			"USE_MAIN_ELEMENT_SECTION" => $arParams["USE_MAIN_ELEMENT_SECTION"],
			'STRICT_SECTION_CHECK' => (isset($arParams['DETAIL_STRICT_SECTION_CHECK']) ? $arParams['DETAIL_STRICT_SECTION_CHECK'] : ''),
			"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
			"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
			"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
			"OFFERS_LIMIT" => $arParams["DETAIL_OFFERS_LIMIT"],

			'DISABLE_INIT_JS_IN_COMPONENT' => (isset($arParams['DISABLE_INIT_JS_IN_COMPONENT']) ? $arParams['DISABLE_INIT_JS_IN_COMPONENT'] : ''),
			'SET_VIEWED_IN_COMPONENT' => (isset($arParams['DETAIL_SET_VIEWED_IN_COMPONENT']) ? $arParams['DETAIL_SET_VIEWED_IN_COMPONENT'] : ''),
			'SHOW_SLIDER' => (isset($arParams['DETAIL_SHOW_SLIDER']) ? $arParams['DETAIL_SHOW_SLIDER'] : ''),
			'SLIDER_INTERVAL' => (isset($arParams['DETAIL_SLIDER_INTERVAL']) ? $arParams['DETAIL_SLIDER_INTERVAL'] : ''),
			'SLIDER_PROGRESS' => (isset($arParams['DETAIL_SLIDER_PROGRESS']) ? $arParams['DETAIL_SLIDER_PROGRESS'] : ''),
			'USE_ENHANCED_ECOMMERCE' => (isset($arParams['USE_ENHANCED_ECOMMERCE']) ? $arParams['USE_ENHANCED_ECOMMERCE'] : ''),
			'DATA_LAYER_NAME' => (isset($arParams['DATA_LAYER_NAME']) ? $arParams['DATA_LAYER_NAME'] : ''),
			
			"USE_GIFTS_DETAIL" => $arParams['USE_GIFTS_DETAIL']?: 'Y',
			"USE_GIFTS_MAIN_PR_SECTION_LIST" => $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST']?: 'Y',
			"GIFTS_SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
			"GIFTS_SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
			"GIFTS_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
			"GIFTS_DETAIL_HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
			"GIFTS_DETAIL_TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
			"GIFTS_DETAIL_BLOCK_TITLE" => $arParams["GIFTS_DETAIL_BLOCK_TITLE"],
			"GIFTS_SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
			"GIFTS_SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
			"GIFTS_MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

			"GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
			"GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],
		),
		$component
	);?>
</div>
<div class="clearfix"></div>

<?/*fix title after ajax form start*/
$arAdditionalData = $arNavParams = array();
$arAdditionalData['TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle());
$arAdditionalData['WINDOW_TITLE'] = htmlspecialcharsback($APPLICATION->GetTitle('title'));

// dirty hack: try to get breadcrumb call params
for ($i = 0, $cnt = count($APPLICATION->buffer_content_type); $i < $cnt; $i++){
	if ($APPLICATION->buffer_content_type[$i]['F'][1] == 'GetNavChain'){
		$arNavParams = $APPLICATION->buffer_content_type[$i]['P'];
	}
}
if ($arNavParams){
	$arAdditionalData['NAV_CHAIN'] = $APPLICATION->GetNavChain($arNavParams[0], $arNavParams[1], $arNavParams[2], $arNavParams[3], $arNavParams[4]);
}
?>
<script type="text/javascript">
	BX.addCustomEvent(window, "onAjaxSuccess", function(){
		var arAjaxPageData = <?=CUtil::PhpToJSObject($arAdditionalData);?>;
		if (arAjaxPageData.TITLE)
			BX.ajax.UpdatePageTitle(arAjaxPageData.TITLE);
		if (arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE)
			BX.ajax.UpdateWindowTitle(arAjaxPageData.WINDOW_TITLE || arAjaxPageData.TITLE);
		if (arAjaxPageData.NAV_CHAIN)
			BX.ajax.UpdatePageNavChain(arAjaxPageData.NAV_CHAIN);
		// top.BX.ajax.UpdatePageData(arAjaxPageData);
	});
</script>
<?/*fix title after ajax form end*/?>

<?$arAllValues=$arSimilar=$arAccessories=array();
/*similar goods*/
$arExpValues=CMshopCache::CIBlockElement_GetProperty($arParams["IBLOCK_ID"], $ElementID, array("CACHE" => array("TAG" =>CMshopCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("CODE" => "EXPANDABLES"));
if($arExpValues){
	$arAllValues["EXPANDABLES"]=$arExpValues;
}
/*accessories goods*/
$arAccessories=CMshopCache::CIBlockElement_GetProperty($arParams["IBLOCK_ID"], $ElementID, array("CACHE" => array("TAG" =>CMshopCache::GetIBlockCacheTag($arParams["IBLOCK_ID"]))), array("CODE" => "ASSOCIATED"));
if($arAccessories){
	$arAllValues["ASSOCIATED"]=$arAccessories;
}?>

<?if($arAccessories || $arExpValues || (ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N'))){?>
	<?$class_block="s_".$this->randString();
	$arTab=array();
	if($arExpValues){
		$arTab["EXPANDABLES"]=GetMessage("EXPANDABLES_TITLE");
	}
	if($arAccessories){
		$arTab["ASSOCIATED"]=GetMessage("ASSOCIATED_TITLE");
	}
	/* Start Big Data */
	if(ModuleManager::isModuleInstalled("sale") && (!isset($arParams['USE_BIG_DATA']) || $arParams['USE_BIG_DATA'] != 'N')){
		$arTab["RECOMENDATION"]=GetMessage("RECOMENDATION_TITLE");
	}?>
	<div class="specials_tabs_section1 specials_slider_wrapp1 specials tab_slider_wrapp <?=$class_block;?>">
		<div class="top_blocks mainntcn2">
			<ul class="tabs">
				<?$i=1;
				foreach($arTab as $code=>$title):?>
					<li data-code="<?=$code?>" <?=($i==1 ? "class='cur'" : "")?>><span><?=$title;?></span></li>
					<?$i++;?>
				<?endforeach;?>
				<li class="stretch"></li>
			</ul>
			<ul class="slider_navigation top">
				<?$i=1;
				foreach($arTab as $code=>$title):?>
					<li class="tabs_slider_navigation <?=$code?>_nav <?=($i==1 ? "cur" : "")?>" data-code="<?=$code?>"></li>
					<?$i++;?>
				<?endforeach;?>
			</ul>
		</div>
		<?$disply_elements=($arParams["DISPLAY_ELEMENT_SLIDER"] ? $arParams["DISPLAY_ELEMENT_SLIDER"] : 10);?>
		<ul class="tabs_content">
			<?foreach($arTab as $code=>$title){?>
				<li class="tab <?=$code?>_wrapp" data-code="<?=$code?>">
					<?if($code=="RECOMENDATION"){?>
						<?
						$GLOBALS["CATALOG_CURRENT_ELEMENT_ID"] = $ElementID;
						/*if(Loader::includeModule("catalog")){
							$arSKU = CCatalogSKU::GetInfoByProductIBlock($arParams['IBLOCK_ID']);
						}
						$ElementOfferIblockID = (!empty($arSKU) ? $arSKU['IBLOCK_ID'] : 0);*/
						?>
						<?$APPLICATION->IncludeComponent("bitrix:catalog.bigdata.products", CMShop::checkVersionExt(), array(
							"LINE_ELEMENT_COUNT" => 5,
							"TEMPLATE_THEME" => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
							"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
							"BASKET_URL" => $arParams["BASKET_URL"],
							"ACTION_VARIABLE" => (!empty($arParams["ACTION_VARIABLE"]) ? $arParams["ACTION_VARIABLE"] : "action")."_cbdp",
							"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
							"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
							"ADD_PROPERTIES_TO_BASKET" => "N",
							"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
							"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
							"SHOW_OLD_PRICE" => $arParams['SHOW_OLD_PRICE'],
							"SHOW_DISCOUNT_PERCENT" => $arParams['SHOW_DISCOUNT_PERCENT'],
							"PRICE_CODE" => $arParams["PRICE_CODE"],
							"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
							"PRODUCT_SUBSCRIPTION" => $arParams['PRODUCT_SUBSCRIPTION'],
							"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
							"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
							"SHOW_NAME" => "Y",
							"SHOW_IMAGE" => "Y",
							"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
							"MESS_BTN_BUY" => $arParams['MESS_BTN_BUY'],
							"MESS_BTN_DETAIL" => $arParams['MESS_BTN_DETAIL'],
							"MESS_BTN_SUBSCRIBE" => $arParams['MESS_BTN_SUBSCRIBE'],
							"MESS_NOT_AVAILABLE" => $arParams['MESS_NOT_AVAILABLE'],
							"PAGE_ELEMENT_COUNT" => $disply_elements,
							"SHOW_FROM_SECTION" => "N",
							"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
							"IBLOCK_ID" => $arParams["IBLOCK_ID"],
							"DEPTH" => "2",
							"CACHE_TYPE" => $arParams["CACHE_TYPE"],
							"CACHE_TIME" => $arParams["CACHE_TIME"],
							"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
							"CACHE_FILTER" => "Y",
							"SHOW_PRODUCTS_".$arParams["IBLOCK_ID"] => "Y",
							"ADDITIONAL_PICT_PROP_".$arParams["IBLOCK_ID"] => $arParams['ADD_PICT_PROP'],
							"LABEL_PROP_".$arParams["IBLOCK_ID"] => "-",
							"HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
							'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
							"CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
							"CURRENCY_ID" => $arParams["CURRENCY_ID"],
							"SECTION_ID" => $arResult["VARIABLES"]["SECTION_ID"],
							"SECTION_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
							"SECTION_ELEMENT_ID" => $arResult["VARIABLES"]["SECTION_ID"],
							"SECTION_ELEMENT_CODE" => $arResult["VARIABLES"]["SECTION_CODE"],
							"ID" => $ElementID,
							"PROPERTY_CODE_".$arParams["IBLOCK_ID"] => $arParams["LIST_PROPERTY_CODE"],
							"CART_PROPERTIES_".$arParams["IBLOCK_ID"] => $arParams["PRODUCT_PROPERTIES"],
							"RCM_TYPE" => (isset($arParams['BIG_DATA_RCM_TYPE']) ? $arParams['BIG_DATA_RCM_TYPE'] : ''),
							/*"OFFER_TREE_PROPS_".$ElementOfferIblockID => $arParams["OFFER_TREE_PROPS"],
							"ADDITIONAL_PICT_PROP_".$ElementOfferIblockID => $arParams['OFFER_ADD_PICT_PROP'],*/
							"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
							"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
							),
							false,
							array("HIDE_ICONS" => "Y")
						);
						?>
					<?}else{?>
						<ul class="tabs_slider <?=$code?>_slides wr">
							<?$GLOBALS['arrFilter'.$code] = array( "ID" => $arAllValues[$code] );?>
							<?$APPLICATION->IncludeComponent(
								"bitrix:catalog.top",
								"main",
								array(
									"TITLE_BLOCK" => $arParams["SECTION_TOP_BLOCK_TITLE"],
									"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
									"IBLOCK_ID" => $arParams["IBLOCK_ID"],
									"FILTER_NAME" => 'arrFilter'.$code,
									"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
									"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
									"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
									"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
									"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
									"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
									"BASKET_URL" => $arParams["BASKET_URL"],
									"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
									"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
									"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
									"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
									"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
									"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
									"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
									"ELEMENT_COUNT" => $disply_elements,
									"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
									"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
									"PROPERTY_CODE" => $arParams["DETAIL_PROPERTY_CODE"],
									"PRICE_CODE" => $arParams["PRICE_CODE"],
									"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
									"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
									"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
									"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
									"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
									"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
									"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
									"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
									"CACHE_TYPE" => $arParams["CACHE_TYPE"],
									"CACHE_TIME" => $arParams["CACHE_TIME"],
									"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
									"CACHE_FILTER" => "Y",
									"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
									"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
									"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
									"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
									"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
									"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
									"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
									"OFFERS_LIMIT" => $arParams["TOP_OFFERS_LIMIT"],
									'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
									'CURRENCY_ID' => $arParams['CURRENCY_ID'],
									'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
									'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
									'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
									'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
									'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
									'LABEL_PROP' => $arParams['LABEL_PROP'],
									'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
									'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],

									'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
									'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
									'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
									'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
									'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
									'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
									'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
									'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
									'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
									'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
									'ADD_TO_BASKET_ACTION' => $basketAction,
									'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
									'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
								),
								false, array("HIDE_ICONS"=>"Y")
							);?>
						</ul>
					<?}?>
				</li>
			<?}?>
		</ul>
	</div>
	<script type="text/javascript">
		$(document).ready(function(){

			$('.tab_slider_wrapp.<?=$class_block;?> .tabs > li').first().addClass('cur');
			$('.tab_slider_wrapp.<?=$class_block;?> .slider_navigation > li').first().addClass('cur');
			$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li').first().addClass('cur');

			var flexsliderItemWidth = 210;
			var flexsliderItemMargin = 20;

			var sliderWidth = $('.tab_slider_wrapp.<?=$class_block;?>').outerWidth();
			var flexsliderMinItems = Math.floor(sliderWidth / (flexsliderItemWidth + flexsliderItemMargin));
			$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').flexslider({
				animation: 'slide',
				selector: '.tabs_slider .catalog_item',
				slideshow: false,
				animationSpeed: 600,
				directionNav: true,
				controlNav: false,
				pauseOnHover: true,
				animationLoop: true,
				itemWidth: flexsliderItemWidth,
				itemMargin: flexsliderItemMargin,
				minItems: flexsliderMinItems,
				controlsContainer: '.tabs_slider_navigation.cur',
				start: function(slider){
					slider.find('li').css('opacity', 1);
				}
			});

			$('.tab_slider_wrapp.<?=$class_block;?> .tabs > li').on('click', function(){
				if(!$(this).hasClass('active')){
					var sliderIndex = $(this).index();
					$(this).addClass('active').addClass('cur').siblings().removeClass('active').removeClass('cur');
					$('.tab_slider_wrapp.<?=$class_block;?> .slider_navigation > li:eq(' + sliderIndex + ')').addClass('cur').show().siblings().removeClass('cur');
					$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li:eq(' + sliderIndex + ')').addClass('cur').siblings().removeClass('cur');
					if(!$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur .flex-viewport').length){
						$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').flexslider({
							animation: 'slide',
							selector: '.tabs_slider .catalog_item',
							slideshow: false,
							animationSpeed: 600,
							directionNav: true,
							controlNav: false,
							pauseOnHover: true,
							animationLoop: true,
							itemWidth: flexsliderItemWidth,
							itemMargin: flexsliderItemMargin,
							minItems: flexsliderMinItems,
							controlsContainer: '.tabs_slider_navigation.cur',
						});
					}
					$(window).resize();
				}
			});

			$(window).resize(function(){
				var sliderWidth = $('.tab_slider_wrapp.<?=$class_block;?>').outerWidth();
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').css('height', '');
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content .tab.cur .tabs_slider .buttons_block').hide();
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').equalize({children: '.item-title'});
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').equalize({children: '.item_info'});
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content > li.cur').equalize({children: '.catalog_item'});
				var itemsButtonsHeight = $('.tab_slider_wrapp.<?=$class_block;?> .tabs_content .tab.cur .tabs_slider li .buttons_block').height();
				var tabsContentUnhover = $('.tab_slider_wrapp.<?=$class_block;?> .tabs_content .tab.cur').height() * 1;
				var tabsContentHover = tabsContentUnhover + itemsButtonsHeight+50;
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content .tab.cur').attr('data-unhover', tabsContentUnhover);
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content .tab.cur').attr('data-hover', tabsContentHover);
				$('.tab_slider_wrapp.<?=$class_block;?> .tabs_content').height(tabsContentUnhover);
			});

			$(window).resize();
			$(document).on({
				mouseover: function(e){
					var tabsContentHover = $(this).closest('.tab').attr('data-hover') * 1;
					$(this).closest('.tab').fadeTo(100, 1);
					$(this).closest('.tab').stop().css({'height': tabsContentHover});
					$(this).find('.buttons_block').fadeIn(450, 'easeOutCirc');
				},
				mouseleave: function(e){
					var tabsContentUnhoverHover = $(this).closest('.tab').attr('data-unhover') * 1;
					$(this).closest('.tab').stop().animate({'height': tabsContentUnhoverHover}, 100);
					$(this).find('.buttons_block').stop().fadeOut(233);
				}
			}, '.<?=$class_block;?> .tabs_slider li');
		})
	</script>
<?}?>

<div class="detail_footer">
	<?
	$IsViewedTypeLocal = $TEMPLATE_OPTIONS['VIEWED_TYPE']['CURRENT_VALUE'] === 'LOCAL';
	$arViewedIDs = CMShop::getViewedProducts();
	?>
	<?if($arViewedIDs):?>
		<div class="similar_products_wrapp">
			<?if(!$IsViewedTypeLocal):?>
				<?$GLOBALS['arrFilterViewed'] = array('ID' => $arViewedIDs);?>
				<?$APPLICATION->IncludeComponent(
					"bitrix:catalog.top",
					"products_slider_viewed",
					array(
						"TITLE_BLOCK" => GetMessage('VIEWED_BEFORE'),
						"IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
						"IBLOCK_ID" => $arParams["IBLOCK_ID"],
						"FILTER_NAME" => 'arrFilterViewed',
						"ELEMENT_SORT_FIELD" => $arParams["ELEMENT_SORT_FIELD"],
						"ELEMENT_SORT_ORDER" => $arParams["ELEMENT_SORT_ORDER"],
						"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
						"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
						"SECTION_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["section"],
						"DETAIL_URL" => $arResult["FOLDER"].$arResult["URL_TEMPLATES"]["element"],
						"BASKET_URL" => $arParams["BASKET_URL"],
						"ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
						"PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
						"SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],
						"PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
						"PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
						"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
						"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
						"ELEMENT_COUNT" => $arParams["VIEWED_ELEMENT_COUNT"],
						"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
						"LINE_ELEMENT_COUNT" => $arParams["TOP_LINE_ELEMENT_COUNT"],
						"PROPERTY_CODE" => array("HIT"),
						"PRICE_CODE" => $arParams["PRICE_CODE"],
						"USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
						"SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
						"PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
						"PRICE_VAT_SHOW_VALUE" => $arParams["PRICE_VAT_SHOW_VALUE"],
						"USE_PRODUCT_QUANTITY" => $arParams['USE_PRODUCT_QUANTITY'],
						"ADD_PROPERTIES_TO_BASKET" => (isset($arParams["ADD_PROPERTIES_TO_BASKET"]) ? $arParams["ADD_PROPERTIES_TO_BASKET"] : ''),
						"PARTIAL_PRODUCT_PROPERTIES" => (isset($arParams["PARTIAL_PRODUCT_PROPERTIES"]) ? $arParams["PARTIAL_PRODUCT_PROPERTIES"] : ''),
						"PRODUCT_PROPERTIES" => $arParams["PRODUCT_PROPERTIES"],
						"CACHE_TYPE" => $arParams["CACHE_TYPE"],
						"CACHE_TIME" => $arParams["CACHE_TIME"],
						"CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
						"CACHE_FILTER" => "Y",
						"OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
						"OFFERS_FIELD_CODE" => $arParams["LIST_OFFERS_FIELD_CODE"],
						"OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],
						"OFFERS_SORT_FIELD" => $arParams["OFFERS_SORT_FIELD"],
						"OFFERS_SORT_ORDER" => $arParams["OFFERS_SORT_ORDER"],
						"OFFERS_SORT_FIELD2" => $arParams["OFFERS_SORT_FIELD2"],
						"OFFERS_SORT_ORDER2" => $arParams["OFFERS_SORT_ORDER2"],
						"OFFERS_LIMIT" => $arParams["TOP_OFFERS_LIMIT"],
						'CONVERT_CURRENCY' => $arParams['CONVERT_CURRENCY'],
						'CURRENCY_ID' => $arParams['CURRENCY_ID'],
						'HIDE_NOT_AVAILABLE' => $arParams['HIDE_NOT_AVAILABLE'],
						'HIDE_NOT_AVAILABLE_OFFERS' => $arParams["HIDE_NOT_AVAILABLE_OFFERS"],
						'VIEW_MODE' => (isset($arParams['TOP_VIEW_MODE']) ? $arParams['TOP_VIEW_MODE'] : ''),
						'ROTATE_TIMER' => (isset($arParams['TOP_ROTATE_TIMER']) ? $arParams['TOP_ROTATE_TIMER'] : ''),
						'TEMPLATE_THEME' => (isset($arParams['TEMPLATE_THEME']) ? $arParams['TEMPLATE_THEME'] : ''),
						'LABEL_PROP' => $arParams['LABEL_PROP'],
						'ADD_PICT_PROP' => $arParams['ADD_PICT_PROP'],
						'PRODUCT_DISPLAY_MODE' => $arParams['PRODUCT_DISPLAY_MODE'],
						'OFFER_ADD_PICT_PROP' => $arParams['OFFER_ADD_PICT_PROP'],
						'OFFER_TREE_PROPS' => $arParams['OFFER_TREE_PROPS'],
						'PRODUCT_SUBSCRIPTION' => $arParams['PRODUCT_SUBSCRIPTION'],
						'SHOW_DISCOUNT_PERCENT' => $arParams['SHOW_DISCOUNT_PERCENT'],
						'SHOW_OLD_PRICE' => $arParams['SHOW_OLD_PRICE'],
						'MESS_BTN_BUY' => $arParams['MESS_BTN_BUY'],
						'MESS_BTN_ADD_TO_BASKET' => $arParams['MESS_BTN_ADD_TO_BASKET'],
						'MESS_BTN_SUBSCRIBE' => $arParams['MESS_BTN_SUBSCRIBE'],
						'MESS_BTN_DETAIL' => $arParams['MESS_BTN_DETAIL'],
						'MESS_NOT_AVAILABLE' => $arParams['MESS_NOT_AVAILABLE'],
						'ADD_TO_BASKET_ACTION' => $basketAction,
						'SHOW_CLOSE_POPUP' => isset($arParams['COMMON_SHOW_CLOSE_POPUP']) ? $arParams['COMMON_SHOW_CLOSE_POPUP'] : '',
						'COMPARE_PATH' => $arResult['FOLDER'].$arResult['URL_TEMPLATES']['compare'],
					),
					false, array("HIDE_ICONS"=>"Y")
				);?>
			<?else:?>
				<?$APPLICATION->IncludeComponent(
					"aspro:catalog.viewed.market",
					"slider",
					array(
						"TITLE_BLOCK" => GetMessage('VIEWED_BEFORE'),
						"VIEW_TYPE_IN_TAB" => "N",
						"SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
						"DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
						"DISPLAY_COMPARE" => $arParams["USE_COMPARE"],
						"SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
						"SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
					),
					false, array("HIDE_ICONS" => "Y")
				);?>
			<?endif;?>
		</div>
	<?endif;?>
</div>