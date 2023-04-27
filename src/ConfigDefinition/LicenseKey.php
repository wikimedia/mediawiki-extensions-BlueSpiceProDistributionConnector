<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;

class LicenseKey extends ConfigDefinition\StringSetting {

	/**
	 * @return string[]
	 */
	public function getPaths() {
		return [
				static::MAIN_PATH_FEATURE . '/' . static::FEATURE_SYSTEM . '/BlueSpiceProDistributionConnector',
				static::MAIN_PATH_EXTENSION . '/BlueSpiceProDistributionConnector/' . static::FEATURE_SYSTEM ,
				static::MAIN_PATH_PACKAGE . '/' . static::PACKAGE_PRO . '/BlueSpiceProDistributionConnector',
			];
	}

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return "bs-pro-distribution-config-license-key";
	}
}
