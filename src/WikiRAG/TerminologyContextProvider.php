<?php

namespace BlueSpice\ProDistributionConnector\WikiRAG;

use Lingo\Backend;
use MediaWiki\Extension\WikiRAG\IContextProvider;
use MediaWiki\Registration\ExtensionRegistry;

class TerminologyContextProvider implements IContextProvider {

	private ?Backend $backend = null;

	/** @var array{string, string}|null */
	private $term = null;

	/**
	 * @return string
	 */
	public function provide(): string {
		$data = [];
		while ( $this->term !== null ) {
			$data[$this->term[0] ] = $this->term[1];
			$this->term = $this->backend->next();
		}

		return json_encode( $data, JSON_PRETTY_PRINT );
	}

	/**
	 * @return bool
	 */
	public function canProvide(): bool {
		if ( !ExtensionRegistry::getInstance()->isLoaded( 'Lingo' ) ) {
			return false;
		}
		$this->initBackend();
		$this->term = $this->backend->next();
		return $this->term !== null;
	}

	/**
	 * @return string
	 */
	public function getExtension(): string {
		return 'json';
	}

	private function initBackend() {
		if ( !$this->backend ) {
			$this->backend = new $GLOBALS[ 'wgexLingoBackend' ]();
		}
	}
}
