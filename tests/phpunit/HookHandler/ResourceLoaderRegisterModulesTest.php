<?php

namespace BlueSpice\ProDistributionConnector\Tests\HookHandler;

use BlueSpice\ProDistributionConnector\HookHandler\ResourceLoaderRegisterModules;
use MediaWiki\Registration\ExtensionRegistry;
use MediaWiki\ResourceLoader\ResourceLoader;

/**
 * @covers \BlueSpice\ProDistributionConnector\HookHandler\ResourceLoaderRegisterModules
 */
class ResourceLoaderRegisterModulesTest extends \MediaWikiIntegrationTestCase {

	/**
	 * @dataProvider provideEnabled
	 */
	public function testConditionalRegistration( $enabled ) {
		$extensionRegistry = $this->createMock( ExtensionRegistry::class );
		$extensionRegistry->method( 'isLoaded' )
			->willReturn( $enabled );

		$resourceLoader = $this->createMock( ResourceLoader::class );
		$resourceLoader->expects( $enabled ? $this->once() : $this->never() )
			->method( 'register' )
			->with(
				$this->arrayHasKey(
					'ext.prodistributionconnector.externalcontent.vePlugin'
				)
			);

		( new ResourceLoaderRegisterModules( $extensionRegistry ) )
			->onResourceLoaderRegisterModules( $resourceLoader );
	}

	public static function provideEnabled() {
		return [
			'External Content loaded' => [ true ],
			'External Content not loaded' => [ false ],
		];
	}
}
