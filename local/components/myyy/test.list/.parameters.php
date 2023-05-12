<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

if(!CModule::IncludeModule("iblock"))
    return;

//закидываем все типы инфоблоков в $iblockTypes
$iblockTypes = CIBlockParameters::GetIBlockTypes(["-"=>" "]);

//получаем все ид инфоблоков для выбраного типа инфоблока
$arIBlocks=["-"=>" "];
$db_iblock = CIBlock::GetList(["SORT"=>"ASC"], ["SITE_ID"=>$_REQUEST["site"],
    "TYPE" => ($arCurrentValues["IBLOCK_TYPE"]!="-"?$arCurrentValues["IBLOCK_TYPE"]:"")]);
while($arRes = $db_iblock->Fetch())
    $arIBlocks[$arRes["ID"]] = "[".$arRes["ID"]."] ".$arRes["NAME"];


$arComponentParameters=[
    "GROUPS" => [
        "FILTER_SETTINGS" => [
            "SORT" => 150,
            "NAME" => GetMessage("TEST_IBLOCK_DESC_FILTER_SETTINGS"),
        ]
    ],
    "PARAMETERS" => [
        "IBLOCK_TYPE" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("TEST_IBLOCK_LIST_TYPE"),
            "TYPE" => "LIST",
            "VALUES" => $iblockTypes,
            "REFRESH" => "Y",
        ],
        "IBLOCK_ID" => [
            "PARENT" => "BASE",
            "NAME" => GetMessage("TEST_IBLOCK_DESC_LIST_ID"),
            "TYPE" => "LIST",
            "VALUES" => $arIBlocks,
            "REFRESH" => "Y",
        ],
        "USE_FILTER" => [
            "PARENT" => "FILTER_SETTINGS",
            "NAME" => GetMessage("TEST_IBLOCK_DESC_USE_FILTER"),
            "TYPE" => "CHECKBOX",
            "DEFAULT" => "N",
            "REFRESH" => "Y",
        ]
    ]
];

if($arCurrentValues["USE_FILTER"]=="Y"){
    $arComponentParameters["PARAMETERS"]["FILTER_NAME"]=[   //имя массива для фильтра
        "PARENT" => "FILTER_SETTINGS",
        "NAME" => GetMessage("TEST_IBLOCK_FILTER"),
        "TYPE" => "STRING",
        "DEFAULT" => "",
    ];
    $arComponentParameters["PARAMETERS"]["FILTER_FIELD_CODE"] = CIBlockParameters::GetFieldCode(GetMessage("TEST_IBLOCK_FIELD"), "FILTER_SETTINGS");
}