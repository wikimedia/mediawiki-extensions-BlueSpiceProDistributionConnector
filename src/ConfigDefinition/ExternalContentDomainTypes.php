<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;
use HTMLMultiSelectPlusAdd;
use MediaWiki\Registration\ExtensionRegistry;

class ExternalContentDomainTypes extends ConfigDefinition\ArraySetting {

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
		return "bs-pro-distribution-ec-config-domain-types";
	}

	/**
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-pro-distribution-ec-config-domain-types-help';
	}

	/**
	 * @return bool
	 */
	public function isHidden() {
		return !ExtensionRegistry::getInstance()->isLoaded( 'External Content' );
	}

	/**
	 * @return HTMLMultiSelectPlusAdd
	 */
	public function getHtmlFormField() {
		return new HTMLMultiSelectPlusAdd( $this->makeFormFieldParams() );
	}
}
