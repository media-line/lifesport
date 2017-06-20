<?
//<title>CSV (lifesport)</title>
use Bitrix\Main;
use Bitrix\Highloadblock as HL;
use Bitrix\Highloadblock\HighloadBlockTable as HLBT;
use Bitrix\Main\Entity;

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

function GetEntityDataClass($HlBlockId)
{
    if (empty($HlBlockId) || $HlBlockId < 1) {
        return false;
    }
    $hlblock = HLBT::getById($HlBlockId)->fetch();
    $entity = HLBT::compileEntity($hlblock);
    $entity_data_class = $entity->getDataClass();
    return $entity_data_class;
}

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
    public $name = null; //переменная с названием
    public $article = null; //переменная с артикулом
    public $color = null; //переменная с цветом
    public $size = null; //переменная с размером
    public $warehouse = null; //переменная с названием склада
    public $amount = null; //переменная с количеством на складе
    public $cost = null; //переменная с ценой
    public $idtp = null; //переменная с ID торгового предложения

    //метод запроса в БД
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

    //метод импорта значения
    function IMP($qr)
    {
        global $DB;
        $query = $qr;
        $result = $DB->Query($query);
        return $result;
    }

    //метод получения массива с ID торговых предложений по артикулу
    function getArrID()
    {
        $xml_id = $this->article;
        $sample = "IBLOCK_ELEMENT_ID";
        $qr = "SELECT IBLOCK_ELEMENT_ID FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID=130 AND VALUE='$xml_id'";
        $arID = $this->Query($qr, $sample);
        return $arID;
    }

    //метод получения Размера торговых предложений
    //на входе $idnumber - массив id торговых предложений
    //на выходе $arrShifSize[] - $key - id торгового предложения; $value - значение Размера для этого id
    function getSize($idnumber)
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
            for ($r = 0; $r < count($arSize); $r++) {
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

    //метод получения Цвета товара торговых предложений
    //на входе $arSize[] - $key - id торгового предложения; $value - значение Размера для этого id
    //на выходе $arColor[] - $key - id торгового предложения; $value - значение Цвета для этого id
    function getColor($arSize)
    {
        $arColor = [];
        foreach ($arSize as $key => $value) {
            $qr = "SELECT VALUE FROM b_iblock_element_property WHERE IBLOCK_PROPERTY_ID=129 AND IBLOCK_ELEMENT_ID=$key";
            $sample = "VALUE";
            $color[$key] = $this->Query($qr, $sample);
            unset($qr, $sample);
            foreach ($color as $key => $value) {
                $arColor[$key] = $value[count($value) - 1];
            }
        }
        return $arColor;
    }
}

//создаем функцию сравнения параметров объектов $import и $export
//на входе $pimport - параметр объекта $import; $pexport - параметр объекта $export
//на выходе $checkempty=true - если совпадений не найдено; $arr[] - где $value - id совпавших значений
function Compare($pimport, $pexport)
{
    $arr = [];
    foreach ($pexport as $key => $value) {
        if ($value == $pimport) {
            array_push($arr, $key);
        }
    }
    if (count($arr) == 0) {
        $checkempty = true;
        return $checkempty;
    } else {
        return $arr;
    }
}

