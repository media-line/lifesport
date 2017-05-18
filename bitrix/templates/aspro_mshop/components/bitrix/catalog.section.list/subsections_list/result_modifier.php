<?
	$arSections = array();
	foreach( $arResult["SECTIONS"] as $arItem ) {
		if(!$arSections[$arItem["IBLOCK_SECTION_ID"]])
		{
			$arSections[$arItem["ID"]] = $arItem;
		}
		else
		{
			$arSections[$arItem["IBLOCK_SECTION_ID"]]["SECTIONS"][$arItem["ID"]] = $arItem;
		}
	}

	if($arSections)
		$arResult["SECTIONS"] = $arSections;
?>