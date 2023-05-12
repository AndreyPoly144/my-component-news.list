<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();

class testList extends CBitrixComponent
{
    public function executeComponent()
    {
        //подключение компонента catalog.filter
        self::includeFilter($this->arParams);

        //обработка arParams["FILTER_NAME"]
        $this->arParams = self::checkFilterName($this->arParams);

        //получение $arrFilter из $GLOBALS
        $arrFilter = self::getArrFilter($this->arParams);

        //заполнение $arResult['ITEMS']
        if ($this->arParams['IBLOCK_ID'] != '-' || $this->arParams['IBLOCK_TYPE'] != '-') {
            $this->arResult = self::addItems(
                $this->arParams['IBLOCK_ID'] == '-' ? '' : $this->arParams['IBLOCK_ID'],
                $this->arParams['IBLOCK_TYPE'] == '-' ? '' : $this->arParams['IBLOCK_TYPE'],
                $arrFilter);
        }
        $this->includeComponentTemplate();
    }


    public static function addItems($IBLOCK_ID = '', $IBLOCK_TYPE = '', $arrFilter): array
    {
        $arSelect = ["ID", "NAME", "IBLOCK_ID", "DETAIL_PAGE_URL", "DETAIL_TEXT", "PREVIEW_TEXT", "PREVIEW_PICTURE"];
        $arFilter = ["IBLOCK_ID" => $IBLOCK_ID, "IBLOCK_TYPE" => $IBLOCK_TYPE];
        $filter = array_merge($arFilter, $arrFilter);


        $res = CIBlockElement::GetList([], $filter, false, [], $arSelect);
        while ($ob = $res->GetNextElement()) {
            $arFields = $ob->GetFields();
            $arResult['ITEMS'][$arFields['IBLOCK_ID']][$arFields['ID']] = $arFields;
        }
        return $arResult;
    }

    public static function includeFilter($arParams): void
    {
        if ($arParams["USE_FILTER"] == "Y"):
            global $APPLICATION;
            $APPLICATION->IncludeComponent(
                "bitrix:catalog.filter",
                "",
                [
                    "IBLOCK_TYPE" => $arParams["IBLOCK_TYPE"],
                    "IBLOCK_ID" => $arParams["IBLOCK_ID"],
                    "FILTER_NAME" => $arParams["FILTER_NAME"],
                    "FIELD_CODE" => $arParams["FILTER_FIELD_CODE"]
                ]
            );
            ?>
            <br/>
        <? endif;
    }

    public static function checkFilterName($arParams): array
    {
        if ($arParams["USE_FILTER"] == "Y") {
            if ($arParams["FILTER_NAME"] == '' || !preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"]))
                $arParams["FILTER_NAME"] = "arrFilter";
        } else
            $arParams["FILTER_NAME"] = "";
        return $arParams;
    }

    public static function getArrFilter($arParams): array
    {
        $arrFilter = [];
        if (!empty($arParams["FILTER_NAME"]) && preg_match("/^[A-Za-z_][A-Za-z01-9_]*$/", $arParams["FILTER_NAME"])) {
            $arrFilter = $GLOBALS[$arParams["FILTER_NAME"]] ?? [];
            if (!is_array($arrFilter)) {
                $arrFilter = [];
            }
        }
        return $arrFilter;
    }
}