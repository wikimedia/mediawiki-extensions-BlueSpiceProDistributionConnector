<?php

namespace BlueSpice\ProDistributionConnector\HookHandler;

use BlueSpice\Privacy\Hook\BlueSpicePrivacyPageProviderGetPageHook;
use BlueSpice\Privacy\Hook\BlueSpicePrivacyShowShowMissingPageAlertHook;
use MediaWiki\Skin\Skin;
use MediaWiki\Title\Title;
use MediaWiki\Title\TitleFactory;

class GetPrivacyPageTitle implements
	BlueSpicePrivacyPageProviderGetPageHook,
	BlueSpicePrivacyShowShowMissingPageAlertHook
{

	/**
	 * @param TitleFactory $titleFactory
	 */
	public function __construct(
		private readonly TitleFactory $titleFactory
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function onBlueSpicePrivacyPageProviderGetPage( string $name, ?Title &$title ): void {
		if ( !defined( 'FARMER_CALLED_INSTANCE' ) || FARMER_CALLED_INSTANCE === 'w' ) {
			return;
		}
		$title = $this->titleFactory->newFromText( 'w:Special:PrivacyPages/' . $name );
	}

	/**
	 * @param Skin $skin
	 * @param bool &$showAlert
	 * @return void
	 */
	public function onBlueSpicePrivacyShowShowMissingPageAlert( Skin $skin, bool &$showAlert ): void {
		if ( !defined( 'FARMER_CALLED_INSTANCE' ) || FARMER_CALLED_INSTANCE !== 'w' ) {
			$showAlert = false;
		}
	}
}
