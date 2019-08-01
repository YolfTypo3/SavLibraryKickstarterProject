<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:sav_library_example5/Resources/Private/Language/locallang_db.xlf:tx_savlibraryexample5',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => 'ORDER BY tx_savlibraryexample5.crdate ',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:sav_library_example5/Resources/Public/Icons/icon_tx_savlibraryexample5.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,title,field1,field2,hook_content'
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label'  => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type'  => 'check',
                'default' => 0,
            ]
        ],
        'title' => [
            'exclude' => 1,
            'label'  => 'LLL:EXT:sav_library_example5/Resources/Private/Language/locallang_db.xlf:tx_savlibraryexample5.title',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim'
            ],
        ],
        'field1' => [
            'exclude' => 1,
            'label'  => 'LLL:EXT:sav_library_example5/Resources/Private/Language/locallang_db.xlf:tx_savlibraryexample5.field1',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim'
            ],
        ],
        'field2' => [
            'exclude' => 1,
            'label'  => 'LLL:EXT:sav_library_example5/Resources/Private/Language/locallang_db.xlf:tx_savlibraryexample5.field2',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim'
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'hidden, title, field1, field2, hook_content',
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => '']
    ],
];

?>