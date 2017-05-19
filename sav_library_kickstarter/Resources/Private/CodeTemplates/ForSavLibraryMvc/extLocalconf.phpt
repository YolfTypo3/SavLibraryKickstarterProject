<?php
<sav:function name="removeEmptyLines">
if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

<f:for each="{extension.newTables}" as="table">
<f:if condition="{table.save_and_new}">
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addUserTSConfig('options.saveDocNew.{table.tablename}=1');
</f:if>
</f:for>

// Configures the Dispatcher
\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    '{extension.general.1.vendorName}.' . $_EXTKEY,
    'Pi1',
    [
    // The first controller and its first action will be the default
	 <f:for each="{extension.forms}" as="form">
    '{form.title->sav:upperCamel()}' => 'list,single,edit,save,delete,deleteInSubform,upInSubform,downInSubform',
    </f:for>
    ],
    // Non-cachable controller actions
    [
    <f:for each="{extension.forms}" as="form">
    '{form.title->sav:upperCamel()}' => '{f:if(condition:form.listViewNotCached,then:'list,')}{f:if(condition:form.singleViewNotCached,then:'single,')}edit,save,delete,deleteInSubform,upInSubform,downInSubform',
    </f:for>
    ]
);
</sav:function>
?>
