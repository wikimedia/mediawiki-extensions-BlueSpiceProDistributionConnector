<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;

class ExternalContentDomainWhitelist
	extends ExternalContentFileExtensionWhitelist
	implements ConfigDefinition\IOverwriteGlobal
{

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return "bs-pro-distribution-ec-config-domain-whitelist";
	}

	/**
	 * @return string
	 */
	public function getHelpMessageKey() {
		return 'bs-pro-distribution-ec-config-domain-whitelist-help';
	}

	/**
	 * @return string
	 */
	public function getGlobalName() {
		return 'wgExternalContentDomainWhitelist';
	}
}
