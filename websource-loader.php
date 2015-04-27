<?php
$aHtmlPageDefinition = array(
	'http://www.example.com/'
);
define('BASE_PATH', realpath(dirname(__FILE__)));

$sNamespacePrefix = 'x';
$sServerHttpDestination = 'http://www.example.com';
foreach ($aHtmlPageDefinition as $sHtmlDocumentUrl) {
	$sHtml = file_get_contents($sHtmlDocumentUrl);

	$odom = new DOMDocument('1.0', 'UTF-8');
	$odom->loadHTML($sHtml);
	//$rootNamespace = $odom->lookupNamespaceUri($odom->namespaceURI);
	$oXPath = new DOMXPath($odom);
	//$oXPath->registerNamespace($sNamespacePrefix, $rootNamespace);

	$oFormValuesNodeList = $oXPath->query('//div[contains(@class,"aImgThumb")]/*');
	if ($oFormValuesNodeList->length > 0) {
		foreach ($oFormValuesNodeList as $oDomElement) {
			$sRelativeLink = $oDomElement->getAttribute('href');
			$sDestinationUrl = $sServerHttpDestination . $sRelativeLink;
			$sImage = file_get_contents($sDestinationUrl);
			$aRelativeUrlExploded = explode('/', $sRelativeLink);

			$iPreviousIndex = count($aRelativeUrlExploded) - 1;
			$sPreviousDir = $aRelativeUrlExploded[$iPreviousIndex];

			$aSlicedToDirectory = array_slice($aRelativeUrlExploded, 0, $iPreviousIndex);

			$sRelativeDestinationDir = implode('/', $aSlicedToDirectory);

			if (is_file(BASE_PATH . $sRelativeLink)) {
				continue;
			}

			if (!is_dir(BASE_PATH . $sRelativeDestinationDir)) {
				mkdir(BASE_PATH . $sRelativeDestinationDir);
			}

			file_put_contents(BASE_PATH . $sRelativeLink, $sImage);
		}
	}
}
?>