foreach ($arIMP as $keyt => $value) {
    if ($keyt == 0) continue;
    if ($keyt == 2) break;
    $import = new ImportLS();
//определяем где в массиве из сайта будет артикул
    $import->article = $value[0];
//получаем массив ID торговых предложений по артикулу из БД
    $arID = $import->getArrID();
//получаем массив с Размером для каждого торгового предложения из БД
    $arSize = $import->getSize($arID);
//получаем массив с Цветом для каждого торгового предложения из БД
    $arColor = $import->getColor($arSize);
//из класса ImportLS создаём объект с параметрами (Цвет, Размер, ID торгового предложения) из БД ($export)
    $export = new ImportLS();
    $EColor = $export->color = $arColor; //цвет
    $ESize = $export->size = $arSize; //размер
    $EID = $export->idtp = $arID; //массив id торговых предложений
//в объект ($import) добавляем значения парметров (Цвет, Размер)
    $IColor = $import->color = $value[2]; //цвет
    $ISize = $import->size = $value[3]; //размер
//сравниваем массив цвета $export со значением параметра цвета $import
    $CompareColor = Compare($IColor, $EColor);
//сравниваем массив размера $export со значением параметра размера $import
    $CompareSize = Compare($ISize, $ESize);
    //если есть несовпадение цвета или размера - обрабатываем
    if (!(is_array($CompareColor)) || !(is_array($CompareSize))) {
        if ($CompareSize == true) {
            //проверяем наличие размера в БД
            $sample = "VALUE";
            $qr = "SELECT $sample FROM b_iblock_property_enum WHERE PROPERTY_ID=128";
            $AllSize = $import->Query($qr, $sample);
            $CheckSize = false;
            foreach ($AllSize as $item) {
                if ($item == $ISize) {
                    $CheckSize = true;
                    break;
                } else continue;
            }
            if ($CheckSize == false) {
                //если размера в БД нет - добавляем его в БД
                $valuet = "'" . $ISize . "'";
                $qr = "INSERT INTO b_iblock_property_enum (PROPERTY_ID, VALUE) VALUES (128, $valuet)";
                $import->IMP($qr);
                echo "добавлен новый размер - " . $ISize;
            } else {
                /**/
            }
        } else {
            /**/
        }
        if ($CompareColor == true) {
            //если нет совпадения в цвете проверяем его наличие в справочнике
            CModule::IncludeModule("highloadblock");
            //получаем массив со всеми цветами в справочнике
            $entity_data_class = GetEntityDataClass(1);
            $rsData = $entity_data_class::getList(array(
                'select' => array('*')
            ));
            $arrNewColor = [];
            while ($el = $rsData->fetch()) {
                array_push($arrNewColor, $el);
            }
            //проверяем сущестование цвета в справочнике
            $checkcolor = false;
            foreach ($arrNewColor as $key => $item) {
                if ($IColor == $arrNewColor[$key]["UF_XML_ID"]) {
                    $checkcolor = true;
                    break;
                } else continue;
            };
            if ($checkcolor == false) {
                //если цвета не существует в справочнике - добавляем в справочник новый цвет
                $entity_data_class = GetEntityDataClass(1);
                $result = $entity_data_class::add(array(
                    'UF_NAME' => $IColor,
                    'UF_XML_ID' => $IColor,
                ));

                //получаем название нового цвета
                $NewColor = $arrNewColor[count($arrNewColor) - 1]["UF_XML_ID"];
                $import->color = $NewColor;
                echo "добавлен новый цвет - " . $NewColor;
            } else {/**/
            }
        } else {
            /**/
        }

        //создаем новое торговое предложение
        $intSKUIBlock = 22;
        $arCatalog = CCatalog::GetByID($intSKUIBlock);
        $intProductIBlock = $arCatalog['PRODUCT_IBLOCK_ID']; // ID инфоблока товаров
        $intSKUProperty = $arCatalog['SKU_PROPERTY_ID']; // ID свойства в инфоблоке предложений типа "Привязка к товарам (SKU)"

        $intProductID = CCatalogSku::GetProductInfo($export->idtp[0], $intSKUIBlock); //получаем ID товара
        $ar_res = CCatalogProduct::GetList(
                array(),
                array(
                        "ID"=>$intProductID
                )
        );
        $ProductPropFID = $ar_res->Fetch();

        if ($intProductID) {
            $arProp[$intSKUProperty] = $intProductID;
            $arFields = array(
                'NAME' => $ProductPropFID["ELEMENT_NAME"],
                'IBLOCK_ID' => $intSKUIBlock,
                'ACTIVE' => 'Y',
                'PROPERTY_VALUES' => $arProp
            );

            $obElement = new CIBlockElement();
            $intOfferID = $obElement->Add($arFields); //добавляем новое торговое предложение
            $arrField = array(
                "ID" => $intOfferID,
            );
            $Up = CCatalogProduct::Add($arrField); //обновляем торговое предложение, чтобы его активировать

            //добавляем новые значения цвета и размера для вновь созданного торгового предложения
            //добавляем новый размер
            //определяем ID размера
            $sample = "ID";
            $IS = "'".$ISize."'";
            $qr = "SELECT $sample FROM b_iblock_property_enum WHERE PROPERTY_ID=128 AND VALUE=$IS";
            $querr = $import->Query($qr, $sample);
            //устанавливаем нужный размер, цвет и артикул
            $UpPropSKU = CIBlockElement::SetPropertyValuesEx(
                $intOfferID,
                $intSKUIBlock,
                array("SIZES" => $querr[0], "COLOR_REF" => $IColor, "ARTICLE" => $import->article)
            );
            $AddPriceSKU = CPrice::Add(
                    array(
                        "PRODUCT_ID" => $intOfferID,
                        "CATALOG_GROUP_ID" => 0,
                        "PRICE" => "0.00",
                        "CURRENCY" => "BYN"
                    )
            );
            $GetPrice = CPrice::GetBasePrice(
                $intOfferID, 1, 11
            );
            var_dump($GetPrice);
        }
    } else if ((is_array($CompareColor))&&(is_array($CompareSize))) {
        $GetPrice = CPrice::GetBasePrice(
            901, 1, 11
        );
        var_dump($GetPrice);
    }

//в объект ($import) добавляем значение параметра id торгового предложения и недостающие значения для импорта (Магазин, Количество, Цена)
    if (!(is_array($CompareColor)) || !(is_array($CompareSize))) {
        $Iid = $import->idtp = $intOfferID;
    } else {
        foreach ($CompareColor as $item) {
            foreach ($CompareSize as $initem) {
                if ($item == $initem) {
                    $Iid = $import->idtp = $initem;
                }
            }
        }
    }

    $IShop = $import->warehouse = $value[4];
    $IAmount = $import->amount = $value[5];
    $IPrice = $import->cost = $value[6];
//по полученному id получаем в объект $export значения (Магазин, Количество, Цена) этого торгового предложения из БД
//получаем цену

    $ar_res = CPrice::GetBasePrice($Iid, false);
    $EPrice = $export->cost = $ar_res["PRICE"];
    $idPrice = $ar_res["ID"];

//определяем ID нужного нам магазина
    $nmStore = CCatalogStore::GetList();
    $Store = [];
    while ($tmStore = $nmStore->Fetch()) {
        $Store[$tmStore["ID"]] = $tmStore["TITLE"];
    }
    foreach ($Store as $key => $item) {
        if ($item == $IShop) {
            $EShop = $export->warehouse = $StoreId = $key;
        } else {
//TODO: если совпадения магазина в БД и в файле импорта не найдено
        }
    }
//получаем количество товара в зависимости от магазина
    $rsStore = CCatalogStoreProduct::GetList(
        array(),
        array('PRODUCT_ID' => $Iid, 'STORE_ID' => $StoreId), false, false, array('ID', 'AMOUNT'));
    if ($arStore = $rsStore->Fetch()) {
        $EAmount = $export->amount = $arStore['AMOUNT'];
        $idAmount = $arStore["ID"];
    }
//сравниваем Цены, при необходимости - импортируем новые
    if ($EPrice != $IPrice) {
        $ChPrice = CPrice::Update(
            $idPrice,
            array("PRODUCT_ID" => $Iid, "PRICE" => $IPrice)
        );
        if ($ChPrice == false) {
            echo "<br/>" . $keyt . ". проблемы с изменением цены";
            continue;
        }
    }
//сравниваем количество, при необходимости - изменяем
    if ($EAmount != $IAmount) {
        $ChAmount = CCatalogStoreProduct::Update(
            $idAmount,
            array("PRODUCT_ID" => $Iid, "STORE_ID" => $EShop, "AMOUNT" => $IAmount)
        );
        if ($ChAmount == false) {
            echo "<br/>" . $keyt . ". проблемы с изменением количетсва";
        }
    }

    $finish = true;
}

$fileclose = fclose($fh);
if ($finish == true) {
    echo "Импорт товаров - завершен.";
}

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