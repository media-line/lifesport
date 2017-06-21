<? if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die(); ?>
<?
$shema = array();
?>
<div class="basket_props_block" id="bx_basket_div_<?= $arResult["ID"]; ?>" style="display: none;">
    <? if (!empty($arResult['PRODUCT_PROPERTIES_FILL'])) {
        foreach ($arResult['PRODUCT_PROPERTIES_FILL'] as $propID => $propInfo) {
            ?>
            <input type="hidden" name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"
                   value="<? echo htmlspecialcharsbx($propInfo['ID']); ?>">
            <? if (isset($arResult['PRODUCT_PROPERTIES'][$propID]))
                unset($arResult['PRODUCT_PROPERTIES'][$propID]);
        }
    }
    $arResult["EMPTY_PROPS_JS"] = "Y";
    $emptyProductProperties = empty($arResult['PRODUCT_PROPERTIES']);
    if (!$emptyProductProperties) {
        $arResult["EMPTY_PROPS_JS"] = "N";
        ?>
        <div class="wrapper">
            <table>
                <? foreach ($arResult['PRODUCT_PROPERTIES'] as $propID => $propInfo) {
                    ?>
                    <tr>
                        <td><? echo $arResult['PROPERTIES'][$propID]['NAME']; ?></td>
                        <td>
                            <? if (
                                'L' == $arResult['PROPERTIES'][$propID]['PROPERTY_TYPE']
                                && 'C' == $arResult['PROPERTIES'][$propID]['LIST_TYPE']
                            ) {
                                foreach ($propInfo['VALUES'] as $valueID => $value) {
                                    ?><label><input type="radio"
                                                    name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"
                                                    value="<? echo $valueID; ?>" <? echo($valueID == $propInfo['SELECTED'] ? '"checked"' : ''); ?>><? echo $value; ?>
                                    </label><?
                                }
                            } else {
                                ?><select name="<? echo $arParams['PRODUCT_PROPS_VARIABLE']; ?>[<? echo $propID; ?>]"><?
                                foreach ($propInfo['VALUES'] as $valueID => $value) {
                                    ?>
                                    <option
                                    value="<? echo $valueID; ?>" <? echo($valueID == $propInfo['SELECTED'] ? '"selected"' : ''); ?>><? echo $value; ?></option><?
                                }
                                ?></select><?
                            } ?>
                        </td>
                    </tr>
                <? } ?>
            </table>
        </div>
    <? } ?>
</div>
<?
$this->setFrameMode(true);
$currencyList = '';
if (!empty($arResult['CURRENCIES'])) {
    $templateLibrary[] = 'currency';
    $currencyList = CUtil::PhpToJSObject($arResult['CURRENCIES'], false, true, true);
}
$templateData = array(
    'TEMPLATE_LIBRARY' => $templateLibrary,
    'CURRENCIES' => $currencyList,
    'STORES' => array(
        "USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
        "SCHEDULE" => $arParams["SCHEDULE"],
        "USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
        "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
        "ELEMENT_ID" => $arResult["ID"],
        "STORE_PATH" => $arParams["STORE_PATH"],
        "MAIN_TITLE" => $arParams["MAIN_TITLE"],
        "MAX_AMOUNT" => $arParams["MAX_AMOUNT"],
        "USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
        "SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
        "SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
        "USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
        "USER_FIELDS" => $arParams['USER_FIELDS'],
        "FIELDS" => $arParams['FIELDS'],
        "STORES" => $arParams['STORES'],
    )
);
unset($currencyList, $templateLibrary);


// заменяем картинки цветов на соотв. картинки из предложений
foreach ($arResult['SKU_PROPS']['COLOR_REF']['VALUES'] as &$colorValue) {
    foreach ($arResult['OFFERS'] as $offer) {
        if($colorValue['XML_ID'] == $offer['PROPERTIES']['COLOR_REF']['VALUE']) {
            if(isset($offer['DETAIL_PICTURE']['ID'])) {
                $img_ = CFile::ResizeImageGet($offer['DETAIL_PICTURE']['ID'], array( "width" => 100, "height" => 100 ), BX_RESIZE_IMAGE_EXACT, true );
                $colorValue['PICT']['SRC'] = $img_['src'];
                $colorValue['PICT']['WIDTH'] = $img_['width'];
                $colorValue['PICT']['HEIGHT'] = $img_['height'];
            }
        }
    }
}


$arSkuTemplate = array();
if (!empty($arResult['SKU_PROPS'])) {
    $arSkuTemplate = CMShop::GetSKUPropsArray($arResult['SKU_PROPS'], $arResult["SKU_IBLOCK_ID"], "list", $arParams["OFFER_HIDE_NAME_PROPS"]);
}

//        echo '<pre>';print_r($arResult['SKU_PROPS']['COLOR_REF']['VALUES']);echo '</pre>';//die;
//echo '<pre>';print_r($arResult['OFFERS'][0]['PROPERTIES']['COLOR_REF']['VALUE']);echo '</pre>';//die;


$strMainID = $this->GetEditAreaId($arResult['ID']);

$strObName = 'ob' . preg_replace("/[^a-zA-Z0-9_]/", "x", $strMainID);

$arResult["strMainID"] = $this->GetEditAreaId($arResult['ID']);
$arItemIDs = CMShop::GetItemsIDs($arResult, "Y");
$totalCount = CMShop::GetTotalCount($arResult);
$arQuantityData = CMShop::GetQuantityArray($totalCount, $arItemIDs["ALL_ITEM_IDS"], "Y");

$arParams["BASKET_ITEMS"] = ($arParams["BASKET_ITEMS"] ? $arParams["BASKET_ITEMS"] : array());
$useStores = $arParams["USE_STORE"] == "Y" && $arResult["STORES_COUNT"] && $arQuantityData["RIGHTS"]["SHOW_QUANTITY"];
$showCustomOffer = (($arResult['OFFERS'] && $arParams["TYPE_SKU"] != "N") ? true : false);
if ($showCustomOffer) {
    $templateData['JS_OBJ'] = $strObName;
}
$strMeasure = '';
if ($arResult["OFFERS"]) {
    $strMeasure = $arResult["MIN_PRICE"]["CATALOG_MEASURE_NAME"];
    $templateData["STORES"]["OFFERS"] = "Y";
    foreach ($arResult["OFFERS"] as $key => $arOffer) {
        $templateData["STORES"]["OFFERS_ID"][] = $arOffer["ID"];
    }
} else {
    if (($arParams["SHOW_MEASURE"] == "Y") && ($arResult["CATALOG_MEASURE"])) {
        $arMeasure = CCatalogMeasure::getList(array(), array("ID" => $arResult["CATALOG_MEASURE"]), false, false, array())->GetNext();
        $strMeasure = $arMeasure["SYMBOL_RUS"];
    }
    $arAddToBasketData = CMShop::GetAddToBasketArray($arResult, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'big_btn', $arParams);
}
$arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);

// save item viewed
$arFirstPhoto = reset($arResult['MORE_PHOTO']);
$arViewedData = array(
    'PRODUCT_ID' => $arResult['ID'],
    'IBLOCK_ID' => $arResult['IBLOCK_ID'],
    'NAME' => $arResult['NAME'],
    'DETAIL_PAGE_URL' => $arResult['DETAIL_PAGE_URL'],
    'PICTURE_ID' => $arResult['PREVIEW_PICTURE'] ? $arResult['PREVIEW_PICTURE']['ID'] : ($arFirstPhoto ? $arFirstPhoto['ID'] : false),
    'CATALOG_MEASURE_NAME' => $arResult['CATALOG_MEASURE_NAME'],
    'MIN_PRICE' => $arResult['MIN_PRICE'],
    'CAN_BUY' => $arResult['CAN_BUY'] ? 'Y' : 'N',
    'IS_OFFER' => 'N',
    'WITH_OFFERS' => $arResult['OFFERS'] ? 'Y' : 'N',
);
?>
<script type="text/javascript">
    setViewedProduct(<?=$arResult['ID']?>, <?=CUtil::PhpToJSObject($arViewedData, false)?>);
</script>
<meta itemprop="name"
      content="<?= $name = strip_tags(!empty($arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE']) ? $arResult['IPROPERTY_VALUES']['ELEMENT_PAGE_TITLE'] : $arResult['NAME']) ?>"/>
<meta itemprop="category" content="<?= $arResult['CATEGORY_PATH'] ?>"/>
<meta itemprop="description"
      content="<?= (strlen(strip_tags($arResult['PREVIEW_TEXT'])) ? strip_tags($arResult['PREVIEW_TEXT']) : (strlen(strip_tags($arResult['DETAIL_TEXT'])) ? strip_tags($arResult['DETAIL_TEXT']) : $name)) ?>"/>
