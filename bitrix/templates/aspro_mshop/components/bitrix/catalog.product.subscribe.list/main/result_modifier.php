<?
use Bitrix\Main\Loader;
use Bitrix\Main\Type\Collection;
use Bitrix\Currency\CurrencyTable;

if (!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();
$arParams['IS_SUBSCRIBE_LIST'] = 'Y';
$arParams['TYPE_SKU'] = 'TYPE_1';
/** @var CBitrixComponentTemplate $this */
/** @var array $arParams */
/** @var array $arResult */
/** @global CDatabase $DB */
if (!isset($arParams['LINE_ELEMENT_COUNT']))
	$arParams['LINE_ELEMENT_COUNT'] = 3;
$arParams['LINE_ELEMENT_COUNT'] = intval($arParams['LINE_ELEMENT_COUNT']);
if (2 > $arParams['LINE_ELEMENT_COUNT'] || 5 < $arParams['LINE_ELEMENT_COUNT'])
	$arParams['LINE_ELEMENT_COUNT'] = 3;

if (!empty($arResult['ITEMS']))
{
	$arEmptyPreview = false;
	$strEmptyPreview = $this->GetFolder() . '/images/no_photo.png';
	if (file_exists($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview))
	{
		$arSizes = getimagesize($_SERVER['DOCUMENT_ROOT'] . $strEmptyPreview);
		if (!empty($arSizes))
		{
			$arEmptyPreview = array(
				'SRC' => $strEmptyPreview,
				'WIDTH' => intval($arSizes[0]),
				'HEIGHT' => intval($arSizes[1])
			);
		}
		unset($arSizes);
	}
	unset($strEmptyPrev);

	$arSKUPropList = array();
	$arSKUPropIDs = array();
	$arSKUPropKeys = array();
	$catalogs = array();
	$boolSKU = false;
	$strBaseCurrency = '';
	$boolConvert = isset($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
	$arOfferProps = implode(';', $arParams['OFFERS_CART_PROPERTIES']);

	if (!$boolConvert)
		$strBaseCurrency = CCurrency::GetBaseCurrency();

	$arNewItemsList = array();
	foreach ($arResult['ITEMS'] as $key => $arItem)
	{
		$itemId = $arItem['ID'];

		foreach($arResult['CATALOGS'] as $catalog)
		{
			$offersCatalogId = (int)$catalog['OFFERS_IBLOCK_ID'];
			$offersPropId = (int)$catalog['OFFERS_PROPERTY_ID'];
			$catalogId = (int)$catalog['IBLOCK_ID'];
			$sku = false;
			if($offersCatalogId > 0 && $offersPropId > 0)
			{
				$sku = array(
					'IBLOCK_ID' => $offersCatalogId,
					'SKU_PROPERTY_ID' => $offersPropId,
					'PRODUCT_IBLOCK_ID' => $catalogId
				);
			}
			if(!empty($sku) && is_array($sku))
			{
				$arSKUPropList[$itemId] = CIBlockPriceTools::getTreeProperties(
					$sku,
					$arParams['OFFER_TREE_PROPS'][$itemId],
					array('PICT' => $arEmptyPreview, 'NAME' => '-')
				);
				$needValues = array();
				CIBlockPriceTools::getTreePropertyValues($arSKUPropList[$itemId], $needValues);

				$arSKUPropIDs[$itemId] = array_keys($arSKUPropList[$itemId]);
				if (!empty($arSKUPropIDs[$arItem['ID']]))
					$arSKUPropKeys[$itemId] = array_fill_keys($arSKUPropIDs[$itemId], true);

				foreach($arSKUPropList[$itemId] as $propertyCode => &$propertyValue)
				{
					foreach($propertyValue['VALUES'] as $keyProperty => $value)
					{
						if($propertyValue['SHOW_MODE'] == 'PICT')
							$desiredValue = $value['XML_ID'];
						else
							$desiredValue = $value['NAME'];
						if(!in_array($desiredValue, $arParams['PROPERTY_VALUE'][$itemId][$propertyCode]))
							unset($propertyValue['VALUES'][$keyProperty]);
					}
				}
			}
		}

		$arItem['CATALOG_QUANTITY'] = (
		0 < $arItem['CATALOG_QUANTITY'] && is_float($arItem['CATALOG_MEASURE_RATIO'])
			? floatval($arItem['CATALOG_QUANTITY'])
			: intval($arItem['CATALOG_QUANTITY'])
		);
		$arItem['CATALOG'] = false;
		$arItem['LABEL'] = false;
		if (!isset($arItem['CATALOG_SUBSCRIPTION']) || 'Y' != $arItem['CATALOG_SUBSCRIPTION'])
			$arItem['CATALOG_SUBSCRIPTION'] = 'N';

		// Item Label Properties
		$itemIblockId = $arItem['IBLOCK_ID'];
		$propertyName = isset($arParams['LABEL_PROP'][$itemIblockId]) ? $arParams['LABEL_PROP'][$itemIblockId] : false;

		if ($propertyName && isset($arItem['PROPERTIES'][$propertyName]))
		{
			$property = $arItem['PROPERTIES'][$propertyName];

			if (!empty($property['VALUE']))
			{
				if ('N' == $property['MULTIPLE'] && 'L' == $property['PROPERTY_TYPE'] && 'C' == $property['LIST_TYPE'])
				{
					$arItem['LABEL_VALUE'] = $property['NAME'];
				}
				else
				{
					$arItem['LABEL_VALUE'] = (is_array($property['VALUE'])
						? implode(' / ', $property['VALUE'])
						: $property['VALUE']
					);
				}
				$arItem['LABEL'] = true;

				if (isset($arItem['DISPLAY_PROPERTIES'][$propertyName]))
					unset($arItem['DISPLAY_PROPERTIES'][$propertyName]);
			}
			unset($property);
		}
		// !Item Label Properties

		// item double images
		$productPictures = array(
			"PICT" => false,
			"SECOND_PICT" => false
		);

		if (isset($arParams['ADDITIONAL_PICT_PROP'][$itemIblockId]))
		{
			$productPictures = CIBlockPriceTools::getDoublePicturesForItem($arItem, $arParams['ADDITIONAL_PICT_PROP'][$itemIblockId]);
		}
		else
		{
			$productPictures = CIBlockPriceTools::getDoublePicturesForItem($arItem, false);
		}
		if (empty($productPictures['PICT']))
			$productPictures['PICT'] = $arEmptyPreview;
		if (empty($productPictures['SECOND_PICT']))
			$productPictures['SECOND_PICT'] = $productPictures['PICT'];
		$arItem['PREVIEW_PICTURE'] = $productPictures['PICT'];
		$arItem['PREVIEW_PICTURE_SECOND'] = $productPictures['SECOND_PICT'];
		$arItem['SECOND_PICT'] = true;
		$arItem['PRODUCT_PREVIEW'] = $productPictures['PICT'];
		$arItem['PRODUCT_PREVIEW_SECOND'] = $productPictures['SECOND_PICT'];
		// !item double images

		$arItem['CATALOG'] = true;
		if (!isset($arItem['CATALOG_TYPE']))
			$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_PRODUCT;
		if (
			(CCatalogProduct::TYPE_PRODUCT == $arItem['CATALOG_TYPE'] || CCatalogProduct::TYPE_SKU == $arItem['CATALOG_TYPE'])
			&& !empty($arItem['OFFERS'])
		)
		{
			$arItem['CATALOG_TYPE'] = CCatalogProduct::TYPE_SKU;
		}
		switch ($arItem['CATALOG_TYPE'])
		{
			case CCatalogProduct::TYPE_SET:
				$arItem['OFFERS'] = array();
				$arItem['CATALOG_MEASURE_RATIO'] = 1;
				$arItem['CATALOG_QUANTITY'] = 0;
				$arItem['CHECK_QUANTITY'] = false;
				break;
			case CCatalogProduct::TYPE_SKU:
				break;
			case CCatalogProduct::TYPE_PRODUCT:
			default:
				$arItem['CHECK_QUANTITY'] = ('Y' == $arItem['CATALOG_QUANTITY_TRACE'] && 'N' == $arItem['CATALOG_CAN_BUY_ZERO']);
				break;
		}

		// Offers
		if ($arItem['CATALOG'] && isset($arItem['OFFERS']) && !empty($arItem['OFFERS']))
		{
			$arItem['MIN_PRICE'] = $arItem['MIN_BASIS_PRICE'] = false;
			$minItemPriceID = $minItemPrice = 0;
			$minItemPriceFormat = '';

			$arItem['MIN_PRICE'] = CMShop::getMinPriceFromOffersExt(
				$arItem['OFFERS'],
				$boolConvert ? $arResult['CONVERT_CURRENCY']['CURRENCY_ID'] : $strBaseCurrency
			);

			foreach ($arItem['OFFERS'] as $keyOffer => $arOffer){
				if($arOffer["MIN_PRICE"]["CAN_ACCESS"]){
					if($arOffer["MIN_PRICE"]["DISCOUNT_VALUE"] < $arOffer["MIN_PRICE"]["VALUE"]){
						$minOfferPrice = $arOffer["MIN_PRICE"]["DISCOUNT_VALUE"];
						$minOfferPriceFormat = $arOffer["MIN_PRICE"]["PRINT_DISCOUNT_VALUE"];
						$minOfferPriceID = $arOffer["MIN_PRICE"]["PRICE_ID"];
					}
					else{
						$minOfferPrice = $arOffer["MIN_PRICE"]["VALUE"];
						$minOfferPriceFormat = $arOffer["MIN_PRICE"]["PRINT_VALUE"];
						$minOfferPriceID = $arOffer["MIN_PRICE"]["PRICE_ID"];
					}

					if($minItemPrice > 0 && $minOfferPrice < $minItemPrice){
						$minItemPrice = $minOfferPrice;
						$minItemPriceFormat = $minOfferPriceFormat;
						$minItemPriceID = $minOfferPriceID;
						$minItemID = $arOffer["ID"];
					}
					elseif($minItemPrice == 0){
						$minItemPrice = $minOfferPrice;
						$minItemPriceFormat = $minOfferPriceFormat;
						$minItemPriceID = $minOfferPriceID;
						$minItemID = $arOffer["ID"];
					}
				}
			}

			$arItem['MIN_PRICE']["MIN_PRICE_ID"] = $minItemPriceID;
			$arItem['MIN_PRICE']["MIN_ITEM_ID"] = $minItemID;

			$arMatrixFields = $arSKUPropKeys[$itemId];
			$arMatrix = array();

			$arNewOffers = array();
			$boolSKUDisplayProperties = false;
			$arItem['OFFERS_PROP'] = false;

			foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
			{
				if(!array_key_exists($arOffer['ID'], $arParams['LIST_SUBSCRIPTIONS']))
					continue;

				$arRow = array();
				foreach ($arSKUPropIDs[$itemId] as $propkey => $strOneCode)
				{
					$arCell = array(
						'VALUE' => 0,
						'SORT' => PHP_INT_MAX,
						'NA' => true
					);
					$arCell['NA'] = false;

					if (isset($arOffer['DISPLAY_PROPERTIES'][$strOneCode]))
					{
						if('directory' == $arSKUPropList[$itemId][$strOneCode]['USER_TYPE'])
						{
							$intValue = $arSKUPropList[$itemId][$strOneCode]['XML_MAP'][$arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']];
							$arCell['VALUE'] = $intValue;
						}
						elseif('L' == $arSKUPropList[$itemId][$strOneCode]['PROPERTY_TYPE'])
						{
							$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE_ENUM_ID']);
						}
						elseif('E' == $arSKUPropList[$itemId][$strOneCode]['PROPERTY_TYPE'])
						{
							$arCell['VALUE'] = intval($arOffer['DISPLAY_PROPERTIES'][$strOneCode]['VALUE']);
						}
						$arCell['SORT'] = $arSKUPropList[$itemId][$strOneCode]['VALUES'][$arCell['VALUE']]['SORT'];
					}

					$arRow[$strOneCode] = $arCell;
				}
				$arMatrix[$keyOffer] = $arRow;

				$newOfferProps = array();
				if(!empty($arParams['PROPERTY_CODE'][$arOffer['IBLOCK_ID']]))
				{
					foreach($arParams['PROPERTY_CODE'][$arOffer['IBLOCK_ID']] as $propName)
						$newOfferProps[$propName] = $arOffer['DISPLAY_PROPERTIES'][$propName];
				}
				$arOffer['DISPLAY_PROPERTIES'] = $newOfferProps;

				$arOffer['CHECK_QUANTITY'] = ('Y' == $arOffer['CATALOG_QUANTITY_TRACE'] && 'N' == $arOffer['CATALOG_CAN_BUY_ZERO']);
				if (!isset($arOffer['CATALOG_MEASURE_RATIO']))
					$arOffer['CATALOG_MEASURE_RATIO'] = 1;
				if (!isset($arOffer['CATALOG_QUANTITY']))
					$arOffer['CATALOG_QUANTITY'] = 0;
				$arOffer['CATALOG_QUANTITY'] = (
				0 < $arOffer['CATALOG_QUANTITY'] && is_float($arOffer['CATALOG_MEASURE_RATIO'])
					? floatval($arOffer['CATALOG_QUANTITY'])
					: intval($arOffer['CATALOG_QUANTITY'])
				);
				$arOffer['CATALOG_TYPE'] = CCatalogProduct::TYPE_OFFER;
				CIBlockPriceTools::setRatioMinPrice($arOffer);

				$offerPictures = CIBlockPriceTools::getDoublePicturesForItem($arOffer, $arParams['ADDITIONAL_PICT_PROP'][$arOffer['IBLOCK_ID']]);
				$arOffer['OWNER_PICT'] = empty($offerPictures['PICT']);
				$arOffer['PREVIEW_PICTURE'] = false;
				$arOffer['PREVIEW_PICTURE_SECOND'] = false;
				$arOffer['SECOND_PICT'] = true;
				if (!$arOffer['OWNER_PICT'])
				{
					if (empty($offerPictures['SECOND_PICT']))
						$offerPictures['SECOND_PICT'] = $offerPictures['PICT'];
					$arOffer['PREVIEW_PICTURE'] = $offerPictures['PICT'];
					$arOffer['PREVIEW_PICTURE_SECOND'] = $offerPictures['SECOND_PICT'];
				}
				if ('' != $arParams['OFFER_ADD_PICT_PROP'] && isset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]))
					unset($arOffer['DISPLAY_PROPERTIES'][$arParams['OFFER_ADD_PICT_PROP']]);
				$arNewOffers[$keyOffer] = $arOffer;
			}
			$arItem['OFFERS'] = $arNewOffers;

			$arUsedFields = array();
			$arSortFields = array();

			$arPropSKU = $arItem['OFFERS_PROPS_JS'] = array();

			$matrixKeys = array_keys($arMatrix);
			foreach ($arSKUPropIDs[$itemId] as $propkey => $propCode)
			{
				foreach ($matrixKeys as $keyOffer)
				{
					if (!isset($arItem['OFFERS'][$keyOffer]['TREE']))
						$arItem['OFFERS'][$keyOffer]['TREE'] = array();
					$propId = $arSKUPropList[$itemId][$propCode]['ID'];
					$value = $arMatrix[$keyOffer][$propCode]['VALUE'];
					if (!isset($arItem['SKU_TREE_VALUES'][$propId]))
						$arItem['SKU_TREE_VALUES'][$propId] = array();
					$arItem['SKU_TREE_VALUES'][$propId][$value] = true;
					$arItem['OFFERS'][$keyOffer]['TREE']['PROP_'.$propId] = $value;
					$arItem['OFFERS'][$keyOffer]['SKU_SORT_'.$propCode] = $arMatrix[$keyOffer][$propCode]['SORT'];
					$arUsedFields[$propCode] = true;
					$arSortFields['SKU_SORT_'.$propCode] = SORT_NUMERIC;

					$arPropSKU[$propCode][$arMatrix[$keyOffer][$propCode]["VALUE"]] = $arSKUPropList[$itemId][$propCode]["VALUES"][$arMatrix[$keyOffer][$propCode]["VALUE"]];
					unset($value, $propId);
				}
				unset($keyOffer);

				if($arPropSKU[$propCode])
				{
					Collection::sortByColumn($arPropSKU[$propCode], array("SORT" => SORT_NUMERIC)); // sort sku prop values
					$arItem['OFFERS_PROPS_JS'][$propCode] = array(
						"ID" => $arSKUPropList[$itemId][$propCode]["ID"],
						"CODE" => $arSKUPropList[$itemId][$propCode]["CODE"],
						"NAME" => $arSKUPropList[$itemId][$propCode]["NAME"],
						"SORT" => $arSKUPropList[$itemId][$propCode]["SORT"],
						"PROPERTY_TYPE" => $arSKUPropList[$itemId][$propCode]["PROPERTY_TYPE"],
						"USER_TYPE" => $arSKUPropList[$itemId][$propCode]["USER_TYPE"],
						"LINK_IBLOCK_ID" => $arSKUPropList[$itemId][$propCode]["LINK_IBLOCK_ID"],
						"SHOW_MODE" => $arSKUPropList[$itemId][$propCode]["SHOW_MODE"],
						"VALUES" => $arPropSKU[$propCode]
					);
				}
			}
			unset($propkey, $propCode);
			unset($matrixKeys);
			$arItem['OFFERS_PROP'] = $arUsedFields;

			\Bitrix\Main\Type\Collection::sortByColumn($arItem['OFFERS'], $arSortFields);

			// Find Selected offer
			foreach($arItem['OFFERS']  as $ind => $offer)
				if($offer['SELECTED'])
				{
					$arItem['OFFERS_SELECTED'] = $ind;
					break;
				}

			$arMatrix = array();
			$intSelected = -1;
			foreach ($arItem['OFFERS'] as $keyOffer => $arOffer)
			{
				$arSKUProps = false;
				if (!empty($arOffer['DISPLAY_PROPERTIES']))
				{
					$boolSKUDisplayProperties = true;
					$arSKUProps = array();
					foreach ($arOffer['DISPLAY_PROPERTIES'] as &$arOneProp)
					{
						if ('F' == $arOneProp['PROPERTY_TYPE'])
							continue;
						$arSKUProps[] = array(
							'NAME' => $arOneProp['NAME'],
							'VALUE' => $arOneProp['DISPLAY_VALUE']
						);
					}
					unset($arOneProp);
				}

				$totalCount = CMShop::GetTotalCount($arOffer);
				$arOffer['IS_OFFER'] = 'Y';
				$arOffer['IBLOCK_ID'] = $arResult['IBLOCK_ID'];
				$arAddToBasketData = CMShop::GetAddToBasketArray($arOffer, $totalCount, $arParams["DEFAULT_COUNT"], $arParams["BASKET_URL"], false, $arItemIDs["ALL_ITEM_IDS"], 'small read_more1', $arParams);
				$arAddToBasketData["HTML"] = str_replace('data-item', 'data-props="'.$arOfferProps.'" data-item', $arAddToBasketData["HTML"]);

				$arOneRow = array(
					'ID' => $arOffer['ID'],
					'NAME' => $arOffer['~NAME'],
					'TREE' => $arOffer['TREE'],
					'DISPLAY_PROPERTIES' => $arSKUProps,
					'PRICE' => (isset($arOffer['RATIO_PRICE']) ? $arOffer['RATIO_PRICE'] : $arOffer['MIN_PRICE']),
					'SECOND_PICT' => $arOffer['SECOND_PICT'],
					'OWNER_PICT' => $arOffer['OWNER_PICT'],
					'PREVIEW_PICTURE' => $arOffer['PREVIEW_PICTURE'],
					'PREVIEW_PICTURE_SECOND' => $arOffer['PREVIEW_PICTURE_SECOND'],
					'CHECK_QUANTITY' => $arOffer['CHECK_QUANTITY'],
					'MAX_QUANTITY' => $arOffer['CATALOG_QUANTITY'],
					'STEP_QUANTITY' => $arOffer['CATALOG_MEASURE_RATIO'],
					'QUANTITY_FLOAT' => is_double($arOffer['CATALOG_MEASURE_RATIO']),
					'MEASURE' => $arOffer['~CATALOG_MEASURE_NAME'],
					'CAN_BUY' => $arOffer['CAN_BUY'],
					'CATALOG_SUBSCRIBE' => $arOffer['CATALOG_SUBSCRIBE'],
					'AVAILIABLE' => CMShop::GetQuantityArray($arOffer['CATALOG_QUANTITY']),
					'URL' => $arItem['DETAIL_PAGE_URL'],
					'SHOW_MEASURE' => ($arParams["SHOW_MEASURE"]=="Y" ? "Y" : "N"),
					'SHOW_ONE_CLICK_BUY' => "N",
					'ONE_CLICK_BUY' => GetMessage("ONE_CLICK_BUY"),
					'OFFER_PROPS' => $arOfferProps,
					'NO_PHOTO' => $arEmptyPreview,
					'CONFIG' => $arAddToBasketData,
					'HTML' => $arAddToBasketData["HTML"],
					'PRODUCT_QUANTITY_VARIABLE' => $arParams["PRODUCT_QUANTITY_VARIABLE"],
					'BUY_URL' => $arOffer['~BUY_URL'],
					'ADD_URL' => $arOffer['~ADD_URL'],
				);
				$arMatrix[$keyOffer] = $arOneRow;
			}

			if (-1 == $intSelected)
				$intSelected = 0;
			if (!$arMatrix[$intSelected]['OWNER_PICT'] && !empty($arItem['OFFERS']))
			{
				$arItem['PREVIEW_PICTURE'] = $arMatrix[$intSelected]['PREVIEW_PICTURE'];
				$arItem['PREVIEW_PICTURE_SECOND'] = $arMatrix[$intSelected]['PREVIEW_PICTURE_SECOND'];
			}
			$arItem['JS_OFFERS'] = $arMatrix;
			$arItem['OFFERS_SELECTED'] = $intSelected;
			$arItem['OFFERS_PROPS_DISPLAY'] = $boolSKUDisplayProperties;
		}

		if (!empty($arItem['DISPLAY_PROPERTIES']))
		{
			foreach ($arItem['DISPLAY_PROPERTIES'] as $propKey => $arDispProp)
			{
				if ('F' == $arDispProp['PROPERTY_TYPE'])
					unset($arItem['DISPLAY_PROPERTIES'][$propKey]);
			}
		}
		$arItem['LAST_ELEMENT'] = 'N';
		$arNewItemsList[$key] = $arItem;
	}

	$arNewItemsList[$key]['LAST_ELEMENT'] = 'Y';
	$arResult['ITEMS'] = $arNewItemsList;
	$arResult['SKU_PROPS'] = $arSKUPropList;
	$arResult['DEFAULT_PICTURE'] = $arEmptyPreview;

	$arResult['CURRENCIES'] = array();
	if (Loader::includeModule('currency'))
	{
		if ($boolConvert)
		{
			$currencyFormat = CCurrencyLang::GetFormatDescription($arResult['CONVERT_CURRENCY']['CURRENCY_ID']);
			$arResult['CURRENCIES'] = array(
				array(
					'CURRENCY' => $arResult['CONVERT_CURRENCY']['CURRENCY_ID'],
					'FORMAT' => array(
						'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
						'DEC_POINT' => $currencyFormat['DEC_POINT'],
						'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
						'DECIMALS' => $currencyFormat['DECIMALS'],
						'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
						'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
					)
				)
			);
			unset($currencyFormat);
		}
		else
		{
			$currencyIterator = CurrencyTable::getList(array(
				'select' => array('CURRENCY')
			));
			while ($currency = $currencyIterator->fetch())
			{
				$currencyFormat = CCurrencyLang::GetFormatDescription($currency['CURRENCY']);
				$arResult['CURRENCIES'][] = array(
					'CURRENCY' => $currency['CURRENCY'],
					'FORMAT' => array(
						'FORMAT_STRING' => $currencyFormat['FORMAT_STRING'],
						'DEC_POINT' => $currencyFormat['DEC_POINT'],
						'THOUSANDS_SEP' => $currencyFormat['THOUSANDS_SEP'],
						'DECIMALS' => $currencyFormat['DECIMALS'],
						'THOUSANDS_VARIANT' => $currencyFormat['THOUSANDS_VARIANT'],
						'HIDE_ZERO' => $currencyFormat['HIDE_ZERO']
					)
				);
			}
			unset($currencyFormat, $currency, $currencyIterator);
		}
	}

	$this->__component->arResultCacheKeys = array_merge($this->__component->arResultCacheKeys, array('CURRENCIES', 'ITEMS'));
}
?>