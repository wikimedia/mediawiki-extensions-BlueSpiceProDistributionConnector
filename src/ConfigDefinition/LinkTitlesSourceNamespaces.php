<?php

namespace BlueSpice\ProDistributionConnector\ConfigDefinition;

use BlueSpice\ConfigDefinition;
use MediaWiki\Config\Config;
use MediaWiki\Context\IContextSource;
use MediaWiki\Language\Language;
use MediaWiki\MediaWikiServices;
use MediaWiki\Title\NamespaceInfo;

class LinkTitlesSourceNamespaces extends ConfigDefinition\ArraySetting implements ConfigDefinition\IOverwriteGlobal {

	/** @var NamespaceInfo */
	private $nsInfo;

	/** @var Language */
	private $language;

	/**
	 * @inheritDoc
	 */
	public static function getInstance( $context, $config, $name ) {
		$nsInfo = MediaWikiServices::getInstance()->getNamespaceInfo();
		$language = MediaWikiServices::getInstance()->getContentLanguage();
		return new static( $context, $config, $name, $nsInfo, $language );
	}

	/**
	 * @param IContextSource $context
	 * @param Config $config
	 * @param string $name
	 * @param NamespaceInfo $namespaceInfo
	 * @param Language $language
	 */
	public function __construct( $context, $config, $name, NamespaceInfo $namespaceInfo, Language $language ) {
		parent::__construct( $context, $config, $name );
		$this->nsInfo = $namespaceInfo;
		$this->language = $language;
	}

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
	 * @return array|mixed
	 */
	public function getValue() {
		return parent::getValue() ?: [ NS_MAIN ];
	}

	/**
	 * @return array
	 */
	protected function getOptions() {
		$nsOptions = [];
		foreach ( $this->nsInfo->getContentNamespaces() as $ns ) {
			if ( $ns === NS_MAIN ) {
				$label = $this->context->msg( 'bs-ns_main' )->text();
			} else {
				$label = $this->language->getNsText( $ns );
			}
			$nsOptions[$ns] = $label;
		}
		if ( !isset( $nsOptions[NS_MAIN] ) ) {
			$nsOptions[NS_MAIN] = $this->context->msg( 'bs-ns_main' )->text();
		}
		return $nsOptions;
	}

	/**
	 * @return string
	 */
	public function getLabelMessageKey() {
		return "bs-pro-distribution-config-link-titles-source-ns";
	}

	/**
	 * @return string
	 */
	public function getHelpMessageKey() {
		return "bs-pro-distribution-config-link-titles-source-ns-help";
	}

	/**
	 * @return string
	 */
	public function getGlobalName() {
		return 'wgLinkTitlesSourceNamespaces';
	}
}
