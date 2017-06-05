<?
	if(!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();
	global $APPLICATION;
    $aMenuLinksExt = array();

    //id инфоболока для меню
    $mainMenuId = 21;
    //максимальная вложенность
    $maxDepth = 3;

    if(CModule::IncludeModule("iblock")){
        $arOrder = Array("LEFT_MARGIN"=>"ASC");

        $arSelect = Array("ID", "NAME", "CODE", "DETAIL_PICTURE", "IBLOCK_ID", "IBLOCK_CODE","DEPTH_LEVEL", "IBLOCK_SECTION_ID", "SECTION_PAGE_URL", "UF_BANNER_LINK");

        $arFilter = Array("IBLOCK_ID"=>$mainMenuId, "ACTIVE"=>"Y", "<=DEPTH_LEVEL"=>$maxDepth);

        $res = CIBlockSection::GetList($arOrder, $arFilter, false, $arSelect);

        while($ob = $res->GetNext()) {
            
            $link = $ob['SECTION_PAGE_URL'] . '/';
            $aMenuLinksExt[] = Array(
                $ob['NAME'], 
                $link, 
                array(),
                array(
                    'FROM_IBLOCK' => 1,
                    'IS_PARENT' => $ob['DEPTH_LEVEL'] == $maxDepth ? '' : 1,
                    'DEPTH_LEVEL' => $ob['DEPTH_LEVEL'],
                    'PICTURE' => $ob['DETAIL_PICTURE'],
                    'BANNER_LINK' => $ob['UF_BANNER_LINK']
                ),
                ''
            );
        }
     
    }
	$aMenuLinks = array_merge($aMenuLinks, $aMenuLinksExt);
    
?>