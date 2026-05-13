<?php

namespace BlueSpice\ProDistributionConnector\Collector;

use BS\UsageTracker\Collectors\Base;
use MediaWiki\Config\ConfigException;
use MediaWiki\Config\GlobalVarConfig;

class NoOfNamespaces extends Base {

	/**
	 * @return \BS\UsageTracker\CollectorResult
	 * @throws ConfigException
	 */
	public function getUsageData() {
		$oRes = new \BS\UsageTracker\CollectorResult( $this );
		$configBsg  = new GlobalVarConfig( 'bsg' );
		$pageTemplatesDisabled = count( $configBsg->get( 'PageTemplatesExcludeNs' ) ?? [] );
		$configSmwg  = new GlobalVarConfig( 'smwg' );
		$linksArray = $configSmwg->has( 'NamespacesWithSemanticLinks' ) ?
			$configSmwg->get( 'NamespacesWithSemanticLinks' ) : [];
		$SMWEnabled = count( array_filter(
					$linksArray,
					static function ( $value ) {
						return $value !== false;
					}
		) );
		$configWg  = new GlobalVarConfig( 'wg' );
		$stabilizedNamespaces = $configWg->has( 'ContentStabilizationEnabledNamespaces' ) ?
			$configWg->get( 'ContentStabilizationEnabledNamespaces' ) : [];
		$stabilizationEnabled = count( array_unique( $stabilizedNamespaces ) );

		$readConfirmationNamespaces = $configWg->has( 'NamespacesWithEnabledReadConfirmation' ) ?
			$configWg->get( 'NamespacesWithEnabledReadConfirmation' ) : [];
		$readConfirmationEnabled = count( array_unique( $readConfirmationNamespaces ) );
		$count =
		[
			"PageTemplatesDisabled" => $pageTemplatesDisabled,
			"SMWEnabled" => $SMWEnabled,
			"StabilizationEnabled" => $stabilizationEnabled,
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
