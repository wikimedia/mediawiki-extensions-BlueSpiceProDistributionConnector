<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;
use HTMLMultiSelectPlusAdd;
use MediaWiki\Registration\ExtensionRegistry;

class ExternalContentFileExtensionWhitelist
	extends ConfigDefinition\ArraySetting
	implements ConfigDefinition\IOverwriteGlobal
{

	/**
	 * @return string[]
	 */
	public function getPaths() {
		$ext = 'BlueSpiceProDistributionConnector';
		return [
			static::MAIN_PATH_FEATURE . '/' . static::FEATURE_EDITOR . "/$ext",
			static::MAIN_PATH_EXTENSION . "/$ext/" . static::FEATURE_EDITOR,
			static::FEATURE_EDITOR . '/' . static::PACKAGE_PRO . "/$ext",
		];
	}

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return "bs-pro-distribution-ec-config-file-whitelist";
	}

	/**
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-pro-distribution-ec-config-file-whitelist-help';
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return !ExtensionRegistry::getInstance()->isLoaded( 'External Content' );
	}

	/**
	 * @return string
	 */
	public function getGlobalName() {
		return 'wgExternalContentFileExtensionWhitelist';
	}

	/**
	 *
	 * @return HTMLMultiSelectPlusAdd
	 */
	public function getHtmlFormField() {
		return new HTMLMultiSelectPlusAdd( $this->makeFormFieldParams() );
	}
}
