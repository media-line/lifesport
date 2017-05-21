<?
//<title>CSV (lifesport)</title>
use Bitrix\Main;
IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/catalog/import_setup_templ.php');
/** @global string $ACTION */
/** @global string $URL_DATA_FILE */
/** @global string $DATA_FILE_NAME */
/** @global int $IBLOCK_ID */
/** @global string $fields_type */
/** @global string $first_names_r */
/** @global string $delimiter_r */
/** @global string $delimiter_other_r */
/** @global string $first_names_f */
/** @global string $metki_f */

global $APPLICATION, $USER;

$NUM_CATALOG_LEVELS = (int)Main\Config\Option::get('catalog', 'num_catalog_levels');
if ($NUM_CATALOG_LEVELS <= 0)
	$NUM_CATALOG_LEVELS = 3;

$arSetupErrors = array();

global
	$arCatalogAvailProdFields,
	$defCatalogAvailProdFields,
	$arCatalogAvailPriceFields,
	$defCatalogAvailPriceFields,
	$arCatalogAvailValueFields,
	$defCatalogAvailValueFields,
	$arCatalogAvailQuantityFields,
	$defCatalogAvailQuantityFields,
	$arCatalogAvailGroupFields,
	$defCatalogAvailGroupFields,
	$defCatalogAvailCurrencies;

//********************  ACTIONS  **************************************//
if (($ACTION == 'IMPORT_EDIT' || $ACTION == 'IMPORT_COPY') && $STEP == 1)
{
	if (isset($arOldSetupVars['IBLOCK_ID']))
		$IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
	if (isset($arOldSetupVars['URL_DATA_FILE']))
		$URL_DATA_FILE = $arOldSetupVars['URL_DATA_FILE'];
	if (isset($arOldSetupVars['DATA_FILE_NAME']))
		$DATA_FILE_NAME = $arOldSetupVars['DATA_FILE_NAME'];
}

