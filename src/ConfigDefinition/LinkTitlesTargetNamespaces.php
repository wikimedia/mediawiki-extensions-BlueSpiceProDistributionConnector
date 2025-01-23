<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;
use MediaWiki\Language\Language;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\NamespaceInfo;

class LinkTitlesTargetNamespaces extends LinkTitlesSourceNamespaces {

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return "bs-pro-distribution-config-link-titles-target-ns";
	}

	/**
	 * @return string
	 */
	public function getHelpMessageKey() {
		return "bs-pro-distribution-config-link-titles-target-ns-help";
	}

	/**
	 * @return string
	 */
	public function getGlobalName() {
		return 'LinkTitlesTargetNamespaces';
	}
}
