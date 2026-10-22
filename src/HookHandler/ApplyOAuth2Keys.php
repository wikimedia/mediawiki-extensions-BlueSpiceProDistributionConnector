<?php

namespace BlueSpice\ProDistributionConnector\HookHandler;

use MediaWiki\Config\ConfigFactory;
use MediaWiki\Registration\ExtensionRegistry;

class ApplyOAuth2Keys {

	private const KEYS = [ 'OAuth2PublicKey', 'OAuth2PrivateKey' ];

	/**
	 * @param ConfigFactory $configFactory
	 */
	public function __construct( private readonly ConfigFactory $configFactory ) {
	}

	/**
	 * The OAuth session provider reads the keys during session setup, which happens before
	 * extension functions run. Therefore the values from Special:BlueSpiceConfigManager must
	 * be applied here, not only in `BlueSpice\Foundation::overwriteGlobals`.
	 *
	 * @return void
	 */
	public function onSetupAfterCache() {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'OAuth' ) ) {
			return;
		}
		$config = $this->configFactory->makeConfig( 'bsg' );
		foreach ( self::KEYS as $key ) {
			$value = $config->get( $key );
			if ( is_string( $value ) && $value !== '' ) {
				$GLOBALS["wg$key"] = $value;
			}
		}
	}
}
