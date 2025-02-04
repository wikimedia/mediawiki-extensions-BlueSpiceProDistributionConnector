<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;

class LinkTitlesParseOnEdit extends ConfigDefinition\BooleanSetting implements ConfigDefinition\IOverwriteGlobal {

	/**
	 * @return string[]
	 */
	public function getPaths() {
		return [
				static::MAIN_PATH_FEATURE . '/' . static::FEATURE_CONTENT_STRUCTURING . '/LinkTitles',
				static::MAIN_PATH_EXTENSION . '/LinkTitles/' . static::FEATURE_CONTENT_STRUCTURING,
				static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_PRO . '/LinkTitles',
			];
	}

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return "bs-pro-distribution-config-link-titles-parse-on-edit";
	}

	/**
	 * @return string
	 */
	public function getHelpMessageKey() {
		return "bs-pro-distribution-config-link-titles-parse-on-edit-help";
	}

	/**
	 * @return string
	 */
	public function getGlobalName() {
		return 'wgLinkTitlesParseOnEdit';
	}
}
