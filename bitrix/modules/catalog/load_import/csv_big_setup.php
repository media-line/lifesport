<?
//<title>CSV (lifesport)</title>
use Bitrix\Main;

IncludeModuleLangFile($_SERVER['DOCUMENT_ROOT'] . '/bitrix/modules/catalog/import_setup_templ.php');
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
if (($ACTION == 'IMPORT_EDIT' || $ACTION == 'IMPORT_COPY') && $STEP == 1) {
    if (isset($arOldSetupVars['IBLOCK_ID']))
        $IBLOCK_ID = $arOldSetupVars['IBLOCK_ID'];
    if (isset($arOldSetupVars['URL_DATA_FILE']))
        $URL_DATA_FILE = $arOldSetupVars['URL_DATA_FILE'];
    if (isset($arOldSetupVars['DATA_FILE_NAME']))
        $DATA_FILE_NAME = $arOldSetupVars['DATA_FILE_NAME'];
}
//********************  END ACTIONS  **********************************//

$aMenu = array(
    array(
        "TEXT" => GetMessage("CATI_ADM_RETURN_TO_LIST"),
        "TITLE" => GetMessage("CATI_ADM_RETURN_TO_LIST_TITLE"),
        "LINK" => "/bitrix/admin/cat_import_setup.php?lang=" . LANGUAGE_ID,
        "ICON" => "btn_list",
    )
);

$context = new CAdminContextMenu($aMenu);

$context->Show();

if (!empty($arSetupErrors))
    ShowError(implode('<br>', $arSetupErrors));
?>
<!--suppress JSUnresolvedVariable -->
<form method="POST" action="<? /* echo $APPLICATION->GetCurPage(); */ ?>" ENCTYPE="multipart/form-data" name="dataload">
    <?
    $aTabs = array(
        array("DIV" => "edit1", "TAB" => GetMessage("CAT_ADM_CSV_IMP_TAB1"), "ICON" => "store", "TITLE" => GetMessage("CAT_ADM_CSV_IMP_TAB1_TITLE")),
    );

    $tabControl = new CAdminTabControl("tabControl", $aTabs, false, true);
    $tabControl->Begin();

    $tabControl->BeginNextTab();

    if ($STEP == 1) {
        ?>
        <tr class="heading">
            <td colspan="2"><? echo GetMessage("CATI_DATA_LOADING"); ?></td>
        </tr>
        <tr>
            <td valign="top" width="40%"><? echo GetMessage("CATI_DATA_FILE_SITE"); ?>:</td>
            <td valign="top" width="60%">
                <input type="text" name="URL_DATA_FILE" size="40"
                       value="<? echo htmlspecialcharsbx($URL_DATA_FILE); ?>">
                <input type="button" value="<? echo GetMessage("CATI_BUTTON_CHOOSE") ?>"
                       onclick="cmlBtnSelectClick();"><?
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
            <td valign="top" width="40%">
                <input type="submit" value="Сохранить">
            </td>
            <td valign="top" width="60%"></td>
        </tr>
        <?
    }

    $tabControl->EndTab();

    ?>
</form>

<?php

$arIMP = [];
$fh = fopen($_SERVER['DOCUMENT_ROOT'] . $_POST["URL_DATA_FILE"], 'r');
while (($info = fgetcsv($fh, 1000, "@")) !== false) {
    //получаем преобразованный в массив csv-файл
    array_push($arIMP, explode(";", implode("", $info)));
}
//получаем массив со всеми существующими торговыми предложениями из БД
$arSKU = [];
$res1 = $DB->Query("SELECT * FROM b_iblock_element WHERE IBLOCK_ID=22");
while ($result = $res1->Fetch()) {
    array_push($arSKU, $result);
}

//при совпадении Артикул, Цвет, Размер, Склад надо сравнить Остаток, Цена и в случае расхождения - заменить
//функция сравнения параметров (Артикул, Цвет, Размер, Склад) из файла и из БД, а также последующих действий
function ComparisionParam($fromFile, $fromDB)
{
    $equality = 0;
    if ($fromFile == $fromDB) {
        $equality = $equality;
    } elseif ($fromFile != $fromDB) {
        $equality = $equality + 1;
    }
    return $equality;
}

//функция сравнения значений (Остаток, Цена) из файла импорта и из БД, а так же последующих действий
function ComparisionValue($fromFile, $fromDB)
{

}

//$arIMP[] - массив со всеми торговыми предложениями из файла
//$arSKU[] - массив со всеми торговыми предложениями из БД

//функция сравнения торговых предложений
function ComparisionSKU()
{

}

$fileclose = fclose($fh); ?>
<!-- блок тестирования полученных данных -->
<table>
    <tr>
        <td>
            <pre>
                <?php
                var_dump($arSKU);
                ?>
            </pre>
        </td>
        <td>
            <pre>
                <?php
                var_dump($arIMP);
                ?>
            </pre>
        </td>
    </tr>
</table>
<!-- /блок тестирования полученных данных -->

<script type="text/javascript">
    function showTranslitSettings() {
        var useTranslit = BX('USE_TRANSLIT_Y'),
            translitLang = BX('tr_TRANSLIT_LANG'),
            translitUpdate = BX('tr_USE_UPDATE_TRANSLIT');
        if (!BX.type.isElementNode(useTranslit) || !BX.type.isElementNode(translitLang) || !BX.type.isElementNode(translitUpdate))
            return;
        BX.style(translitLang, 'display', (useTranslit.checked ? 'table-row' : 'none'));
        BX.style(translitUpdate, 'display', (useTranslit.checked ? 'table-row' : 'none'));
    }
    BX.ready(function () {
        var useTranslit = BX('USE_TRANSLIT_Y'),
            translitLang = BX('tr_TRANSLIT_LANG'),
            translitUpdate = BX('tr_USE_UPDATE_TRANSLIT');
        if (BX.type.isElementNode(useTranslit) && BX.type.isElementNode(translitLang) && BX.type.isElementNode(translitUpdate))
            BX.bind(useTranslit, 'click', showTranslitSettings);
    });
</script>