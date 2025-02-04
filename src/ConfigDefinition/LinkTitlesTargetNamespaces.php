<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

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
		return 'wgLinkTitlesTargetNamespaces';
	}

	/**
	 * @return array|mixed
	 */
	public function getValue() {
		return $this->getConfig()->get( $this->getName() );
	}
}