if ($STEP > 1)
{
	if (strlen($URL_DATA_FILE) > 0 && file_exists($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && is_file($_SERVER["DOCUMENT_ROOT"].$URL_DATA_FILE) && $APPLICATION->GetFileAccessPermission($URL_DATA_FILE)>="R")
		$DATA_FILE_NAME = $URL_DATA_FILE;

	if (strlen($DATA_FILE_NAME) <= 0)
		$arSetupErrors[] = GetMessage("CATI_NO_DATA_FILE");

	if (empty($arSetupErrors))
	{
		$IBLOCK_ID = (int)$IBLOCK_ID;
		$arIBlock = array();
		if ($IBLOCK_ID <= 0)
		{
			$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK");
		}
		else
		{
			$arIBlock = CIBlock::GetArrayByID($IBLOCK_ID);
			if (false === $arIBlock)
			{
				$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK");
			}
		}
	}

	if (empty($arSetupErrors))
	{
		if (!CIBlockRights::UserHasRightTo($IBLOCK_ID, $IBLOCK_ID, 'iblock_admin_display'))
			$arSetupErrors[] = GetMessage("CATI_NO_IBLOCK_RIGHTS");
	}

	if (!empty($arSetupErrors))
	{
		$STEP = 1;
	}
}

//********************  END ACTIONS  **********************************//

$aMenu = array(
	array(
		"TEXT"=>GetMessage("CATI_ADM_RETURN_TO_LIST"),
		"TITLE"=>GetMessage("CATI_ADM_RETURN_TO_LIST_TITLE"),
		"LINK"=>"/bitrix/admin/cat_import_setup.php?lang=".LANGUAGE_ID,
		"ICON"=>"btn_list",
	)
);

$context = new CAdminContextMenu($aMenu);

$context->Show();

if (!empty($arSetupErrors))
	ShowError(implode('<br>', $arSetupErrors));
?>
<!--suppress JSUnresolvedVariable -->
<form method="POST" action="<? echo $APPLICATION->GetCurPage(); ?>" ENCTYPE="multipart/form-data" name="dataload">
<?
$aTabs = array(
	array("DIV" => "edit1", "TAB" => GetMessage("CAT_ADM_CSV_IMP_TAB1"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_IMP_TAB1_TITLE")),
);

$tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
$tabControl->Begin();

$tabControl->BeginNextTab();

if ($STEP == 1)
{
	?><tr class="heading">
		<td colspan="2"><? echo GetMessage("CATI_DATA_LOADING"); ?></td>
	</tr>
	<tr>
		<td valign="top" width="40%"><? echo GetMessage("CATI_DATA_FILE_SITE"); ?>:</td>
		<td valign="top" width="60%">
			<input type="text" name="URL_DATA_FILE" size="40" value="<? echo htmlspecialcharsbx($URL_DATA_FILE); ?>">
			<input type="button" value="<? echo GetMessage("CATI_BUTTON_CHOOSE")?>" onclick="cmlBtnSelectClick();"><?
CAdminFileDialog::ShowScript(
	array(
		"event" => "cmlBtnSelectClick",
		"arResultDest" => array("FORM_NAME" => "dataload", "FORM_ELEMENT_NAME" => "URL_DATA_FILE"),
		"arPath" => array("PATH" => "/upload/catalog", "SITE" => SITE_ID),
		"select" => 'F',// F - file only, D - folder only, DF - files & dirs
		"operation" => 'O',// O - open, S - save
		"showUploadTab" => true,
		"showAddToMenuTab" => false,
		"fileFilter" => 'csv',
		"allowAllFiles" => true,
		"SaveConfig" => true
	)
);
		?></td>
	</tr>
	<tr>
		<td valign="top" width="40%"><? echo GetMessage("CATI_INFOBLOCK"); ?>:</td>
		<td valign="top" width="60%"><?
			if (!isset($IBLOCK_ID))
				$IBLOCK_ID = 0;
			echo GetIBlockDropDownListEx(
				$IBLOCK_ID,
				'IBLOCK_TYPE_ID',
				'IBLOCK_ID',
				array('CHECK_PERMISSIONS' => 'Y','MIN_PERMISSION' => 'W'),
				"",
				"",
				'class="adm-detail-iblock-types"',
				'class="adm-detail-iblock-list"'
			);
		?></td>
	</tr>
	<?
}

$tabControl->EndTab();

$tabControl->BeginNextTab();

?></form>
<script type="text/javascript">
<?if ($STEP < 2):?>
tabControl.SelectTab("edit1");
tabControl.DisableTab("edit2");
tabControl.DisableTab("edit3");
tabControl.DisableTab("edit4");
<?elseif ($STEP == 2):?>
tabControl.SelectTab("edit2");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit3");
tabControl.DisableTab("edit4");
<?elseif ($STEP == 3):?>
tabControl.SelectTab("edit3");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit2");
tabControl.DisableTab("edit4");
<?elseif ($STEP == 4):?>
tabControl.SelectTab("edit4");
tabControl.DisableTab("edit1");
tabControl.DisableTab("edit2");
tabControl.DisableTab("edit3");
<?endif;?>
function showTranslitSettings()
{
	var useTranslit = BX('USE_TRANSLIT_Y'),
		translitLang = BX('tr_TRANSLIT_LANG'),
		translitUpdate = BX('tr_USE_UPDATE_TRANSLIT');
	if (!BX.type.isElementNode(useTranslit) || !BX.type.isElementNode(translitLang) || !BX.type.isElementNode(translitUpdate))
		return;
	BX.style(translitLang, 'display', (useTranslit.checked ? 'table-row' : 'none'));
	BX.style(translitUpdate, 'display', (useTranslit.checked ? 'table-row' : 'none'));
}
BX.ready(function(){
	var useTranslit = BX('USE_TRANSLIT_Y'),
		translitLang = BX('tr_TRANSLIT_LANG'),
		translitUpdate = BX('tr_USE_UPDATE_TRANSLIT');
	if (BX.type.isElementNode(useTranslit) && BX.type.isElementNode(translitLang) && BX.type.isElementNode(translitUpdate))
		BX.bind(useTranslit, 'click', showTranslitSettings);
});
</script>