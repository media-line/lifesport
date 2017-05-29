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
                <input type="submit" value="Загрузить файл">
            </td>
            <td valign="top" width="60%"></td>
        </tr>
        <?
    }

    $tabControl->EndTab();

    ?>
</form>

<?php
$arIMP = []; //массив содержащий данные по торговым предложениям из преобразованного файла-импорта
$fh = fopen($_SERVER['DOCUMENT_ROOT'] . $_POST["URL_DATA_FILE"], 'r');
while (($info = fgetcsv($fh, 1000, "@")) !== false) {
    //получаем файл-импорт преобразованный в $arIMP[]
    array_push($arIMP, explode(";", implode("", $info)));
}

//создаем класс для импорта
class ImportLS
{
    public $article = null; //переменная с артикулом
    public $color = null; //переменная с цветом
    public $size = null; //переменная с размером
    public $warehouse = null; //переменная с названием склада
    public $number = null; //переменная с количеством на складе
    public $cost = null; //переменная с ценой
    public $idtp = null; //переменная с ID торгового предложения

    //функция запроса в БД
    function Query($qr, $sample, $samplekey = null)
    {
        global $DB;
        $arr = [];
        $query = $qr;
        $res = $DB->Query($query);
        while ($result = $res->Fetch()) {
            if ($samplekey == null) {
                array_push($arr, $result[$sample]);
            } else {
                $arr[$result[$samplekey]] = $result[$sample];
            }
        }
        //уничтожаем временные переменные
        unset($query);
        unset($res);
        unset($result);
        //возвращаем массив с массивом ID торговых предложений
        return $arr;
    }

    //функция получения массива с ID торговых предложений по артикулу
    function getArrID()
    {
        $xml_id = $this->article;
        $sample = "IBLOCK_ELEMENT_ID";
        $qr = "SELECT IBLOCK_ELEMENT_ID FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID=130 AND VALUE='$xml_id'";
        $arID = $this->Query($qr, $sample);
        return $arID;
    }

    //функция получения значений Размера у ID торговых предложений
    function getSizeColor($idnumber)
    {
        //получаем массив с расшифровкой размера для торговых предложений ($arDesh[]; $key = шифр-ID; $item = значение размера)
        $qr = "SELECT ID, VALUE FROM b_iblock_property_enum WHERE PROPERTY_ID=128";
        $samplekey = "ID";
        $sample = "VALUE";
        $arDesh = $this->Query($qr, $sample, $samplekey);
        unset($qr, $sample, $samplekey);

        //получаем массив с зашифрованными значениями размера для каждого торгового предложения
        $arrShifSize = [];
        foreach ($idnumber as $key => $value) {
            $qr = "SELECT VALUE FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID=128 AND IBLOCK_ELEMENT_ID=$value";
            $sample = "VALUE";
            $arSize = $this->Query($qr, $sample);
            for ($r=0;$r<count($arSize);$r++) {
                $arrShifSize[$value] = $arSize[$r];
            }
        }

        //преобразуем зашифрованные значения размера в нормальные
        foreach ($arrShifSize as $key => $value) {
            foreach ($arDesh as $id => $item) {
                if ($id == $value) {
                    $size = $item;
                }
            }
            $arrShifSize[$key] = $size;
        }

        //возвращаем массив значений размеров для каджого торгового предложения ($key - ID торгового предложения; $item - нормальное значение размера)
        return $arrShifSize;
    }
}

$import = new ImportLS();
foreach ($arIMP as $key => $value) {
    if ($key == 0) continue;
    if ($key == 2) break;
    $import->article = $value[0];
    $arID = $import->getArrID();
    $arSize = $import->getSizeColor($arID);
    var_dump($arID);
    var_dump($arSize);

}

$fileclose = fclose($fh);
?>


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