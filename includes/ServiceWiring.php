<?php

use BlueSpice\ProDistributionConnector\UserCounter;
use MediaWiki\MediaWikiServices;

return [
	'BlueSpiceUserCounter' => static function ( MediaWikiServices $services ) {
		return new UserCounter(
			$services->getConfigFactory()->makeConfig( 'bsg' ),
			$services->getDBLoadBalancer(),
			$services->getBlockManager()
		);
	}
];
