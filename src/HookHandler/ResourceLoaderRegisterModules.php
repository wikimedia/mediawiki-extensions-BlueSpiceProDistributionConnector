<?php

namespace BlueSpice\ProDistributionConnector\HookHandler;

use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\Hook\ResourceLoaderRegisterModulesHook;
use MediaWiki\ResourceLoader\ResourceLoader;

class ResourceLoaderRegisterModules implements ResourceLoaderRegisterModulesHook {

	private ExtensionRegistry $extensionRegistry;

	public function __construct(
		ExtensionRegistry $extensionRegistry,
	) {
		$this->extensionRegistry = $extensionRegistry;
	}

	/**
	 * @inheritDoc
	 */
	public function onResourceLoaderRegisterModules( ResourceLoader $rl ): void {
		$localBasePath = dirname( __DIR__, 2 ) . '/resources';
		$remoteExtPath = 'BlueSpiceDistributionConnector/resources';

		if ( $this->extensionRegistry->isLoaded( 'External Content' ) ) {
			$rl->register( [
				'ext.prodistributionconnector.externalcontent.vePlugin' => [
					'packageFiles' => [
						[
							'name' => 've/datatransferhandler/supportedUrls.json',
							'callback' =>
								'BlueSpice\\ProDistributionConnector\\ExternalContentClientConfig'
								. '::getSupportedDomainsForPaste',
						],
						've/datatransferhandler/ExternalContentUrl.js',
					],
					'dependencies' => [
						'ext.visualEditor.core',
					],
				],
			] );
		}
	}
}
