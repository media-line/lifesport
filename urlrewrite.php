<?
$arUrlRewrite = array(
	array(
		"CONDITION" => "#^/bitrix/services/ymarket/([\\w\\d\\-]+)?(/)?(([\\w\\d\\-]+)(/)?)?#",
		"RULE" => "REQUEST_OBJECT=\$1&METHOD=\$4",
		"ID" => "",
		"PATH" => "/bitrix/services/ymarket/index.php",
	),
	array(
		"CONDITION" => "#^/information/links/([a-zA-Z0-9_]+)/\\?{0,1}(.*)\$#",
		"RULE" => "/information/links/index.php?SECTION_CODE=\\1&\\2",
		"ID" => "",
		"PATH" => "",
	),
	array(
		"CONDITION" => "#^/online/([\\.\\-0-9a-zA-Z]+)(/?)([^/]*)#",
		"RULE" => "alias=\$1",
		"ID" => "bitrix:im.router",
		"PATH" => "/desktop_app/router.php",
	),
	array(
		"CONDITION" => "#^/board/([a-zA-Z0-9_]+)/\\?{0,1}(.*)\$#",
		"RULE" => "/board/index.php?SECTION_CODE=\\1&\\2",
		"ID" => "",
		"PATH" => "",
	),
	array(
		"CONDITION" => "#^/personal/history-of-orders/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => "/personal/history-of-orders/index.php",
	),
	array(
		"CONDITION" => "#^/bitrix/services/ymarket/#",
		"RULE" => "",
		"ID" => "",
		"PATH" => "/bitrix/services/ymarket/index.php",
	),
	array(
		"CONDITION" => "#^/online/(/?)([^/]*)#",
		"RULE" => "",
		"ID" => "bitrix:im.router",
		"PATH" => "/desktop_app/router.php",
	),
	array(
		"CONDITION" => "#^/stssync/calendar/#",
		"RULE" => "",
		"ID" => "bitrix:stssync.server",
		"PATH" => "/bitrix/services/stssync/calendar/index.php",
	),
	array(
		"CONDITION" => "#^/contacts/stores/#",
		"RULE" => "",
		"ID" => "bitrix:catalog.store",
		"PATH" => "/contacts/stores/index.php",
	),
	array(
		"CONDITION" => "#^/personal/order/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.order",
		"PATH" => "/personal/order/index.php",
	),
	array(
		"CONDITION" => "#^/info/articles/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/info/articles/index.php",
	),
	array(
		"CONDITION" => "#^/info/article/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/info/article/index.php",
	),
	array(
		"CONDITION" => "#^/company/news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/company/news/index.php",
	),
	array(
		"CONDITION" => "#^/nationalnews/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/nationalnews/index.php",
	),
	array(
		"CONDITION" => "#^/info/brands/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/info/brands/index.php",
	),
	array(
		"CONDITION" => "#^/job/vacancy/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/job/vacancy/index.php",
	),
	array(
		"CONDITION" => "#^/job/resume/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/job/resume/index.php",
	),
	array(
		"CONDITION" => "#^/info/brand/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/info/brand/index.php",
	),
	array(
		"CONDITION" => "#^/personal/#",
		"RULE" => "",
		"ID" => "bitrix:sale.personal.section",
		"PATH" => "/personal/index.php",
	),
	array(
		"CONDITION" => "#^/products/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/products/index.php",
	),
	array(
		"CONDITION" => "#^/services/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/services/index.php",
	),
	array(
		"CONDITION" => "#^/catalog/#",
		"RULE" => "",
		"ID" => "bitrix:catalog",
		"PATH" => "/catalog/index.php",
	),
	array(
		"CONDITION" => "#^/themes/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/themes/index.php",
	),
	array(
		"CONDITION" => "#^/forum/#",
		"RULE" => "",
		"ID" => "bitrix:forum",
		"PATH" => "/forum/index.php",
	),
	array(
		"CONDITION" => "#^/photo/#",
		"RULE" => "",
		"ID" => "bitrix:photogallery_user",
		"PATH" => "/photo/index.php",
	),
	array(
		"CONDITION" => "#^/blogs/#",
		"RULE" => "",
		"ID" => "bitrix:blog",
		"PATH" => "/blogs/index.php",
	),
	array(
		"CONDITION" => "#^/news/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/news/index.php",
	),
	array(
		"CONDITION" => "#^/sale/#",
		"RULE" => "",
		"ID" => "bitrix:news",
		"PATH" => "/sale/index.php",
	),
);

?>