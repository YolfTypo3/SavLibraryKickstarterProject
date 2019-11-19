<?php

defined('TYPO3_MODE') or die();


\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPItoST43(
    'sav_library_example2',
    'Classes/Controller/SavLibraryExample2Controller.php',
    '_pi1',
    'list_type',
    1
);

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('
plugin.tx_savlibraryexample2_pi1.userFunc = YolfTypo3\SavLibraryExample2\Controller\SavLibraryExample2Controller->main
'
);

?>
