<?php

namespace BlueSpice\ProDistributionConnector\Collector;

use BS\UsageTracker\Collectors\Base;
use GlobalVarConfig;

class NoOfNamespaces extends Base {

	/**
	 *
	 * @return \BS\UsageTracker\CollectorResult
	 * @throws \MWException
	 */
	public function getUsageData() {
		$oRes = new \BS\UsageTracker\CollectorResult( $this );
		$configBsg  = new GlobalVarConfig( 'bsg' );
		$pageTemplatesDisabled = count( $configBsg->get( 'PageTemplatesExcludeNs' ) ?? [] );
		$configSmwg  = new GlobalVarConfig( 'smwg' );
		$linksArray = $configSmwg->get( 'NamespacesWithSemanticLinks' );
		$SMWEnabled = count( array_filter(
					$linksArray,
					static function ( $value ) {
						return $value !== false;
					}
		) );
		$configWg  = new GlobalVarConfig( 'wg' );
		$flaggedRevsEnabled = count( array_unique( $configWg->get( 'FlaggedRevsNamespaces' ) ?? [] ) );
		$readConfirmationEnabled = count( array_unique(
			$configWg->get( 'NamespacesWithEnabledReadConfirmation' ) ?? []
		) );
		$count =
		[
			"PageTemplatesDisabled" => $pageTemplatesDisabled,
			"SMWEnabled" => $SMWEnabled,
			"FlaggedRevsEnabled" => $flaggedRevsEnabled,
			"ReadConfirmationEnabled" => $readConfirmationEnabled

		];
		$prefix = $oRes->identifier;
		$contentarray = [];
		foreach ( $count as $key => $val ) {
			array_push( $contentarray, $this->getCollectorData( $key, $val, $oRes, $prefix ) );

		}
		return $contentarray;
	}

	/**
	 * @param string $key
	 * @param array $val
	 * @param CollectorResult $res
	 * @param string $prefix
	 * @return array
	 */
	protected function getCollectorData( $key, $val, $res, $prefix ) {
		$contentarray = [];
		$res->count = $val;
		$res->identifier = $prefix . "." . ( $key );
		return array_merge( $contentarray, (array)$res );
	}

}
