<?php

namespace Ssch\TYPO3Rector\Tests\Rector\v9\v4\RemoveMethodsFromEidUtilityAndTsfeRector\Fixture;

$GLOBALS['TSFE']->initFEuser();
$GLOBALS['TSFE']->storeSessionData();
$GLOBALS['TSFE']->previewInfo();
$GLOBALS['TSFE']->hook_eofe();
$GLOBALS['TSFE']->addTempContentHttpHeaders();
$GLOBALS['TSFE']->sendCacheHeaders();

?>
-----
<?php

namespace Ssch\TYPO3Rector\Tests\Rector\v9\v4\RemoveMethodsFromEidUtilityAndTsfeRector\Fixture;

$GLOBALS['TSFE']->fe_user->storeSessionData();

?>
