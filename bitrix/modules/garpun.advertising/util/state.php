<?php

$rs_state = Array(
    "NAME" => Array(
        "READONLY" => "N"
    ),
    "TYPE" => Array(
        "READONLY" => "Y"
    ),
    "SOURCE" => Array(
        "READONLY" => "Y"
    ),
    "TYPE_ALGORITM" => Array(
        "READONLY" => "Y"
    ),
    "GEO" => Array(
        "READONLY" => "N"
    ),
    "VS_NAME" => Array(
        "READONLY" => "Y"
    ),
    "VS_PRICE" => Array(
        "READONLY" => "N"
    ),
    "UPDATE_TIME" => Array(
        "VISIBLE" => "Y"
    ),
    "BUTTON_RS_APPLAY" => Array(
        "VISIBLE" => "Y",
    ),
    "BUTTON_D_TO_LIST" => Array(
        "VISIBLE" => "N",
    ),
);
$stateList = Array(
    "N" => Array(
        "NAME" => Array(
            "READONLY" => "N"
        ),
        "TYPE" => Array(
            "READONLY" => "N"
        ),
        "SOURCE" => Array(
            "READONLY" => "N"
        ),
        "SOURCE" => Array(
            "READONLY" => "N"
        ),
        "TYPE_ALGORITM" => Array(
            "READONLY" => "N"
        ),
        "GEO" => Array(
            "READONLY" => "N"
        ),
        "VS_NAME" => Array(
            "READONLY" => "N"
        ),
        "VS_PRICE" => Array(
            "READONLY" => "N"
        ),
        "UPDATE_TIME" => Array(
            "READONLY" => "N",
            "VISIBLE" => "Y"
        ),
        "BUTTON_N_TO_D" => Array(
            "VISIBLE" => "Y",
        ),
        "BUTTON_R_TO_S" => Array(
            "VISIBLE" => "N",
        ),
        "BUTTON_S_TO_R" => Array(
            "VISIBLE" => "N",
        ),
        "BUTTON_R_TO_D" => Array(
            "VISIBLE" => "N",
        ),
        "BUTTON_RS_APPLAY" => Array(
            "VISIBLE" => "Y",
        ),
        "BUTTON_D_TO_LIST" => Array(
            "VISIBLE" => "N",
        ),
    ),
    /**/
    "D" => Array(
        "NAME" => Array(
            "READONLY" => "Y",
            "VISIBLE" => "N"
        ),
        "TYPE" => Array(
            "READONLY" => "Y",
            "VISIBLE" => "N"
        ),
        "SOURCE" => Array(
            "READONLY" => "Y",
            "VISIBLE" => "N"
        ),
        "TYPE_ALGORITM" => Array(
            "READONLY" => "Y",
            "VISIBLE" => "N"
        ),
        "GEO" => Array(
            "READONLY" => "Y",
            "VISIBLE" => "N"
        ),
        "VS_NAME" => Array(
            "READONLY" => "Y",
            "VISIBLE" => "N"
        ),
        "VS_PRICE" => Array(
            "READONLY" => "Y",
            "VISIBLE" => "N"
        ),
        "UPDATE_TIME" => Array(
            "VISIBLE" => "N"
        ),
        "BUTTON_N_TO_D" => Array(
            "VISIBLE" => "N",
        ),
        "BUTTON_R_TO_S" => Array(
            "VISIBLE" => "N",
        ),
        "BUTTON_S_TO_R" => Array(
            "VISIBLE" => "N",
        ),
        "BUTTON_R_TO_D" => Array(
            "VISIBLE" => "N",
        ),
        "LOADER_N_TO_D" => Array(
            "VISIBLE" => "Y",
        ),
        "BUTTON_RS_APPLAY" => Array(
            "VISIBLE" => "Y",
        ),
        "BUTTON_D_TO_LIST" => Array(
            "VISIBLE" => "Y",
        ),
    ),
);

$stateList["S"] = array_merge($rs_state + Array(
    "BUTTON_N_TO_D" => Array(
        "VISIBLE" => "N",
    ),
    "BUTTON_R_TO_S" => Array(
        "VISIBLE" => "N",
    ),
    "BUTTON_S_TO_R" => Array(
        "VISIBLE" => "Y",
    ),
    "BUTTON_R_TO_D" => Array(
        "VISIBLE" => "N",
    ),
        ));
$stateList["S"]["UPDATE_TIME"] = Array(
    "READONLY" => "N",
);
$stateList["R"] = array_merge($rs_state, Array(
    "BUTTON_N_TO_D" => Array(
        "VISIBLE" => "N",
    ),
    "BUTTON_R_TO_S" => Array(
        "VISIBLE" => "Y",
    ),
    "BUTTON_S_TO_R" => Array(
        "VISIBLE" => "N",
    ),
    "BUTTON_R_TO_D" => Array(
        "VISIBLE" => "Y",
    ),
        )
);