<div class="item_main_info <?= (!$showCustomOffer ? "noffer" : ""); ?>" id="<?= $arItemIDs["strMainID"]; ?>">
    <div class="img_wrapper">
        <div class="stickers">
            <? if (is_array($arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"])): ?>
                <? foreach ($arResult["PROPERTIES"]["HIT"]["VALUE_XML_ID"] as $key => $class) { ?>
                    <div class="sticker_<?= strtolower($class); ?>"
                         title="<?= $arResult["PROPERTIES"]["HIT"]["VALUE"][$key] ?>"></div>
                <? } ?>
            <? endif; ?>
        </div>
        <div class="item_slider">
            <? reset($arResult['MORE_PHOTO']);
            $arFirstPhoto = current($arResult['MORE_PHOTO']); ?>
            <div class="slides">
                <? if ($showCustomOffer && !empty($arResult['OFFERS_PROP'])) { ?>
                    <div class="offers_img wof">

                        <?
                        $alt = $arFirstPhoto["ALT"];
                        $title = $arFirstPhoto["TITLE"];
                        ?>
                        <? if ($arFirstPhoto["BIG"]["src"]) { ?>
                            <? $img_ = CFile::ResizeImageGet($arFirstPhoto["ID"], array( "width" => 320, "height" => 320 ), BX_RESIZE_IMAGE_EXACT, true );?>
                            <a href="<?= $arFirstPhoto["BIG"]["src"] ?>" class="fancy_offer" title="<?= $title; ?>">
                                <img id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>"
                                     <?/*src="<?= $arFirstPhoto['SMALL']['src']; ?>" alt="<?= $alt; ?>"*/?>
                                     src="<?= $img_['src']; ?>" alt="<?= $alt; ?>"
                                     title="<?= $title; ?>" itemprop="image">
                            </a>
                        <? } else { ?>
                            <a href="javascript:void(0)" class="fancy_offer" title="<?= $title; ?>">
                                <img id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PICT']; ?>"
                                     src="<?= $arFirstPhoto['SRC']; ?>" alt="<?= $alt; ?>" title="<?= $title; ?>"
                                     itemprop="image">
                            </a>
                        <? } ?>
                    </div>
                <? } else {
                    if ($arResult["MORE_PHOTO"]) {
                        ?>
                        <ul>
                            <? foreach ($arResult["MORE_PHOTO"] as $i => $arImage) {
                                ?>
                                <? $isEmpty = ($arImage["SMALL"]["src"] ? false : true);
                                $alt = $arImage["ALT"];
                                $title = $arImage["TITLE"];
                                ?>
                                <li id="photo-<?= $i ?>" <?= (!$i ? 'class="current"' : 'style="display: none;"') ?>>
                                    <? if (!$isEmpty) {
                                        ?>
                                        <a href="<?= $arImage["BIG"]["src"] ?>" <?= ($bIsOneImage ? '' : 'data-fancybox-group="item_slider"') ?>
                                           class="fancy" title="<?= $title; ?>">
                                            <img src="<?= $arImage["SMALL"]["src"] ?>" alt="<?= $alt; ?>"
                                                 title="<?= $title; ?>"<?= (!$i ? ' itemprop="image"' : '') ?> />
                                        </a>
                                        <?
                                    } else {
                                        ?>
                                        <img src="<?= $arImage["SRC"] ?>" alt="<?= $alt; ?>" title="<?= $title; ?>"/>
                                        <?
                                    } ?>
                                </li>
                                <?
                            } ?>
                        </ul>
                        <?
                    }
                } ?>
            </div>
            <? /*thumbs*/ ?>
            <? if (!$showCustomOffer || empty($arResult['OFFERS_PROP'])) {
                if (count($arResult["MORE_PHOTO"]) > 1):?>
                    <div class="wrapp_thumbs">
                        <div class="thumbs"
                             style="max-width:<?= ceil(((count($arResult['MORE_PHOTO']) <= 4 ? count($arResult['MORE_PHOTO']) : 4) * 70) - 10) ?>px;">
                            <ul class="slides_block" id="thumbs">
                                <? foreach ($arResult["MORE_PHOTO"] as $i => $arImage): ?>
                                    <li <?= (!$i ? 'class="current"' : '') ?>>
                                        <span><img src="<?= $arImage["THUMB"]["src"] ?>" alt="<?= $arImage["ALT"]; ?>"
                                                   title="<?= $arImage["TITLE"]; ?>"/></span>
                                    </li>
                                <? endforeach; ?>
                            </ul>
                        </div>
                        <span class="thumbs_navigation"></span>
                    </div>
                <? endif; ?>
            <? } else { ?>
                <?/*
                <div class="wrapp_thumbs">
                    <div class="sliders">
                        <div class="thumbs" style="">
                        </div>
                    </div>
                </div>
                */?>
            <? } ?>
        </div>
        <? /*mobile*/ ?>
        <? if (!$showCustomOffer || empty($arResult['OFFERS_PROP'])) { ?>
            <div class="item_slider flex">
                <ul class="slides">
                    <? if ($arResult["MORE_PHOTO"]) {
                        foreach ($arResult["MORE_PHOTO"] as $i => $arImage) {
                            ?>
                            <? $isEmpty = ($arImage["SMALL"]["src"] ? false : true); ?>
                            <li id="mphoto-<?= $i ?>" <?= (!$i ? 'class="current"' : 'style="display: none;"') ?>>
                                <?
                                $alt = $arImage["ALT"];
                                $title = $arImage["TITLE"];
                                ?>
                                <? if (!$isEmpty) {
                                    ?>
                                    <a href="<?= $arImage["BIG"]["src"] ?>" data-fancybox-group="item_slider_flex"
                                       class="fancy" title="<?= $title; ?>">
                                        <img src="<?= $arImage["SMALL"]["src"] ?>" alt="<?= $alt; ?>"
                                             title="<?= $title; ?>"/>
                                    </a>
                                    <?
                                } else {
                                    ?>
                                    <img src="<?= $arImage["SRC"]; ?>" alt="<?= $alt; ?>" title="<?= $title; ?>"/>
                                    <?
                                } ?>
                            </li>
                            <?
                        }
                    } ?>
                </ul>
            </div>
            <script type="text/javascript">
                $(".thumbs").flexslider({
                    animation: "slide",
                    selector: ".slides_block > li",
                    slideshow: false,
                    animationSpeed: 600,
                    directionNav: true,
                    controlNav: false,
                    pauseOnHover: true,
                    itemWidth: 60,
                    itemMargin: 10,
                    animationLoop: true,
                    controlsContainer: ".thumbs_navigation",
                });

                $(".item_slider.flex").flexslider({
                    animation: "slide",
                    slideshow: true,
                    slideshowSpeed: 10000,
                    animationSpeed: 600,
                    directionNav: false,
                    pauseOnHover: true,
                    animationLoop: false,
                });

                $('.item_slider .thumbs li').first().addClass('current');

                $('.item_slider .thumbs').delegate('li:not(.current)', 'click', function () {
                    $(this).addClass('current').siblings().removeClass('current').parents('.item_slider').find('.slides li').fadeOut(333);
                    $(this).parents('.item_slider').find('.slides li').eq($(this).index()).addClass('current').stop().fadeIn(333);
                });
            </script>
        <? }else{ ?>
            <div class="item_slider flex"></div>
        <? } ?>
    </div>
    <div class="right_info">
        <div class="info_item">
            <? if (($arParams["DISPLAY_WISH_BUTTONS"] != "N" && $arResult["CAN_BUY"]) || ($arParams["DISPLAY_COMPARE"] == "Y") || strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || $arResult["BRAND_ITEM"]) { ?>
                <div class="top_info">
                    <div class="wrap_md">
                        <? if ($arResult["BRAND_ITEM"]) { ?>
                            <div class="brand iblock">
                                <? if (!$arResult["BRAND_ITEM"]["IMAGE"]): ?>
                                    <b class="block_title"><?= GetMessage("BRAND"); ?>:</b>
                                    <a href="<?= $arResult["BRAND_ITEM"]["DETAIL_PAGE_URL"] ?>"><?= $arResult["BRAND_ITEM"]["NAME"] ?></a>
                                <? else: ?>
                                    <a class="brand_picture" href="<?= $arResult["BRAND_ITEM"]["DETAIL_PAGE_URL"] ?>">
                                        <img src="<?= $arResult["BRAND_ITEM"]["IMAGE"]["src"] ?>"
                                             alt="<?= $arResult["BRAND_ITEM"]["NAME"] ?>"
                                             title="<?= $arResult["BRAND_ITEM"]["NAME"] ?>"/>
                                    </a>
                                <? endif; ?>
                            </div>
                        <? } ?>
                        <? if ((($arParams["DISPLAY_WISH_BUTTONS"] != "N") || ($arParams["DISPLAY_COMPARE"] == "Y")) || (strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || ($arResult['SHOW_OFFERS_PROPS'] && $showCustomOffer))): ?>
                            <div>
                                <? if (($arParams["DISPLAY_WISH_BUTTONS"] != "N") || ($arParams["DISPLAY_COMPARE"] == "Y")): ?>
                                    <div class="like_icons iblock">
                                        <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N"): ?>
                                            <? if (!$arResult["OFFERS"]): ?>
                                                <div class="wish_item text" data-item="<?= $arResult["ID"] ?>"
                                                     data-iblock="<?= $arResult["IBLOCK_ID"] ?>">
                                                    <span class="value pseudo"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_IZB') ?>"><span><?= GetMessage('CT_BCE_CATALOG_IZB') ?></span></span>
                                                    <span class="value pseudo added"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_IZB_ADDED') ?>"><span><?= GetMessage('CT_BCE_CATALOG_IZB_ADDED') ?></span></span>
                                                </div>
                                            <? elseif ($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1' && !empty($arResult['OFFERS_PROP'])): ?>
                                                <div class="wish_item text " data-item="<?= $arResult["ID"] ?>"
                                                     data-iblock="<?= $arResult["IBLOCK_ID"] ?>" <?= (!empty($arResult['OFFERS_PROP']) ? 'data-offers="Y"' : ''); ?>
                                                     data-props="<?= $arOfferProps ?>">
                                                    <span class="value pseudo <?= $arParams["TYPE_SKU"]; ?>"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_IZB') ?>"><span><?= GetMessage('CT_BCE_CATALOG_IZB') ?></span></span>
                                                    <span class="value pseudo added <?= $arParams["TYPE_SKU"]; ?>"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_IZB_ADDED') ?>"><span><?= GetMessage('CT_BCE_CATALOG_IZB_ADDED') ?></span></span>
                                                </div>
                                            <? endif; ?>
                                        <? endif; ?>
                                        <? if ($arParams["DISPLAY_COMPARE"] == "Y"): ?>
                                            <? if (!$arResult["OFFERS"]): ?>
                                                <div data-item="<?= $arResult["ID"] ?>"
                                                     data-iblock="<?= $arResult["IBLOCK_ID"] ?>"
                                                     data-href="<?= $arResult["COMPARE_URL"] ?>"
                                                     class="compare_item text <?= ($arResult["OFFERS"] ? $arParams["TYPE_SKU"] : ""); ?>"
                                                     id="<? echo $arItemIDs["ALL_ITEM_IDS"]['COMPARE_LINK']; ?>">
                                                    <span class="value pseudo"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_COMPARE') ?>"><span><?= GetMessage('CT_BCE_CATALOG_COMPARE') ?></span></span>
                                                    <span class="value pseudo added"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_COMPARE_ADDED') ?>"><span><?= GetMessage('CT_BCE_CATALOG_COMPARE_ADDED') ?></span></span>
                                                </div>
                                            <? elseif ($arResult["OFFERS"] && $arParams["TYPE_SKU"] === 'TYPE_1'): ?>
                                                <div data-item="<?= $arResult["ID"] ?>"
                                                     data-iblock="<?= $arResult["IBLOCK_ID"] ?>"
                                                     data-href="<?= $arResult["COMPARE_URL"] ?>"
                                                     class="compare_item text <?= $arParams["TYPE_SKU"]; ?>">
                                                    <span class="value pseudo"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_COMPARE') ?>"><span><?= GetMessage('CT_BCE_CATALOG_COMPARE') ?></span></span>
                                                    <span class="value pseudo added"
                                                          title="<?= GetMessage('CT_BCE_CATALOG_COMPARE_ADDED') ?>"><span><?= GetMessage('CT_BCE_CATALOG_COMPARE_ADDED') ?></span></span>
                                                </div>
                                            <? endif; ?>
                                        <? endif; ?>
                                    </div>
                                <? endif; ?>
                                <? if (strlen($arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"]) || ($arResult['SHOW_OFFERS_PROPS'] && $showCustomOffer)): ?>
                                    <div class="article iblock" itemprop="additionalProperty" itemscope
                                         itemtype="http://schema.org/PropertyValue"
                                         <? if ($arResult['SHOW_OFFERS_PROPS']){ ?>id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_ARTICLE_DIV'] ?>"
                                         style="display: none;"<? } ?>>
                                        <span class="block_title" itemprop="name"><?= GetMessage("ARTICLE"); ?></span>
                                        <span class="value"
                                              itemprop="value"><?= $arResult["DISPLAY_PROPERTIES"]["CML2_ARTICLE"]["VALUE"] ?></span>
                                    </div>
                                <? endif; ?>
                            </div>
                        <? endif; ?>
                    </div>
                </div>
            <? } ?>
            <div class="middle_info wrap_md">
                <div class="prices_block iblock">
                    <div class="cost prices clearfix">
                        <? if (count($arResult["OFFERS"]) > 0) {
                            $minPrice = false;
                            $min_price_id = 0;
                            if (isset($arResult['MIN_PRICE']) || isset($arResult['RATIO_PRICE'])) {
                                // $minPrice = (isset($arResult['RATIO_PRICE']) ? $arResult['RATIO_PRICE'] : $arResult['MIN_PRICE']);
                                $minPrice = $arResult['MIN_PRICE'];
                            }
                            $offer_id = 0;
                            if ($arParams["TYPE_SKU"] == "N") {
                                $offer_id = $minPrice["MIN_ITEM_ID"];
                            }
                            $min_price_id = $minPrice["MIN_PRICE_ID"];
                            if (!$min_price_id)
                                $min_price_id = $minPrice["PRICE_ID"];
                            if ($minPrice["MIN_ITEM_ID"])
                                $item_id = $minPrice["MIN_ITEM_ID"];
                            $prefix = '';
                            if ('N' == $arParams['TYPE_SKU'] || $arParams['DISPLAY_TYPE'] == 'table' || empty($arResult['OFFERS_PROP'])) {
                                $prefix = GetMessage("CATALOG_FROM");
                            }
                            if ($arParams["SHOW_OLD_PRICE"] == "Y") {
                                ?>
                                <div class="price" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE']; ?>">
                                    <? if (strlen($minPrice["PRINT_DISCOUNT_VALUE"])): ?>
                                        <?= $prefix; ?> <?= $minPrice["PRINT_DISCOUNT_VALUE"]; ?>
                                        <? if (($arParams["SHOW_MEASURE"] == "Y") && $strMeasure) {
                                            ?>
                                            /<?= $strMeasure ?>
                                        <? } ?>
                                    <? endif; ?>
                                </div>
                                <div class="price discount">
                                    <strike id="<?= $arItemIDs["ALL_ITEM_IDS"]['OLD_PRICE'] ?>" <?= (!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '') ?>><?= $minPrice["PRINT_VALUE"]; ?></strike>
                                </div>
                                <? if ($arParams["SHOW_DISCOUNT_PERCENT"] == "Y") {
                                    ?>
                                    <div class="sale_block" <?= (!$minPrice["DISCOUNT_DIFF"] ? 'style="display:none;"' : '') ?>>
                                        <? if ($minPrice["DISCOUNT_DIFF_PERCENT"])
                                            $percent = $minPrice["DISCOUNT_DIFF_PERCENT"];
                                        else
                                            $percent = round(($minPrice["DISCOUNT_DIFF"] / $minPrice["VALUE"]) * 100, 2); ?>
                                        <div class="value">-<?= $percent; ?>%</div>
                                        <div class="text"><?= GetMessage("CATALOG_ECONOMY"); ?>
                                            <span><?= $minPrice["PRINT_DISCOUNT_DIFF"]; ?></span></div>
                                        <div class="clearfix"></div>
                                    </div>
                                <? } ?>
                            <? } else {
                                ?>
                                <div class="price" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE']; ?>">
                                    <? if (strlen($minPrice["PRINT_DISCOUNT_VALUE"])): ?>
                                        <?= $prefix; ?> <?= $minPrice['PRINT_DISCOUNT_VALUE']; ?>
                                        <? if (($arParams["SHOW_MEASURE"] == "Y") && $strMeasure) {
                                            ?>
                                            /<?= $strMeasure ?>
                                        <? } ?>
                                    <? endif; ?>
                                </div>
                            <? } ?>
                        <? } else { ?>
                            <?
                            $arCountPricesCanAccess = 0;
                            $min_price_id = 0;
                            foreach ($arResult["PRICES"] as $key => $arPrice) {
                                if ($arPrice["CAN_ACCESS"]) {
                                    $arCountPricesCanAccess++;
                                }
                            } ?>
                            <? foreach ($arResult["PRICES"] as $key => $arPrice) { ?>
                                <? if ($arPrice["CAN_ACCESS"]) {
                                    $percent = 0;
                                    if ($arPrice["MIN_PRICE"] == "Y") {
                                        $min_price_id = $arPrice["PRICE_ID"];
                                    } ?>
                                    <? $price = CPrice::GetByID($arPrice["ID"]); ?>
                                    <? if ($arCountPricesCanAccess > 1): ?>
                                        <div class="price_name"><?= $price["CATALOG_GROUP_NAME"]; ?></div>
                                    <? endif; ?>
                                    <? if ($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] && $arParams["SHOW_OLD_PRICE"] == "Y") { ?>
                                        <div class="price"
                                             id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE']; ?>"<?= ($bMinPrice ? ' itemprop="offers" itemscope itemtype="http://schema.org/Offer"' : '') ?>>
                                            <? if ($bMinPrice): ?>
                                                <meta itemprop="price"
                                                      content="<?= ($arPrice['DISCOUNT_VALUE'] ? $arPrice['DISCOUNT_VALUE'] : $arPrice['VALUE']) ?>"/>
                                                <meta itemprop="priceCurrency" content="<?= $arPrice['CURRENCY'] ?>"/>
                                                <link itemprop="availability"
                                                      href="http://schema.org/<?= ($arPrice['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
                                            <? endif; ?>
                                            <? if (strlen($arPrice["PRINT_DISCOUNT_VALUE"])): ?>
                                                <?= $arPrice["PRINT_DISCOUNT_VALUE"]; ?>
                                                <? if (($arParams["SHOW_MEASURE"] == "Y") && $strMeasure) { ?>
                                                    /<?= $strMeasure ?>
                                                <? } ?>
                                            <? endif; ?>
                                        </div>
                                        <div class="price discount">
                                            <strike><?= $arPrice["PRINT_VALUE"]; ?></strike>
                                        </div>
                                        <? if ($arParams["SHOW_DISCOUNT_PERCENT"] == "Y") { ?>
                                            <div class="sale_block">
                                                <? if ($arPrice["DISCOUNT_DIFF_PERCENT"])
                                                    $percent = $arPrice["DISCOUNT_DIFF_PERCENT"];
                                                else
                                                    $percent = round(($arPrice["DISCOUNT_DIFF"] / $arPrice["VALUE"]) * 100, 2); ?>
                                                <? if ($percent && $percent < 100) { ?>
                                                    <div class="value">-<?= $percent; ?>%</div>
                                                <? } ?>
                                                <div class="text"><?= GetMessage("CATALOG_ECONOMY"); ?>
                                                    <span><?= $arPrice["PRINT_DISCOUNT_DIFF"]; ?></span></div>
                                                <div class="clearfix"></div>
                                            </div>
                                        <? } ?>
                                    <? } else { ?>
                                        <div class="price" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PRICE']; ?>">
                                            <? if (strlen($arPrice["PRINT_VALUE"])): ?>
                                                <?= $arPrice["PRINT_VALUE"]; ?>
                                                <? if (($arParams["SHOW_MEASURE"] == "Y") && $strMeasure) { ?>
                                                    /<?= $strMeasure ?>
                                                <? } ?>
                                            <? endif; ?>
                                        </div>
                                    <? } ?>
                                <? } ?>
                            <? } ?>
                        <? } ?>
                    </div>
                    <? $arDiscounts = CCatalogDiscount::GetDiscountByProduct($arResult["ID"], $USER->GetUserGroupArray(), "N", $min_price_id, SITE_ID);
                    $arDiscount = array();
                    if ($arDiscounts)
                        $arDiscount = current($arDiscounts);
                    if ($arDiscount["ACTIVE_TO"]) {
                        ?>
                        <div class="view_sale_block">
                            <div class="count_d_block">
                                <span class="active_to_<?= $arResult["ID"] ?> hidden"><?= $arDiscount["ACTIVE_TO"]; ?></span>
                                <div class="title"><?= GetMessage("UNTIL_AKC"); ?></div>
                                <span class="countdown countdown_<?= $arResult["ID"] ?> values"></span>
                                <script>
                                    $(document).ready(function () {
                                        if ($('.countdown').size()) {
                                            var active_to = $('.active_to_<?=$arResult["ID"]?>').text(),
                                                date_to = new Date(active_to.replace(/(\d+)\.(\d+)\.(\d+)/, '$3/$2/$1'));
                                            $('.countdown_<?=$arResult["ID"]?>').countdown({
                                                until: date_to,
                                                format: 'dHMS',
                                                padZeroes: true,
                                                layout: '{d<}<span class="days item">{dnn}<div class="text">{dl}</div></span>{d>} <span class="hours item">{hnn}<div class="text">{hl}</div></span> <span class="minutes item">{mnn}<div class="text">{ml}</div></span> <span class="sec item">{snn}<div class="text">{sl}</div></span>'
                                            }, $.countdown.regionalOptions['ru']);
                                        }
                                    })
                                </script>
                            </div>
                            <div class="quantity_block">
                                <div class="title"><?= GetMessage("TITLE_QUANTITY_BLOCK"); ?></div>
                                <div class="values">
									<span class="item">
										<span class="value" <?= (count($arResult["OFFERS"]) > 0 ? 'style="opacity:0;"' : '') ?>><?= $totalCount; ?></span>
										<span class="text"><?= GetMessage("TITLE_QUANTITY"); ?></span>
									</span>
                                </div>
                            </div>
                        </div>
                    <? } ?>

                    <? if ($arParams["USE_RATING"] == "Y"): ?>
                        <div class="rating">
                            <? $APPLICATION->IncludeComponent(
                                "bitrix:iblock.vote",
                                "element_rating",
                                Array(
                                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                                    "IBLOCK_ID" => $arResult["IBLOCK_ID"],
                                    "ELEMENT_ID" => $arResult["ID"],
                                    "MAX_VOTE" => 5,
                                    "VOTE_NAMES" => array(),
                                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                                    "CACHE_TIME" => $arParams["CACHE_TIME"],
                                    "DISPLAY_AS_RATING" => 'vote_avg'
                                ),
                                $component, array("HIDE_ICONS" => "Y")
                            ); ?>
                        </div>
                    <? endif; ?>
                </div>
                <div class="buy_block iblock">
                    <? if ($arResult["OFFERS"] && $showCustomOffer) { ?>
                        <div class="sku_props">
                            <? if (!empty($arResult['OFFERS_PROP'])) { ?>
                                <? ///echo '<pre>';print_r($arSkuTemplate);echo '</pre>';die;
                                ?>
                                <div class="bx_catalog_item_scu wrapper_sku"
                                     id="<? echo $arItemIDs["ALL_ITEM_IDS"]['PROP_DIV']; ?>">
                                    <? foreach ($arSkuTemplate as $code => $strTemplate) {
                                        if (!isset($arResult['OFFERS_PROP'][$code]))
                                            continue;
                                        echo str_replace('#ITEM#_prop_', $arItemIDs["ALL_ITEM_IDS"]['PROP'], $strTemplate);
                                    } ?>
                                </div>
                            <? } ?>
                            <? $arItemJSParams = CMShop::GetSKUJSParams($arResult, $arParams, $arResult, "Y"); ?>
                            <script type="text/javascript">
                                var <? echo $arItemIDs["strObName"]; ?> =
                                new JCCatalogElement(<? echo CUtil::PhpToJSObject($arItemJSParams, false, true); ?>);
                            </script>
                        </div>
                    <? } ?>
                    <? if (!$arResult["OFFERS"]): ?>
                        <script>
                            $(document).ready(function () {
                                $('.catalog_detail .tabs_section .tabs_content .form.inline input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
                            });
                        </script>
                        <div class="counter_wrapp">
                            <? if (($arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && $arAddToBasketData["ACTION"] == "ADD") && $arResult["CAN_BUY"]): ?>
                                <div class="counter_block big_basket"
                                     data-offers="<?= ($arResult["OFFERS"] ? "Y" : "N"); ?>"
                                     data-item="<?= $arResult["ID"]; ?>" <?= (($arResult["OFFERS"] && $arParams["TYPE_SKU"] == "N") ? "style='display: none;'" : ""); ?>>
                                    <span class="minus"
                                          id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_DOWN']; ?>">-</span>
                                    <input type="text" class="text"
                                           id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY']; ?>"
                                           name="<? echo $arParams["PRODUCT_QUANTITY_VARIABLE"]; ?>"
                                           value="<?= $arAddToBasketData["MIN_QUANTITY_BUY"] ?>"/>
                                    <span class="plus"
                                          id="<? echo $arItemIDs["ALL_ITEM_IDS"]['QUANTITY_UP']; ?>" <?= ($arAddToBasketData["MAX_QUANTITY_BUY"] ? "data-max='" . $arAddToBasketData["MAX_QUANTITY_BUY"] . "'" : "") ?>>+</span>
                                </div>
                            <? endif; ?>
                            <div id="<? echo $arItemIDs["ALL_ITEM_IDS"]['BASKET_ACTIONS']; ?>"
                                 class="button_block <?= (($arAddToBasketData["ACTION"] == "ORDER" /*&& !$arResult["CAN_BUY"]*/) || !$arResult["CAN_BUY"] || !$arAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] || ($arAddToBasketData["ACTION"] == "SUBSCRIBE" && $arResult["CATALOG_SUBSCRIBE"] == "Y") ? "wide" : ""); ?>">
                                <!--noindex-->
                                <?= $arAddToBasketData["HTML"] ?>
                                <!--/noindex-->
                            </div>
                        </div>
                    <? if ($arAddToBasketData["ACTION"] !== "NOTHING"): ?>
                    <? if ($arAddToBasketData["ACTION"] == "ADD" && $arResult["CAN_BUY"] && $arParams["SHOW_ONE_CLICK_BUY"] != "N"): ?>
                        <div class="wrapp_one_click">
									<span class="transparent big_btn type_block button one_click"
                                          data-item="<?= $arResult["ID"] ?>"
                                          data-iblockID="<?= $arParams["IBLOCK_ID"] ?>"
                                          data-quantity="<?= $arAddToBasketData["MIN_QUANTITY_BUY"]; ?>"
                                          onclick="oneClickBuy('<?= $arResult["ID"] ?>', '<?= $arParams["IBLOCK_ID"] ?>', this)">
										<span><?= GetMessage('ONE_CLICK_BUY') ?></span>
									</span>
                        </div>
                    <? endif; ?>
                    <? endif; ?>
                    <? elseif ($arResult["OFFERS"] && $arParams['TYPE_SKU'] == 'TYPE_1'): ?>
                        <div class="offer_buy_block buys_wrapp" style="display:none;">
                            <div class="counter_wrapp"></div>
                        </div>
                    <? elseif ($arResult["OFFERS"] && $arParams['TYPE_SKU'] != 'TYPE_1'): ?>
                        <span class="big_btn slide_offer button type_block"><i></i><span><?= GetMessage("BUY_BTN"); ?></span></span>
                    <? endif; ?>
                </div>
                <? if (strlen($arResult["PREVIEW_TEXT"])): ?>
                    <div class="preview_text"><?= $arResult["PREVIEW_TEXT"] ?></div>
                <? endif; ?>
            </div>
            <? if (is_array($arResult["STOCK"]) && $arResult["STOCK"]): ?>
                <? foreach ($arResult["STOCK"] as $key => $arStockItem): ?>
                    <div class="stock_board">
                        <div class="title"><?= GetMessage("CATALOG_STOCK_TITLE") ?></div>
                        <div class="txt"><?= $arStockItem["PREVIEW_TEXT"] ?></div>
                        <a class="read_more"
                           href="<?= $arStockItem["DETAIL_PAGE_URL"] ?>"><?= GetMessage("CATALOG_STOCK_VIEW") ?></a>
                    </div>
                <? endforeach; ?>
            <? endif; ?>
            <div class="element_detail_text wrap_md">
                <div class="iblock sh">
                    <? $APPLICATION->IncludeFile(SITE_DIR . "include/share_buttons.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_SOC_BUTTON'))); ?>
                </div>
                <div class="iblock price_txt">
                    <? $APPLICATION->IncludeFile(SITE_DIR . "include/element_detail_text.php", Array(), Array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_DOP_DESCR'))); ?>
                </div>
            </div>
        </div>
    </div>
    <? if ($arResult['OFFERS']): ?>
        <span itemprop="offers" itemscope itemtype="http://schema.org/AggregateOffer" style="display:none;">
			<meta itemprop="lowPrice"
                  content="<?= ($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE']) ?>"/>
		    <meta itemprop="priceCurrency" content="<?= $arResult['MIN_PRICE']['CURRENCY'] ?>"/>
		    <meta itemprop="offerCount" content="<?= count($arResult['OFFERS']) ?>"/>
            <? foreach ($arResult['OFFERS'] as $arOffer): ?>
                <? $currentOffersList = array(); ?>
                <? foreach ($arOffer['TREE'] as $propName => $skuId): ?>
                    <? $propId = (int)substr($propName, 5); ?>
                    <? foreach ($arResult['SKU_PROPS'] as $prop): ?>
                        <? if ($prop['ID'] == $propId): ?>
                            <? foreach ($prop['VALUES'] as $propId => $propValue): ?>
                                <? if ($propId == $skuId): ?>
                                    <? $currentOffersList[] = $propValue['NAME']; ?>
                                    <? break; ?>
                                <? endif; ?>
                            <? endforeach; ?>
                        <? endif; ?>
                    <? endforeach; ?>
                <? endforeach; ?>
                <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<meta itemprop="sku" content="<?= implode('/', $currentOffersList) ?>"/>
					<a href="<?= $arOffer['DETAIL_PAGE_URL'] ?>" itemprop="url"></a>
					<meta itemprop="price"
                          content="<?= ($arOffer['MIN_PRICE']['DISCOUNT_VALUE']) ? $arOffer['MIN_PRICE']['DISCOUNT_VALUE'] : $arOffer['MIN_PRICE']['VALUE'] ?>"/>
					<meta itemprop="priceCurrency" content="<?= $arOffer['MIN_PRICE']['CURRENCY'] ?>"/>
					<link itemprop="availability"
                          href="http://schema.org/<?= ($arOffer['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
				</span>
            <? endforeach; ?>
		</span>
        <? unset($arOffer, $currentOffersList); ?>
    <? else: ?>
        <span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<meta itemprop="price"
                  content="<?= ($arResult['MIN_PRICE']['DISCOUNT_VALUE'] ? $arResult['MIN_PRICE']['DISCOUNT_VALUE'] : $arResult['MIN_PRICE']['VALUE']) ?>"/>
			<meta itemprop="priceCurrency" content="<?= $arResult['MIN_PRICE']['CURRENCY'] ?>"/>
			<link itemprop="availability"
                  href="http://schema.org/<?= ($arResult['MIN_PRICE']['CAN_BUY'] ? 'InStock' : 'OutOfStock') ?>"/>
		</span>
    <? endif; ?>
    <div class="clearleft"></div>

    <? if ($arParams["SHOW_KIT_PARTS"] == "Y" && $arResult["SET_ITEMS"]): ?>
        <div class="set_wrapp set_block">
            <div class="title"><?= GetMessage("GROUP_PARTS_TITLE") ?></div>
            <ul>
                <? foreach ($arResult["SET_ITEMS"] as $iii => $arSetItem): ?>
                    <li class="item">
                        <div class="item_inner">
                            <div class="image">
                                <a href="<?= $arSetItem["DETAIL_PAGE_URL"] ?>">
                                    <? if ($arSetItem["PREVIEW_PICTURE"]): ?>
                                        <? $img = CFile::ResizeImageGet($arSetItem["PREVIEW_PICTURE"], array("width" => 140, "height" => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true); ?>
                                        <img src="<?= $img["src"] ?>" alt="<?= $arSetItem["NAME"]; ?>"
                                             title="<?= $arSetItem["NAME"]; ?>"/>
                                    <? elseif ($arSetItem["DETAIL_PICTURE"]): ?>
                                        <? $img = CFile::ResizeImageGet($arSetItem["DETAIL_PICTURE"], array("width" => 140, "height" => 140), BX_RESIZE_IMAGE_PROPORTIONAL, true); ?>
                                        <img src="<?= $img["src"] ?>" alt="<?= $arSetItem["NAME"]; ?>"
                                             title="<?= $arSetItem["NAME"]; ?>"/>
                                    <? else: ?>
                                        <img src="<?= SITE_TEMPLATE_PATH ?>/images/no_photo_small.png"
                                             alt="<?= $arSetItem["NAME"]; ?>" title="<?= $arSetItem["NAME"]; ?>"/>
                                    <? endif; ?>
                                </a>
                            </div>
                            <div class="item_info">
                                <div class="item-title">
                                    <a href="<?= $arSetItem["DETAIL_PAGE_URL"] ?>"><span><?= $arSetItem["NAME"] ?></span></a>
                                </div>
                                <? if ($arParams["SHOW_KIT_PARTS_PRICES"] == "Y"): ?>
                                    <div class="cost prices clearfix">
                                        <?
                                        $arCountPricesCanAccess = 0;
                                        foreach ($arSetItem["PRICES"] as $key => $arPrice) {
                                            if ($arPrice["CAN_ACCESS"]) {
                                                $arCountPricesCanAccess++;
                                            }
                                        } ?>
                                        <? //foreach($arSetItem["PRICES"] as $key => $arPrice):?>
                                        <? foreach ($arSetItem["PRICES"] as $key => $arPrice): ?>
                                            <? if ($arPrice["CAN_ACCESS"]): ?>
                                                <? $price = CPrice::GetByID($arPrice["ID"]); ?>
                                                <? if ($arCountPricesCanAccess > 1): ?>
                                                    <div class="price_name"><?= $price["CATALOG_GROUP_NAME"]; ?></div>
                                                <? endif; ?>
                                                <? if ($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"] && $arParams["SHOW_OLD_PRICE"] == "Y"): ?>
                                                    <div class="price">
                                                        <?= $arPrice["PRINT_DISCOUNT_VALUE"]; ?>
                                                        <? if (($arParams["SHOW_MEASURE"] == "Y") && $strMeasure): ?>
                                                            <small>/<?= $strMeasure ?></small>
                                                        <? endif; ?>
                                                    </div>
                                                    <div class="price discount">
                                                        <strike><?= $arPrice["PRINT_VALUE"] ?></strike>
                                                    </div>
                                                <? else: ?>
                                                    <div class="price">
                                                        <?= $arPrice["PRINT_VALUE"]; ?>
                                                        <? if (($arParams["SHOW_MEASURE"] == "Y") && $strMeasure): ?>
                                                            <small>/<?= $strMeasure ?></small>
                                                        <? endif; ?>
                                                    </div>
                                                <? endif; ?>
                                            <? endif; ?>
                                        <? endforeach; ?>
                                        <? //endforeach;?>
                                    </div>
                                <? endif; ?>
                            </div>
                        </div>
                    </li>
                    <? if ($arResult["SET_ITEMS"][$iii + 1]): ?>
                        <li class="separator"></li>
                    <? endif; ?>
                <? endforeach; ?>
            </ul>
        </div>
    <? endif; ?>
    <? if ($arResult['OFFERS']): ?>
        <? if ($arResult['OFFER_GROUP']): ?>
            <? foreach ($arResult['OFFERS'] as $arOffer): ?>
                <? if (!$arOffer['OFFER_GROUP']) continue; ?>
                <span id="<?= $arItemIDs['ALL_ITEM_IDS']['OFFER_GROUP'] . $arOffer['ID'] ?>" style="display: none;">
					<? $APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", CMShop::checkVersionExt("main", "catalog", "Y"),
                        array(
                            "IBLOCK_ID" => $arResult["OFFERS_IBLOCK"],
                            "ELEMENT_ID" => $arOffer['ID'],
                            "PRICE_CODE" => $arParams["PRICE_CODE"],
                            "BASKET_URL" => $arParams["BASKET_URL"],
                            "OFFERS_CART_PROPERTIES" => $arParams["OFFERS_CART_PROPERTIES"],
                            "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                            "CACHE_TIME" => $arParams["CACHE_TIME"],
                            "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                            "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
                            "SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
                            "SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
                            "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                            "CURRENCY_ID" => $arParams["CURRENCY_ID"]
                        ), $component, array("HIDE_ICONS" => "Y")
                    ); ?>
				</span>
            <? endforeach; ?>
        <? endif; ?>
    <? else: ?>
        <? $APPLICATION->IncludeComponent("bitrix:catalog.set.constructor", CMShop::checkVersionExt("main", "catalog", "Y"),
            array(
                "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                "ELEMENT_ID" => $arResult["ID"],
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                "CACHE_TIME" => $arParams["CACHE_TIME"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
                "SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
                "SHOW_DISCOUNT_PERCENT" => $arParams["SHOW_DISCOUNT_PERCENT"],
                "CONVERT_CURRENCY" => $arParams['CONVERT_CURRENCY'],
                "CURRENCY_ID" => $arParams["CURRENCY_ID"]
            ), $component, array("HIDE_ICONS" => "Y")
        ); ?>
    <? endif; ?>
</div>

<div class="tabs_section">
    <ul class="tabs1 main_tabs1 tabs-head">
        <?
        $iTab = 0;
        $showProps = false;
        if ($arResult["DISPLAY_PROPERTIES"]) {
            foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp) {
                if (!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE"))) {
                    if (!is_array($arProp["DISPLAY_VALUE"])) {
                        $arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);
                    }
                }
                if (is_array($arProp["DISPLAY_VALUE"])) {
                    foreach ($arProp["DISPLAY_VALUE"] as $value) {
                        if (strlen($value)) {
                            $showProps = true;
                            break 2;
                        }
                    }
                }
            }
        }
        if (!$showProps && $arOffer['DISPLAY_PROPERTIES']) {
            foreach ($arResult['OFFERS'] as $arOffer) {
                foreach ($arOffer['DISPLAY_PROPERTIES'] as $arProp) {
                    if (!is_array($arProp["DISPLAY_VALUE"])) {
                        $arProp["DISPLAY_VALUE"] = array($arProp["DISPLAY_VALUE"]);
                    }

                    foreach ($arProp["DISPLAY_VALUE"] as $value) {
                        if (strlen($value)) {
                            $showProps = true;
                            break 3;
                        }
                    }
                }
            }
        }
        $arVideo = array();
        if (strlen($arResult["DISPLAY_PROPERTIES"]["VIDEO"]["VALUE"])) {
            $arVideo[] = $arResult["DISPLAY_PROPERTIES"]["VIDEO"]["~VALUE"];
        }
        if (isset($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])) {
            if (is_array($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])) {
                $arVideo = $arVideo + $arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["~VALUE"];
            } elseif (strlen($arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["VALUE"])) {
                $arVideo[] = $arResult["DISPLAY_PROPERTIES"]["VIDEO_YOUTUBE"]["~VALUE"];
            }
        }
        if (strlen($arResult["SECTION_FULL"]["UF_VIDEO"])) {
            $arVideo[] = $arResult["SECTION_FULL"]["~UF_VIDEO"];
        }
        if (strlen($arResult["SECTION_FULL"]["UF_VIDEO_YOUTUBE"])) {
            $arVideo[] = $arResult["SECTION_FULL"]["~UF_VIDEO_YOUTUBE"];
        }
        ?>
        <? if ($arResult["OFFERS"] && $arParams["TYPE_SKU"] == "N"): ?>
            <li class="prices_tab<?= (!($iTab++) ? ' current' : '') ?>">
                <span><?= GetMessage("OFFER_PRICES") ?></span>
            </li>
        <? endif; ?>
        <? if ($arResult["DETAIL_TEXT"] || count($arResult["STOCK"]) || count($arResult["SERVICES"]) || ((count($arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"]) && is_array($arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"])) || count($arResult["SECTION_FULL"]["UF_FILES"])) || ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"] != "TAB")): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <span><?= GetMessage("DESCRIPTION_TAB") ?></span>
            </li>
        <? endif; ?>

        <? if (!empty($arResult['OFFERS_PROP'])): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <span><?= GetMessage("SIZES") ?></span>
            </li>
        <? endif; ?>

        <? if ($arParams["PROPERTIES_DISPLAY_LOCATION"] == "TAB" && $showProps): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <span><?= GetMessage("PROPERTIES_TAB") ?></span>
            </li>
        <? endif; ?>

        <li class="<?= (!($iTab++) ? ' current' : '') ?>">
            <span><?php /*echo GetMessage("QUANTITY_TAB_NAME"); */?> Наличие </span>
        </li>

        <? if ($arVideo): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <span><?= GetMessage("VIDEO_TAB") ?></span>
                <? if (count($arVideo) > 1): ?>
                    <span class="count empty">&nbsp;(<?= count($arVideo) ?>)</span>
                <? endif; ?>
            </li>
        <? endif; ?>
        <!--		<? /*if($arParams["USE_REVIEW"] == "Y"):*/ ?>
			<li class="<? /*=(!($iTab++) ? ' current' : '')*/ ?>" id="product_reviews_tab">
				<span><? /*=GetMessage("REVIEW_TAB")*/ ?></span><span class="count empty"></span>
			</li>
		--><? /*endif;*/ ?>
        <!--		<? /*if(($arParams["SHOW_ASK_BLOCK"] == "Y") && (intVal($arParams["ASK_FORM_ID"]))):*/ ?>
			<li class="product_ask_tab <? /*=(!($iTab++) ? ' current' : '')*/ ?>">
				<span><? /*=GetMessage('ASK_TAB')*/ ?></span>
			</li>
		--><? /*endif;*/ ?>
        <!--		<? /*if($useStores && ($showCustomOffer || !$arResult["OFFERS"] )):*/ ?>
			<li class="stores_tab<? /*=(!($iTab++) ? ' current' : '')*/ ?>">
				<span><? /*=GetMessage("STORES_TAB");*/ ?></span>
			</li>
		<? /*endif;*/ ?>
		<? /*if($arParams["SHOW_ADDITIONAL_TAB"] == "Y"):*/ ?>
			<li class="<? /*=(!($iTab++) ? ' current' : '')*/ ?>">
				<span><? /*=GetMessage("ADDITIONAL_TAB");*/ ?></span>
			</li>
		--><? /*endif;*/ ?>
    </ul>
    <? if ($arResult["OFFERS"] && $arParams["TYPE_SKU"] !== "TYPE_1"): ?>
        <script>
            $(document).ready(function () {
                $('.catalog_detail .tabs_section .tabs_content .form.inline input[data-sid="PRODUCT_NAME"]').attr('value', $('h1').text());
            });
        </script>
    <? endif; ?>
    <ul class="tabs_content tabs-body">
        <? $show_tabs = false; ?>
        <? $iTab = 0; ?>
        <?
        $showSkUName = ((in_array('NAME', $arParams['OFFERS_FIELD_CODE'])));
        $showSkUImages = false;
        if (((in_array('PREVIEW_PICTURE', $arParams['OFFERS_FIELD_CODE']) || in_array('DETAIL_PICTURE', $arParams['OFFERS_FIELD_CODE'])))) {
            foreach ($arResult["OFFERS"] as $key => $arSKU) {
                if ($arSKU['PREVIEW_PICTURE'] || $arSKU['DETAIL_PICTURE']) {
                    $showSkUImages = true;
                    break;
                }
            }
        } ?>
        <? if ($arResult["OFFERS"] && $arParams["TYPE_SKU"] !== "TYPE_1"): ?>
            <li class="prices_tab<?= (!($iTab++) ? ' current' : '') ?>">
                <div class="bx_sku_props" style="display:none;">
                    <? $arSkuKeysProp = '';
                    $propSKU = $arParams["OFFERS_CART_PROPERTIES"];
                    if ($propSKU) {
                        $arSkuKeysProp = base64_encode(serialize(array_keys($propSKU)));
                    } ?>
                    <input type="hidden" value="<?= $arSkuKeysProp; ?>"></input>
                </div>
                <table class="colored offers_table">
                    <thead>
                    <tr>
                        <? if ($useStores): ?>
                            <td class="str"></td>
                        <? endif; ?>
                        <? if ($showSkUImages): ?>
                            <td class="property img" width="50"></td>
                        <? endif; ?>
                        <? if ($showSkUName): ?>
                            <td class="property"><?= GetMessage("CATALOG_NAME") ?></td>
                        <? endif; ?>
                        <?
                        if ($arResult["SKU_PROPERTIES"]) {
                            foreach ($arResult["SKU_PROPERTIES"] as $key => $arProp) {
                                ?>
                                <? if (!$arProp["IS_EMPTY"]): ?>
                                    <td class="property">
                                        <span <? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){
                                              ?>class="whint"<?
                                        } ?>><? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"): ?>
                                                <div class="hint"><span class="icon"><i>?</i></span><div
                                                        class="tooltip"><?= $arProp["HINT"] ?></div>
                                                </div><? endif; ?><?= $arProp["NAME"] ?></span>
                                    </td>
                                <? endif; ?>
                                <?
                            }
                        } ?>
                        <td class="price_th"><?= GetMessage("CATALOG_PRICE") ?></td>
                        <? if ($arQuantityData["RIGHTS"]["SHOW_QUANTITY"]): ?>
                            <td class="count_th"><?= GetMessage("AVAILABLE") ?></td>
                        <? endif; ?>
                        <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"): ?>
                            <td class="like_icons_th"></td>
                        <? endif; ?>
                        <td colspan="3"></td>
                    </tr>
                    </thead>
                    <tbody>
                    <? $numProps = count($arResult["SKU_PROPERTIES"]);
                    if ($arResult["OFFERS"]) {
                        foreach ($arResult["OFFERS"] as $key => $arSKU) {
                            ?>
                            <?
                            if ($arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"]) {
                                $sMeasure = $arResult["PROPERTIES"]["CML2_BASE_UNIT"]["VALUE"] . ".";
                            } else {
                                $sMeasure = GetMessage("MEASURE_DEFAULT") . ".";
                            }
                            $skutotalCount = CMShop::CheckTypeCount($arSKU["CATALOG_QUANTITY"]);
                            $arskuQuantityData = CMShop::GetQuantityArray($skutotalCount, array('quantity-wrapp', 'quantity-indicators'));
                            $arSKU["IBLOCK_ID"] = $arResult["IBLOCK_ID"];
                            $arSKU["IS_OFFER"] = "Y";
                            $arskuAddToBasketData = CMShop::GetAddToBasketArray($arSKU, $skutotalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, array(), 'small', $arParams);
                            $arskuAddToBasketData["HTML"] = str_replace('data-item', 'data-props="' . $arOfferProps . '" data-item', $arskuAddToBasketData["HTML"]);
                            ?>
                            <? $collspan = 1; ?>
                            <tr>
                                <? if ($useStores): ?>
                                    <td class="opener">
                                        <? $collspan++; ?>
                                        <span class="opener_icon"><i></i></span>
                                    </td>
                                <? endif; ?>
                                <? if ($showSkUImages): ?>
                                    <? $collspan++; ?>
                                    <td class="property">
                                        <?
                                        $srcImgPreview = $srcImgDetail = false;
                                        $imgPreviewID = ($arResult['OFFERS'][$key]['PREVIEW_PICTURE'] ? (is_array($arResult['OFFERS'][$key]['PREVIEW_PICTURE']) ? $arResult['OFFERS'][$key]['PREVIEW_PICTURE']['ID'] : $arResult['OFFERS'][$key]['PREVIEW_PICTURE']) : false);
                                        $imgDetailID = ($arResult['OFFERS'][$key]['DETAIL_PICTURE'] ? (is_array($arResult['OFFERS'][$key]['DETAIL_PICTURE']) ? $arResult['OFFERS'][$key]['DETAIL_PICTURE']['ID'] : $arResult['OFFERS'][$key]['DETAIL_PICTURE']) : false);
                                        if ($imgPreviewID || $imgDetailID) {
                                            $arImgPreview = CFile::ResizeImageGet($imgPreviewID ? $imgPreviewID : $imgDetailID, array('width' => 50, 'height' => 50), BX_RESIZE_IMAGE_PROPORTIONAL, true);
                                            $srcImgPreview = $arImgPreview['src'];
                                        }
                                        if ($imgDetailID) {
                                            $srcImgDetail = CFile::GetPath($imgDetailID);
                                        }
                                        ?>
                                        <? if ($srcImgPreview || $srcImgDetail): ?>
                                            <a href="<?= ($srcImgDetail ? $srcImgDetail : $srcImgPreview) ?>"
                                               class="fancy" data-fancybox-group="item_slider"><img
                                                        src="<?= $srcImgPreview ?>" alt="<?= $arSKU['NAME'] ?>"/></a>
                                        <? endif; ?>
                                    </td>
                                <? endif; ?>
                                <? if ($showSkUName): ?>
                                    <? $collspan++; ?>
                                    <td class="property"><?= $arSKU['NAME'] ?></td>
                                <? endif; ?>
                                <? foreach ($arResult["SKU_PROPERTIES"] as $arProp) {
                                    ?>
                                    <? if (!$arProp["IS_EMPTY"]): ?>
                                        <? $collspan++; ?>
                                        <td class="property">

                                            <? if ($arResult["TMP_OFFERS_PROP"][$arProp["CODE"]]) {
                                                echo $arResult["TMP_OFFERS_PROP"][$arProp["CODE"]]["VALUES"][$arSKU["TREE"]["PROP_" . $arProp["ID"]]]["NAME"]; ?>
                                                <?
                                            } else {
                                                if (is_array($arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"])) {
                                                    echo implode("/", $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"]);
                                                } else {
                                                    if ($arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE"] == "directory" && isset($arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE_SETTINGS"]["TABLE_NAME"])) {
                                                        $rsData = \Bitrix\Highloadblock\HighloadBlockTable::getList(array('filter' => array('=TABLE_NAME' => $arSKU["PROPERTIES"][$arProp["CODE"]]["USER_TYPE_SETTINGS"]["TABLE_NAME"])));
                                                        if ($arData = $rsData->fetch()) {
                                                            $entity = \Bitrix\Highloadblock\HighloadBlockTable::compileEntity($arData);
                                                            $entityDataClass = $entity->getDataClass();
                                                            $arFilter = array(
                                                                'limit' => 1,
                                                                'filter' => array(
                                                                    '=UF_XML_ID' => $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"]
                                                                )
                                                            );
                                                            $arValue = $entityDataClass::getList($arFilter)->fetch();
                                                            if (isset($arValue["UF_NAME"]) && $arValue["UF_NAME"]) {
                                                                echo $arValue["UF_NAME"];
                                                            } else {
                                                                echo $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"];
                                                            }
                                                        }
                                                    } else {
                                                        echo $arSKU["PROPERTIES"][$arProp["CODE"]]["VALUE"];
                                                    }
                                                }
                                            } ?>
                                        </td>
                                    <? endif; ?>
                                    <?
                                } ?>
                                <td class="price">
                                    <div class="cost prices clearfix">
                                        <?
                                        $collspan++;
                                        $arCountPricesCanAccess = 0;
                                        foreach ($arSKU["PRICES"] as $key => $arPrice) {
                                            if ($arPrice["CAN_ACCESS"]) {
                                                $arCountPricesCanAccess++;
                                            }
                                        } ?>
                                        <? foreach ($arSKU["PRICES"] as $key => $arPrice) {
                                            ?>
                                            <? if ($arPrice["CAN_ACCESS"]) {
                                                ?>
                                                <? if ($arCountPricesCanAccess > 1): ?>
                                                    <? $price = CPrice::GetByID($arPrice["ID"]); ?>
                                                    <div class="many_prices">
                                                    <div class="price_name"><?= $price["CATALOG_GROUP_NAME"]; ?></div>
                                                <? endif; ?>
                                                <? if ($arPrice["VALUE"] > $arPrice["DISCOUNT_VALUE"]) {
                                                    ?>
                                                    <div class="price">
                                                        <?= $arPrice["PRINT_DISCOUNT_VALUE"]; ?>
                                                        <? if (($arParams["SHOW_MEASURE"] == "Y") && $arSKU["CATALOG_MEASURE_NAME"]): ?>
                                                            <small>
                                                            /<?= $arSKU["CATALOG_MEASURE_NAME"] ?></small><? endif; ?>
                                                    </div>
                                                    <div class="price discount">
                                                        <strike><?= $arPrice["PRINT_VALUE"]; ?></strike>
                                                    </div>
                                                    <?
                                                } else {
                                                    ?>
                                                    <div class="price">
                                                        <?= $arPrice["PRINT_VALUE"]; ?>
                                                        <? if (($arParams["SHOW_MEASURE"] == "Y") && $arSKU["CATALOG_MEASURE_NAME"]): ?>
                                                            <small>
                                                            /<?= $arSKU["CATALOG_MEASURE_NAME"] ?></small><? endif; ?>
                                                    </div>
                                                    <?
                                                } ?>
                                                <? if ($arCountPricesCanAccess > 1) {
                                                    ?>
                                                    </div>
                                                    <?
                                                } ?>
                                                <?
                                            } ?>
                                            <?
                                        } ?>
                                    </div>
                                    <div class="adaptive text">
                                        <? if (strlen($arskuQuantityData["TEXT"])): ?>
                                            <div class="count ablock">
                                                <?= $arskuQuantityData["HTML"] ?>
                                            </div>
                                        <? endif; ?>
                                        <!--noindex-->
                                        <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"): ?>
                                            <div class="like_icons ablock">
                                                <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N"): ?>
                                                    <? if ($arSKU['CAN_BUY']): ?>
                                                        <div class="wish_item_button o_<?= $arSKU["ID"]; ?>">
                                                            <span title="<?= GetMessage('CATALOG_WISH') ?>"
                                                                  class="wish_item text to <?= $arParams["TYPE_SKU"]; ?>"
                                                                  data-item="<?= $arSKU["ID"] ?>"
                                                                  data-iblock="<?= $arResult["IBLOCK_ID"] ?>"
                                                                  data-offers="Y"
                                                                  data-props="<?= $arOfferProps ?>"><i></i></span>
                                                            <span title="<?= GetMessage('CATALOG_WISH_OUT') ?>"
                                                                  class="wish_item text in added <?= $arParams["TYPE_SKU"]; ?>"
                                                                  style="display: none;" data-item="<?= $arSKU["ID"] ?>"
                                                                  data-iblock="<?= $arSKU["IBLOCK_ID"] ?>"><i></i></span>
                                                        </div>
                                                    <? endif; ?>
                                                <? endif; ?>
                                                <? if ($arParams["DISPLAY_COMPARE"] == "Y"): ?>
                                                    <div class="compare_item_button o_<?= $arSKU["ID"]; ?>">
                                                        <span title="<?= GetMessage('CATALOG_COMPARE') ?>"
                                                              class="compare_item to text <?= $arParams["TYPE_SKU"]; ?>"
                                                              data-iblock="<?= $arParams["IBLOCK_ID"] ?>"
                                                              data-item="<?= $arSKU["ID"] ?>"><i></i></span>
                                                        <span title="<?= GetMessage('CATALOG_COMPARE_OUT') ?>"
                                                              class="compare_item in added text <?= $arParams["TYPE_SKU"]; ?>"
                                                              style="display: none;"
                                                              data-iblock="<?= $arParams["IBLOCK_ID"] ?>"
                                                              data-item="<?= $arSKU["ID"] ?>"><i></i></span>
                                                    </div>
                                                <? endif; ?>
                                            </div>
                                        <? endif; ?>
                                        <div class="wrap_md">
                                            <? if ($arskuAddToBasketData["ACTION"] == "ADD"): ?>
                                                <? if ($arskuAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && !count($arSKU["OFFERS"]) && $arskuAddToBasketData["ACTION"] == "ADD" && $arSKU["CAN_BUY"]): ?>
                                                    <div class="counter_block_wr iblock ablock">
                                                        <div class="counter_block" data-item="<?= $arSKU["ID"]; ?>">
                                                            <span class="minus">-</span>
                                                            <input type="text" class="text" name="count_items"
                                                                   value="<?= $arskuAddToBasketData["MIN_QUANTITY_BUY"]; ?>"/>
                                                            <span class="plus">+</span>
                                                        </div>
                                                    </div>
                                                <? endif; ?>
                                            <? endif; ?>
                                            <div class="buy iblock ablock">
                                                <div class="counter_wrapp">
                                                    <?= $arskuAddToBasketData["HTML"] ?>
                                                </div>
                                            </div>
                                        </div>
                                        <? if ($arskuAddToBasketData["ACTION"] == "ADD" && $arSKU["CAN_BUY"] && $arParams["SHOW_ONE_CLICK_BUY"] != "N"): ?>
                                            <div class="one_click_buy ablock">
														<span class="button small transparent one_click"
                                                              data-item="<?= $arSKU["ID"] ?>" data-offers="Y"
                                                              data-iblockID="<?= $arParams["IBLOCK_ID"] ?>"
                                                              data-quantity="<?= $arskuAddToBasketData["MIN_QUANTITY_BUY"]; ?>"
                                                              data-props="<?= $arOfferProps ?>"
                                                              onclick="oneClickBuy('<?= $arSKU["ID"] ?>', '<?= $arParams["IBLOCK_ID"] ?>', this)">
															<span><?= GetMessage('ONE_CLICK_BUY') ?></span>
														</span>
                                            </div>
                                        <? endif; ?>
                                        <!--/noindex-->
                                    </div>
                                </td>
                                <? if (strlen($arskuQuantityData["TEXT"])): ?>
                                    <? $collspan++; ?>
                                    <td class="count">
                                        <?= $arskuQuantityData["HTML"] ?>
                                    </td>
                                <? endif; ?>
                                <!--noindex-->
                                <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N" || $arParams["DISPLAY_COMPARE"] == "Y"): ?>
                                    <td class="like_icons">
                                        <? $collspan++; ?>
                                        <? if ($arParams["DISPLAY_WISH_BUTTONS"] != "N"): ?>
                                            <? if ($arSKU['CAN_BUY']): ?>
                                                <div class="wish_item_button o_<?= $arSKU["ID"]; ?>">
                                                    <span title="<?= GetMessage('CATALOG_WISH') ?>"
                                                          class="wish_item text to <?= $arParams["TYPE_SKU"]; ?>"
                                                          data-item="<?= $arSKU["ID"] ?>"
                                                          data-iblock="<?= $arResult["IBLOCK_ID"] ?>" data-offers="Y"
                                                          data-props="<?= $arOfferProps ?>"><i></i></span>
                                                    <span title="<?= GetMessage('CATALOG_WISH_OUT') ?>"
                                                          class="wish_item text in added <?= $arParams["TYPE_SKU"]; ?>"
                                                          style="display: none;" data-item="<?= $arSKU["ID"] ?>"
                                                          data-iblock="<?= $arSKU["IBLOCK_ID"] ?>"><i></i></span>
                                                </div>
                                            <? endif; ?>
                                        <? endif; ?>
                                        <? if ($arParams["DISPLAY_COMPARE"] == "Y"): ?>
                                            <div class="compare_item_button o_<?= $arSKU["ID"]; ?>">
                                                <span title="<?= GetMessage('CATALOG_COMPARE') ?>"
                                                      class="compare_item to text <?= $arParams["TYPE_SKU"]; ?>"
                                                      data-iblock="<?= $arParams["IBLOCK_ID"] ?>"
                                                      data-item="<?= $arSKU["ID"] ?>"><i></i></span>
                                                <span title="<?= GetMessage('CATALOG_COMPARE_OUT') ?>"
                                                      class="compare_item in added text <?= $arParams["TYPE_SKU"]; ?>"
                                                      style="display: none;" data-iblock="<?= $arParams["IBLOCK_ID"] ?>"
                                                      data-item="<?= $arSKU["ID"] ?>"><i></i></span>
                                            </div>
                                        <? endif; ?>
                                    </td>
                                <? endif; ?>
                                <? if ($arskuAddToBasketData["ACTION"] == "ADD"): ?>
                                    <? if ($arskuAddToBasketData["OPTIONS"]["USE_PRODUCT_QUANTITY_DETAIL"] && !count($arSKU["OFFERS"]) && $arskuAddToBasketData["ACTION"] == "ADD" && $arSKU["CAN_BUY"]): ?>
                                        <td class="counter_block_wr">
                                            <div class="counter_block" data-item="<?= $arSKU["ID"]; ?>">
                                                <? $collspan++; ?>
                                                <span class="minus">-</span>
                                                <input type="text" class="text" name="count_items"
                                                       value="<?= $arskuAddToBasketData["MIN_QUANTITY_BUY"]; ?>"/>
                                                <span class="plus">+</span>
                                            </div>
                                        </td>
                                    <? endif; ?>
                                <? endif; ?>
                                <td class="buy" <?= ($arskuAddToBasketData["ACTION"] !== "ADD" || !$arSKU["CAN_BUY"] || $arParams["SHOW_ONE_CLICK_BUY"] == "N" ? 'colspan="3"' : "") ?>>
                                    <? if ($arskuAddToBasketData["ACTION"] !== "ADD" || !$arSKU["CAN_BUY"]): ?>
                                        <? $collspan += 3; ?>
                                        <?
                                    else:?>
                                        <? $collspan++; ?>
                                    <? endif; ?>
                                    <div class="counter_wrapp">
                                        <?= $arskuAddToBasketData["HTML"] ?>
                                    </div>
                                </td>
                                <? if ($arskuAddToBasketData["ACTION"] == "ADD" && $arSKU["CAN_BUY"] && $arParams["SHOW_ONE_CLICK_BUY"] != "N"): ?>
                                    <td class="one_click_buy">
                                        <? $collspan++; ?>
                                        <span class="button small transparent one_click" data-item="<?= $arSKU["ID"] ?>"
                                              data-offers="Y" data-iblockID="<?= $arParams["IBLOCK_ID"] ?>"
                                              data-quantity="<?= $arskuAddToBasketData["MIN_QUANTITY_BUY"]; ?>"
                                              data-props="<?= $arOfferProps ?>"
                                              onclick="oneClickBuy('<?= $arSKU["ID"] ?>', '<?= $arParams["IBLOCK_ID"] ?>', this)">
													<span><?= GetMessage('ONE_CLICK_BUY') ?></span>
												</span>
                                    </td>
                                <? endif; ?>
                                <!--/noindex-->
                            </tr>
                            <? if ($useStores): ?>
                                <? $collspan--; ?>
                                <tr class="offer_stores">
                                    <td colspan="<?= $collspan ?>">
                                        <? $APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
                                            "PER_PAGE" => "10",
                                            "USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
                                            "SCHEDULE" => $arParams["SCHEDULE"],
                                            "USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
                                            "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                                            "ELEMENT_ID" => $arSKU["ID"],
                                            "STORE_PATH" => $arParams["STORE_PATH"],
                                            "MAIN_TITLE" => $arParams["MAIN_TITLE"],
                                            "MAX_AMOUNT" => $arParams["MAX_AMOUNT"],
                                            "SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
                                            "SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                                            "USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
                                            "USER_FIELDS" => $arParams['USER_FIELDS'],
                                            "FIELDS" => $arParams['FIELDS'],
                                            "STORES" => $arParams['STORES'],
                                            "CACHE_TYPE" => "A",
                                        ),
                                            $component
                                        ); ?>
                                </tr>
                            <? endif; ?>
                            <?
                        }
                    } ?>
                    </tbody>
                </table>
            </li>
        <? endif; ?>
        <? if ($arResult["DETAIL_TEXT"] || count($arResult["STOCK"]) || count($arResult["SERVICES"]) || ((count($arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"]) && is_array($arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"])) || count($arResult["SECTION_FULL"]["UF_FILES"])) || ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"] != "TAB")): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <? if (strlen($arResult["DETAIL_TEXT"])): ?>
                    <div class="detail_text"><?= $arResult["DETAIL_TEXT"] ?></div>
                <? endif; ?>
                <? if ($arResult["SERVICES"] && $showProps){ ?>
                <div class="wrap_md descr_div">
                    <? } ?>
                    <? if ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"] != "TAB"): ?>
                        <? if ($arParams["PROPERTIES_DISPLAY_TYPE"] != "TABLE"): ?>
                            <div class="props_block" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>">
                                <? foreach ($arResult["PROPERTIES"] as $propCode => $arProp): ?>
                                    <? if (isset($arResult["DISPLAY_PROPERTIES"][$propCode])): ?>
                                        <? $arProp = $arResult["DISPLAY_PROPERTIES"][$propCode]; ?>
                                        <? if (!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE"))): ?>
                                            <? if ((!is_array($arProp["DISPLAY_VALUE"]) && strlen($arProp["DISPLAY_VALUE"])) || (is_array($arProp["DISPLAY_VALUE"]) && implode('', $arProp["DISPLAY_VALUE"]))): ?>
                                                <div class="char" itemprop="additionalProperty" itemscope
                                                     itemtype="http://schema.org/PropertyValue">
                                                    <div class="char_name">
                                                        <span
                                                            <? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){ ?>class="whint"<? } ?>><? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"): ?>
                                                                <div class="hint"><span class="icon"><i>?</i></span><div
                                                                        class="tooltip"><?= $arProp["HINT"] ?></div>
                                                                </div><? endif; ?><span
                                                                    itemprop="name"><?= ($arProp["~NAME"] ? $arProp["~NAME"] : $arProp["NAME"]); ?></span></span>
                                                    </div>
                                                    <div class="char_value" itemprop="value">
                                                        <? if (count($arProp["DISPLAY_VALUE"]) > 1): ?>
                                                            <?= implode(', ', $arProp["DISPLAY_VALUE"]); ?>
                                                        <? else: ?>
                                                            <?= $arProp["DISPLAY_VALUE"]; ?>
                                                        <? endif; ?>
                                                    </div>
                                                </div>
                                            <? endif; ?>
                                        <? endif; ?>
                                    <? endif; ?>
                                <? endforeach; ?>
                            </div>
                        <? else: ?>
                            <div class="iblock char_block <?= (!$arResult["SERVICES"] ? 'wide' : '') ?>">
                                <h4><?= GetMessage("PROPERTIES_TAB"); ?></h4>
                                <table class="props_list">
                                    <? foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp): ?>
                                        <? if (!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE"))): ?>
                                            <? if ((!is_array($arProp["DISPLAY_VALUE"]) && strlen($arProp["DISPLAY_VALUE"])) || (is_array($arProp["DISPLAY_VALUE"]) && implode('', $arProp["DISPLAY_VALUE"]))): ?>
                                                <tr itemprop="additionalProperty" itemscope
                                                    itemtype="http://schema.org/PropertyValue">
                                                    <td class="char_name">

                                                        <span
                                                            <? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){ ?>class="whint"<? } ?>><? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"): ?>
                                                                <div class="hint"><span class="icon"><i>?</i></span><div
                                                                        class="tooltip"><?= $arProp["HINT"] ?></div>
                                                                </div><? endif; ?><span
                                                                    itemprop="name"><?= ($arProp["~NAME"] ? $arProp["~NAME"] : $arProp["NAME"]); ?></span></span>
                                                    </td>
                                                    <td class="char_value">
													<span itemprop="value">
														<? if (count($arProp["DISPLAY_VALUE"]) > 1): ?>
                                                            <?= implode(', ', $arProp["DISPLAY_VALUE"]); ?>
                                                        <? else: ?>
                                                            <?= $arProp["DISPLAY_VALUE"]; ?>
                                                        <? endif; ?>
													</span>
                                                    </td>
                                                </tr>
                                            <? endif; ?>
                                        <? endif; ?>
                                    <? endforeach; ?>
                                </table>
                                <table class="props_list"
                                       id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>"></table>
                            </div>
                        <? endif; ?>
                    <? endif; ?>
                    <? if ($arResult["SERVICES"]): ?>
                        <div class="iblock serv <?= ($arParams["PROPERTIES_DISPLAY_TYPE"] != "TABLE" ? "block_view" : "") ?>">
                            <h4><?= GetMessage("SERVICES_TITLE") ?></h4>
                            <div class="services_block">
                                <? foreach ($arResult["SERVICES"] as $arService): ?>
                                    <span class="item">
									<a href="<?= $arService["DETAIL_PAGE_URL"] ?>">
										<i class="arrow"><b></b></i>
										<span class="link"><?= $arService["NAME"] ?></span>
									</a>
								</span>
                                <? endforeach; ?>
                            </div>
                        </div>
                    <? endif; ?>
                    <? if ($arResult["SERVICES"] && $showProps){ ?>
                </div>
            <? } ?>
                <?
                $arFiles = array();
                if ($arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"]) {
                    $arFiles = $arResult["PROPERTIES"]["INSTRUCTIONS"]["VALUE"];
                } else {
                    $arFiles = $arResult["SECTION_FULL"]["UF_FILES"];
                }
                if (is_array($arFiles)) {
                    foreach ($arFiles as $key => $value) {
                        if (!intval($value)) {
                            unset($arFiles[$key]);
                        }
                    }
                }
                ?>
                <? if ($arFiles): ?>
                    <div class="files_block">
                        <h4><?= GetMessage("DOCUMENTS_TITLE") ?></h4>
                        <div class="wrap_md">
                            <div class="wrapp_docs iblock">
                                <?
                                $i = 1;
                                foreach ($arFiles as $arItem): ?>
                                <? $arFile = CMShop::GetFileInfo($arItem); ?>
                                <div class="file_type clearfix <?= $arFile["TYPE"]; ?>">
                                    <i class="icon"></i>
                                    <div class="description">
                                        <a target="_blank"
                                           href="<?= $arFile["SRC"]; ?>"><?= $arFile["DESCRIPTION"]; ?></a>
                                        <span class="size"><?= GetMessage('CT_NAME_SIZE') ?>:
                                            <?= $arFile["FILE_SIZE_FORMAT"]; ?>
										</span>
                                    </div>
                                </div>
                                <? if ($i % 3 == 0){
                                ?>
                            </div>
                            <div class="wrapp_docs iblock">
                                <? } ?>
                                <? $i++; ?>
                                <? endforeach; ?>
                            </div>
                        </div>
                    </div>
                <? endif; ?>
            </li>
        <? endif; ?>

        <? if (!empty($arResult['OFFERS_PROP'])): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <div class="product-sizes">
                    <? foreach ($arResult['OFFERS'] as $k => $item) { ?>
                        <div>
                            <? echo $item['PROPERTIES']['SIZES']['VALUE']; ?>
                        </div>
                    <? } ?>
                </div>
            </li>
        <? endif; ?>

        <? if ($showProps && $arParams["PROPERTIES_DISPLAY_LOCATION"] == "TAB"): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <? if ($arParams["PROPERTIES_DISPLAY_TYPE"] != "TABLE"): ?>
                    <div class="props_block" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>">
                        <? foreach ($arResult["PROPERTIES"] as $propCode => $arProp): ?>
                            <? if (isset($arResult["DISPLAY_PROPERTIES"][$propCode])): ?>
                                <? $arProp = $arResult["DISPLAY_PROPERTIES"][$propCode]; ?>
                                <? if (!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE"))): ?>
                                    <? if ((!is_array($arProp["DISPLAY_VALUE"]) && strlen($arProp["DISPLAY_VALUE"])) || (is_array($arProp["DISPLAY_VALUE"]) && implode('', $arProp["DISPLAY_VALUE"]))): ?>
                                        <div class="char" itemprop="additionalProperty" itemscope
                                             itemtype="http://schema.org/PropertyValue">
                                            <div class="char_name">
                                                <span
                                                    <? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){ ?>class="whint"<? } ?>><? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"): ?>
                                                        <div class="hint"><span class="icon"><i>?</i></span><div
                                                                class="tooltip"><?= $arProp["HINT"] ?></div>
                                                        </div><? endif; ?><span
                                                            itemprop="name"><?= ($arProp["~NAME"] ? $arProp["~NAME"] : $arProp["NAME"]); ?></span></span>
                                            </div>
                                            <div class="char_value" itemprop="value">
                                                <? if (count($arProp["DISPLAY_VALUE"]) > 1): ?>
                                                    <?= implode(', ', $arProp["DISPLAY_VALUE"]); ?>
                                                <? else: ?>
                                                    <?= $arProp["DISPLAY_VALUE"]; ?>
                                                <? endif; ?>
                                            </div>
                                        </div>
                                    <? endif; ?>
                                <? endif; ?>
                            <? endif; ?>
                        <? endforeach; ?>
                    </div>
                <? else: ?>
                    <table class="props_list">
                        <? foreach ($arResult["DISPLAY_PROPERTIES"] as $arProp): ?>
                            <? if (!in_array($arProp["CODE"], array("SERVICES", "BRAND", "HIT", "RECOMMEND", "NEW", "STOCK", "VIDEO", "VIDEO_YOUTUBE", "CML2_ARTICLE"))): ?>
                                <? if ((!is_array($arProp["DISPLAY_VALUE"]) && strlen($arProp["DISPLAY_VALUE"])) || (is_array($arProp["DISPLAY_VALUE"]) && implode('', $arProp["DISPLAY_VALUE"]))): ?>
                                    <tr itemprop="additionalProperty" itemscope
                                        itemtype="http://schema.org/PropertyValue">
                                        <td class="char_name">
                                            <span
                                                <? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"){ ?>class="whint"<? } ?>><? if ($arProp["HINT"] && $arParams["SHOW_HINTS"] == "Y"): ?>
                                                    <div class="hint"><span class="icon"><i>?</i></span><div
                                                            class="tooltip"><?= $arProp["HINT"] ?></div>
                                                    </div><? endif; ?><span
                                                        itemprop="name"><?= ($arProp["~NAME"] ? $arProp["~NAME"] : $arProp["NAME"]); ?></span></span>
                                        </td>
                                        <td class="char_value" itemprop="value">
											<span>
												<? if (count($arProp["DISPLAY_VALUE"]) > 1): ?>
                                                    <?= implode(', ', $arProp["DISPLAY_VALUE"]); ?>
                                                <? else: ?>
                                                    <?= $arProp["DISPLAY_VALUE"]; ?>
                                                <? endif; ?>
											</span>
                                        </td>
                                    </tr>
                                <? endif; ?>
                            <? endif; ?>
                        <? endforeach; ?>
                    </table>
                    <table class="props_list" id="<? echo $arItemIDs["ALL_ITEM_IDS"]['DISPLAY_PROP_DIV']; ?>"></table>
                <? endif; ?>
            </li>
        <? endif; ?>

        <li class="<?= (!($iTab++) ? ' current' : '') ?>">
            <div class="availability-name margin-bottom">
                <?php echo ($arResult['NAME']); ?>,
                <span class="availability-offer-value"></span>
            </div>

            <!-- How much products in the shop -->
            <?php
            //создаем класс со значениями остатка и наименования магазина
            class StoreProductSKU
            {
                public $skuid = "id";
                public $namestore = "namestore";
                public $amount = "amount";

                //формирование вывода остатка для всех магазинов для всех торговых предложений
                public function EchoAmount($id, $namestore, $amount)
                {
                    $this->skuid = $id;
                    $this->namestore = $namestore;
                    $this->amount = $amount;

                    //var_dump($id);
                    //var_dump($namestore);
                    //var_dump($amount);
                    ?>
                    <?php foreach ($this->skuid as $key => $value) { ?>
                    <div class="<?php echo $value ?>">
                        <?php
                        for ($r = 0; $r < count($this->namestore); $r++) { ?>
                            <?php $quantity = $this->amount[$key + $r] ?>

                                <p>
                                    <?php echo $this->namestore[$r] ?>
                                    - <?php echo $quantity; ?>
                                </p>

                        <?php } ?>
                    </div>
                    <?php
                }
                }
            }
            ?>

            <div class="quantity">
                <?php
                $select_fields = Array();

                //получаем id самого товара
                $pid = $arResult["ID"];

                //определяем есть ли у товара торговые предложения
                $sku = CCatalogSku::getExistOffers($pid);
                if ($sku[$pid]) {
                    //если есть, определяем id торговых предложений
                    $ressku = CCatalogSku::getOffersList($pid);
                    $resskuid = $ressku[$pid];
                    $skuid = array();
                    foreach ($resskuid as $key => $value) {
                        array_push($skuid, $value["ID"]);
                    };

                    //получаем массив с названиями складов-магазинов
                    $filter = Array("ACTIVE" => "Y");
                    $resStore = CCatalogStore::GetList(array(), $filter, false, false, $select_fields);
                    while ($sklad = $resStore->Fetch()) {
                        $stores[] = $sklad['TITLE'];
                    }

                    //получаем массив с остатком в каждом складе-магазине
                    $arFilter = Array("PRODUCT_ID" => $skuid);
                    $res = CCatalogStoreProduct::GetList(Array(), $arFilter, false, false, $select_fields);
                    while ($arRes = $res->Fetch()) {
                        $sum[] = $arRes['AMOUNT'];
                    }

                    //выводим магазины и остаток
                    $echo = new StoreProductSKU();
                    $echo->EchoAmount($skuid, $stores, $sum);

                } else {
                    echo GetMessage("QUANTITY_NO_OFFERS_MESSAGE");
                }
                ?>
            </div>

            <script>
                //скрываем и отображаем количество товаров в магазине в зависимости от торгового предложения
                function ShowHideAmount() {
                    //получаем id активного торгового предложения из url
                    var params = window.location.search.replace('?', '')
                        .split('&')
                        .reduce(
                            function (p, e) {
                                var a = e.split('=');
                                p[decodeURIComponent(a[0])] = decodeURIComponent(a[1]);
                                return p;
                            },
                            {}
                        );
                    var pid = params['oid'];

                    //определяем элементы с количеством товаров в зависимости от торгового предложения
                    var par = document.getElementsByClassName('quantity')[0];
                    var targ = par.getElementsByTagName('div');

                    //скрываем ненужные элементы
                    for (var r = 0; r < targ.length; r++) {
                        if (targ[r].className != pid) {
                            targ[r].style.display = "none";
                        } else {
                            targ[r].style.display = "inline-block";
                        }
                    }

                    //Получаем название активного торгового предложения
                    var offers = document.getElementsByClassName('bx_size')[0];
                    var activeOffer = offers.getElementsByClassName('active')[0];
                    var availabilityOffer = $('.availability-offer-value');
                    availabilityOffer.html(activeOffer.getAttribute('title'));
                }

                //отлавливаем клик по блоку с размерами и цветом
                function ClickSize() {
                    var targ = document.getElementsByClassName('wrapper_sku')[0];
                    targ.onclick = function () {
                        ShowHideAmount();
                    }
                }

                $(document).ready(function () {
                    ShowHideAmount();
                    ClickSize();
                });
            </script>

            <!-- /How much products in the shop -->

        </li>

        <? if ($arVideo): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <div class="video_block">
                    <? if (count($arVideo) > 1): ?>
                        <table class="video_table">
                            <tbody>
                            <? foreach ($arVideo as $v => $value): ?>
                                <? if (($v + 1) % 2): ?>
                                    <tr>
                                <? endif; ?>
                                <td width="50%"><?= str_replace('src=', 'width="561" height="314" src=', str_replace(array('width', 'height'), array('data-width', 'data-height'), $value)); ?></td>
                                <? if (!(($v + 1) % 2)): ?>
                                    </tr>
                                <? endif; ?>
                            <? endforeach; ?>
                            <? if (($v + 1) % 2): ?>
                                </tr>
                            <? endif; ?>
                            </tbody>
                        </table>
                    <? else: ?>
                        <?= $arVideo[0] ?>
                    <? endif; ?>
                </div>
            </li>
        <? endif; ?>

        <? if ($arParams["USE_REVIEW"] == "Y"): ?>
            <li class="<?= (!($iTab++) ? '' : '') ?>"></li>
        <? endif; ?>

        <? if (($arParams["SHOW_ASK_BLOCK"] == "Y") && (intVal($arParams["ASK_FORM_ID"]))): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <div class="wrap_md forms">
                    <div class="iblock text_block">
                        <? $APPLICATION->IncludeFile(SITE_DIR . "include/ask_tab_detail_description.php", array(), array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_ASK_DESCRIPTION'))); ?>
                    </div>
                    <div class="iblock form_block">
                        <div id="ask_block"></div>
                    </div>
                </div>
            </li>
        <? endif; ?>

        <? if ($useStores && ($showCustomOffer || !$arResult["OFFERS"])): ?>
            <li class="stores_tab<?= (!($iTab++) ? ' current' : '') ?>">
                <? if ($arResult["OFFERS"]) {
                    /*if($showCustomOffer){?>
						<?foreach($arResult["OFFERS"] as $arOffer){?>
							<div class="sku_stores_<?=$arOffer["ID"]?>" style="display: none;">
								<?$APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
										"PER_PAGE" => "10",
										"USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
										"SCHEDULE" => $arParams["SCHEDULE"],
										"USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
										"MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
										"ELEMENT_ID" => $arOffer["ID"],
										"STORE_PATH"  =>  $arParams["STORE_PATH"],
										"MAIN_TITLE"  =>  $arParams["MAIN_TITLE"],
										"MAX_AMOUNT"=>$arParams["MAX_AMOUNT"],
										"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
										"SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
										"SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
										"USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
										"USER_FIELDS" => $arParams['USER_FIELDS'],
										"FIELDS" => $arParams['FIELDS'],
										"STORES" => $arParams['STORES'],
									),
									$component
								);?>
							</div>
						<?}?>
					<?}*/ ?>
                <? } else { ?>
                    <? $APPLICATION->IncludeComponent("bitrix:catalog.store.amount", "main", array(
                        "PER_PAGE" => "10",
                        "USE_STORE_PHONE" => $arParams["USE_STORE_PHONE"],
                        "SCHEDULE" => $arParams["SCHEDULE"],
                        "USE_MIN_AMOUNT" => $arParams["USE_MIN_AMOUNT"],
                        "MIN_AMOUNT" => $arParams["MIN_AMOUNT"],
                        "ELEMENT_ID" => $arResult["ID"],
                        "STORE_PATH" => $arParams["STORE_PATH"],
                        "MAIN_TITLE" => $arParams["MAIN_TITLE"],
                        "MAX_AMOUNT" => $arParams["MAX_AMOUNT"],
                        "USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
                        "SHOW_EMPTY_STORE" => $arParams['SHOW_EMPTY_STORE'],
                        "SHOW_GENERAL_STORE_INFORMATION" => $arParams['SHOW_GENERAL_STORE_INFORMATION'],
                        "USE_ONLY_MAX_AMOUNT" => $arParams["USE_ONLY_MAX_AMOUNT"],
                        "USER_FIELDS" => $arParams['USER_FIELDS'],
                        "FIELDS" => $arParams['FIELDS'],
                        "STORES" => $arParams['STORES'],
                    ),
                        $component
                    ); ?>
                <? } ?>
            </li>
        <? endif; ?>

        <? if ($arParams["SHOW_ADDITIONAL_TAB"] == "Y"): ?>
            <li class="<?= (!($iTab++) ? ' current' : '') ?>">
                <? $APPLICATION->IncludeFile(SITE_DIR . "include/additional_products_description.php", array(), array("MODE" => "html", "NAME" => GetMessage('CT_BCE_CATALOG_ADDITIONAL_DESCRIPTION'))); ?>
            </li>
        <? endif; ?>
    </ul>
</div>
<? if (CMShop::checkVersionModule('sale', '16.5.0')): ?>
    <div class="gifts">
        <? if ($arResult['CATALOG'] && $arParams['USE_GIFTS_DETAIL'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale")) {
            $APPLICATION->IncludeComponent("bitrix:sale.gift.product", "main", array(
                'PRODUCT_ID_VARIABLE' => $arParams['PRODUCT_ID_VARIABLE'],
                'ACTION_VARIABLE' => $arParams['ACTION_VARIABLE'],
                'BUY_URL_TEMPLATE' => $arResult['~BUY_URL_TEMPLATE'],
                'ADD_URL_TEMPLATE' => $arResult['~ADD_URL_TEMPLATE'],
                'SUBSCRIBE_URL_TEMPLATE' => $arResult['~SUBSCRIBE_URL_TEMPLATE'],
                'COMPARE_URL_TEMPLATE' => $arResult['~COMPARE_URL_TEMPLATE'],
                "OFFER_HIDE_NAME_PROPS" => $arParams["OFFER_HIDE_NAME_PROPS"],

                "SHOW_DISCOUNT_PERCENT" => $arParams['GIFTS_SHOW_DISCOUNT_PERCENT'],
                "SHOW_OLD_PRICE" => $arParams['GIFTS_SHOW_OLD_PRICE'],
                "PAGE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                "LINE_ELEMENT_COUNT" => $arParams['GIFTS_DETAIL_PAGE_ELEMENT_COUNT'],
                "HIDE_BLOCK_TITLE" => $arParams['GIFTS_DETAIL_HIDE_BLOCK_TITLE'],
                "BLOCK_TITLE" => $arParams['GIFTS_DETAIL_BLOCK_TITLE'],
                "TEXT_LABEL_GIFT" => $arParams['GIFTS_DETAIL_TEXT_LABEL_GIFT'],
                "SHOW_NAME" => $arParams['GIFTS_SHOW_NAME'],
                "SHOW_IMAGE" => $arParams['GIFTS_SHOW_IMAGE'],
                "MESS_BTN_BUY" => $arParams['GIFTS_MESS_BTN_BUY'],

                "SHOW_PRODUCTS_{$arParams['IBLOCK_ID']}" => "Y",
                "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                "PRODUCT_SUBSCRIPTION" => $arParams["PRODUCT_SUBSCRIPTION"],
                "MESS_BTN_DETAIL" => $arParams["MESS_BTN_DETAIL"],
                "MESS_BTN_SUBSCRIBE" => $arParams["MESS_BTN_SUBSCRIBE"],
                "TEMPLATE_THEME" => $arParams["TEMPLATE_THEME"],
                "PRICE_CODE" => $arParams["PRICE_CODE"],
                "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],
                "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                "BASKET_URL" => $arParams["BASKET_URL"],
                "ADD_PROPERTIES_TO_BASKET" => $arParams["ADD_PROPERTIES_TO_BASKET"],
                "PRODUCT_PROPS_VARIABLE" => $arParams["PRODUCT_PROPS_VARIABLE"],
                "PARTIAL_PRODUCT_PROPERTIES" => $arParams["PARTIAL_PRODUCT_PROPERTIES"],
                "USE_PRODUCT_QUANTITY" => 'N',
                "OFFER_TREE_PROPS_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFER_TREE_PROPS'],
                "CART_PROPERTIES_{$arResult['OFFERS_IBLOCK']}" => $arParams['OFFERS_CART_PROPERTIES'],
                "PRODUCT_QUANTITY_VARIABLE" => $arParams["PRODUCT_QUANTITY_VARIABLE"],
                "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                "SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
                "SALE_STIKER" => $arParams["SALE_STIKER"],
                "SHOW_OLD_PRICE" => $arParams["SHOW_OLD_PRICE"],
                "SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
                "DISPLAY_TYPE" => "block",
                "SHOW_RATING" => $arParams["SHOW_RATING"],
                "DISPLAY_COMPARE" => $arParams["DISPLAY_COMPARE"],
                "DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
                "DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
                "TYPE_SKU" => "Y",

                "POTENTIAL_PRODUCT_TO_BUY" => array(
                    'ID' => isset($arResult['ID']) ? $arResult['ID'] : null,
                    'MODULE' => isset($arResult['MODULE']) ? $arResult['MODULE'] : 'catalog',
                    'PRODUCT_PROVIDER_CLASS' => isset($arResult['PRODUCT_PROVIDER_CLASS']) ? $arResult['PRODUCT_PROVIDER_CLASS'] : 'CCatalogProductProvider',
                    'QUANTITY' => isset($arResult['QUANTITY']) ? $arResult['QUANTITY'] : null,
                    'IBLOCK_ID' => isset($arResult['IBLOCK_ID']) ? $arResult['IBLOCK_ID'] : null,

                    'PRIMARY_OFFER_ID' => isset($arResult['OFFERS'][0]['ID']) ? $arResult['OFFERS'][0]['ID'] : null,
                    'SECTION' => array(
                        'ID' => isset($arResult['SECTION']['ID']) ? $arResult['SECTION']['ID'] : null,
                        'IBLOCK_ID' => isset($arResult['SECTION']['IBLOCK_ID']) ? $arResult['SECTION']['IBLOCK_ID'] : null,
                        'LEFT_MARGIN' => isset($arResult['SECTION']['LEFT_MARGIN']) ? $arResult['SECTION']['LEFT_MARGIN'] : null,
                        'RIGHT_MARGIN' => isset($arResult['SECTION']['RIGHT_MARGIN']) ? $arResult['SECTION']['RIGHT_MARGIN'] : null,
                    ),
                )
            ), $component, array("HIDE_ICONS" => "Y"));
        }
        if ($arResult['CATALOG'] && $arParams['USE_GIFTS_MAIN_PR_SECTION_LIST'] == 'Y' && \Bitrix\Main\ModuleManager::isModuleInstalled("sale")) {
            $APPLICATION->IncludeComponent(
                "bitrix:sale.gift.main.products",
                "main",
                array(
                    "PAGE_ELEMENT_COUNT" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_PAGE_ELEMENT_COUNT'],
                    "BLOCK_TITLE" => $arParams['GIFTS_MAIN_PRODUCT_DETAIL_BLOCK_TITLE'],

                    "OFFERS_FIELD_CODE" => $arParams["OFFERS_FIELD_CODE"],
                    "OFFERS_PROPERTY_CODE" => $arParams["OFFERS_PROPERTY_CODE"],

                    "AJAX_MODE" => $arParams["AJAX_MODE"],
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],

                    "ELEMENT_SORT_FIELD" => 'ID',
                    "ELEMENT_SORT_ORDER" => 'DESC',
                    //"ELEMENT_SORT_FIELD2" => $arParams["ELEMENT_SORT_FIELD2"],
                    //"ELEMENT_SORT_ORDER2" => $arParams["ELEMENT_SORT_ORDER2"],
                    "FILTER_NAME" => 'searchFilter',
                    "SECTION_URL" => $arParams["SECTION_URL"],
                    "DETAIL_URL" => $arParams["DETAIL_URL"],
                    "BASKET_URL" => $arParams["BASKET_URL"],
                    "ACTION_VARIABLE" => $arParams["ACTION_VARIABLE"],
                    "PRODUCT_ID_VARIABLE" => $arParams["PRODUCT_ID_VARIABLE"],
                    "SECTION_ID_VARIABLE" => $arParams["SECTION_ID_VARIABLE"],

                    "CACHE_TYPE" => $arParams["CACHE_TYPE"],
                    "CACHE_TIME" => $arParams["CACHE_TIME"],

                    "CACHE_GROUPS" => $arParams["CACHE_GROUPS"],
                    "SET_TITLE" => $arParams["SET_TITLE"],
                    "PROPERTY_CODE" => $arParams["PROPERTY_CODE"],
                    "PRICE_CODE" => $arParams["PRICE_CODE"],
                    "USE_PRICE_COUNT" => $arParams["USE_PRICE_COUNT"],
                    "SHOW_PRICE_COUNT" => $arParams["SHOW_PRICE_COUNT"],

                    "PRICE_VAT_INCLUDE" => $arParams["PRICE_VAT_INCLUDE"],
                    "CONVERT_CURRENCY" => $arParams["CONVERT_CURRENCY"],
                    "CURRENCY_ID" => $arParams["CURRENCY_ID"],
                    "HIDE_NOT_AVAILABLE" => $arParams["HIDE_NOT_AVAILABLE"],
                    "TEMPLATE_THEME" => (isset($arParams["TEMPLATE_THEME"]) ? $arParams["TEMPLATE_THEME"] : ""),

                    "ADD_PICT_PROP" => (isset($arParams["ADD_PICT_PROP"]) ? $arParams["ADD_PICT_PROP"] : ""),

                    "LABEL_PROP" => (isset($arParams["LABEL_PROP"]) ? $arParams["LABEL_PROP"] : ""),
                    "OFFER_ADD_PICT_PROP" => (isset($arParams["OFFER_ADD_PICT_PROP"]) ? $arParams["OFFER_ADD_PICT_PROP"] : ""),
                    "OFFER_TREE_PROPS" => (isset($arParams["OFFER_TREE_PROPS"]) ? $arParams["OFFER_TREE_PROPS"] : ""),
                    "SHOW_DISCOUNT_PERCENT" => (isset($arParams["SHOW_DISCOUNT_PERCENT"]) ? $arParams["SHOW_DISCOUNT_PERCENT"] : ""),
                    "SHOW_OLD_PRICE" => (isset($arParams["SHOW_OLD_PRICE"]) ? $arParams["SHOW_OLD_PRICE"] : ""),
                    "MESS_BTN_BUY" => (isset($arParams["MESS_BTN_BUY"]) ? $arParams["MESS_BTN_BUY"] : ""),
                    "MESS_BTN_ADD_TO_BASKET" => (isset($arParams["MESS_BTN_ADD_TO_BASKET"]) ? $arParams["MESS_BTN_ADD_TO_BASKET"] : ""),
                    "MESS_BTN_DETAIL" => (isset($arParams["MESS_BTN_DETAIL"]) ? $arParams["MESS_BTN_DETAIL"] : ""),
                    "MESS_NOT_AVAILABLE" => (isset($arParams["MESS_NOT_AVAILABLE"]) ? $arParams["MESS_NOT_AVAILABLE"] : ""),
                    'ADD_TO_BASKET_ACTION' => (isset($arParams["ADD_TO_BASKET_ACTION"]) ? $arParams["ADD_TO_BASKET_ACTION"] : ""),
                    'SHOW_CLOSE_POPUP' => (isset($arParams["SHOW_CLOSE_POPUP"]) ? $arParams["SHOW_CLOSE_POPUP"] : ""),
                    'DISPLAY_COMPARE' => (isset($arParams['DISPLAY_COMPARE']) ? $arParams['DISPLAY_COMPARE'] : ''),
                    'COMPARE_PATH' => (isset($arParams['COMPARE_PATH']) ? $arParams['COMPARE_PATH'] : ''),
                    "SHOW_DISCOUNT_TIME" => $arParams["SHOW_DISCOUNT_TIME"],
                    "SALE_STIKER" => $arParams["SALE_STIKER"],
                    "SHOW_MEASURE" => $arParams["SHOW_MEASURE"],
                    "DISPLAY_TYPE" => "block",
                    "SHOW_RATING" => $arParams["SHOW_RATING"],
                    "DISPLAY_WISH_BUTTONS" => $arParams["DISPLAY_WISH_BUTTONS"],
                    "DEFAULT_COUNT" => $arParams["DEFAULT_COUNT"],
                )
                + array(
                    'OFFER_ID' => empty($arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID']) ? $arResult['ID'] : $arResult['OFFERS'][$arResult['OFFERS_SELECTED']]['ID'],
                    'SECTION_ID' => $arResult['SECTION']['ID'],
                    'ELEMENT_ID' => $arResult['ID'],
                ),
                $component,
                array("HIDE_ICONS" => "Y")
            );
        }
        ?>
    </div>
<? endif; ?>

<script type="text/javascript">
    $(document).on('click', ".item-stock .store_view", function () {
        scroll_block($('.tabs_section'));
    });

    $(".opener").click(function () {
        $(this).find(".opener_icon").toggleClass("opened");
        var showBlock = $(this).parents("tr").toggleClass("nb").next(".offer_stores").find(".stores_block_wrap");
        showBlock.slideToggle(200);
    });

    $(".tabs_section .tabs-head li").live("click", function () {
        if (!$(this).is(".current")) {
            $(".tabs_section .tabs-head li").removeClass("current");
            $(this).addClass("current");
            $(".tabs_section ul.tabs_content li").removeClass("current");
            if ($(this).attr("id") == "product_reviews_tab") {
                $(".shadow.common").hide();
                $("#reviews_content").show();
            }
            else {
                $(".shadow.common").show();
                $("#reviews_content").hide();
                $(".tabs_section ul.tabs_content > li:eq(" + $(this).index() + ")").addClass("current");
            }
        }
    });

    $(".hint .icon").click(function (e) {
        var tooltipWrapp = $(this).parents(".hint");
        tooltipWrapp.click(function (e) {
            e.stopPropagation();
        })
        if (tooltipWrapp.is(".active")) {
            tooltipWrapp.removeClass("active").find(".tooltip").slideUp(200);
        }
        else {
            tooltipWrapp.addClass("active").find(".tooltip").slideDown(200);
            tooltipWrapp.find(".tooltip_close").click(function (e) {
                e.stopPropagation();
                tooltipWrapp.removeClass("active").find(".tooltip").slideUp(100);
            });
            $(document).click(function () {
                tooltipWrapp.removeClass("active").find(".tooltip").slideUp(100);
            });
        }
    });

    $('.set_block').ready(function () {
        $('.set_block ').equalize({children: '.item:not(".r") .cost', reset: true});
        $('.set_block').equalize({children: '.item .item-title', reset: true});
        $('.set_block').equalize({children: '.item .item_info', reset: false});
        //$('.set_wrapp').equalize({children: 'li', reset: true});
    });

    BX.message({
        QUANTITY_AVAILIABLE: '<? echo COption::GetOptionString("aspro.mshop", "EXPRESSION_FOR_EXISTS", GetMessage("EXPRESSION_FOR_EXISTS_DEFAULT"), SITE_ID); ?>',
        QUANTITY_NOT_AVAILIABLE: '<? echo COption::GetOptionString("aspro.mshop", "EXPRESSION_FOR_NOTEXISTS", GetMessage("EXPRESSION_FOR_NOTEXISTS"), SITE_ID); ?>',
        ADD_ERROR_BASKET: '<? echo GetMessage("ADD_ERROR_BASKET"); ?>',
        ADD_ERROR_COMPARE: '<? echo GetMessage("ADD_ERROR_COMPARE"); ?>',
        ONE_CLICK_BUY: '<? echo GetMessage("ONE_CLICK_BUY"); ?>',
        SITE_ID: '<? echo SITE_ID; ?>'
    })
</script>