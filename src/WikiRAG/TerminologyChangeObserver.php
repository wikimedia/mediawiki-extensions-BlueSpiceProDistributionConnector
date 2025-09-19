<?php

namespace BlueSpice\ProDistributionConnector\WikiRAG;

use MediaWiki\Extension\WikiRAG\ChangeObserver\HookObserver;
use MediaWiki\HookContainer\HookContainer;
use MediaWiki\Message\Message;
use MediaWiki\Storage\Hook\PageSaveCompleteHook;

class TerminologyChangeObserver extends HookObserver implements PageSaveCompleteHook {

	/** @var string */
	private string $lingoPage = '';

	/**
	 * @param HookContainer $hookContainer
	 */
	public function __construct(
		HookContainer $hookContainer
	) {
		parent::__construct( $hookContainer );
		$hookContainer->register( 'PageSaveComplete', [ $this, 'onPageSaveComplete' ] );
		// Unfortunately, Lingo offers no method to get the page name
		$this->lingoPage =
			$GLOBALS['wgexLingoPage'] ?:
			Message::newFromKey( 'lingo-terminologypagename' )->inContentLanguage()->text();
		$this->lingoPage = str_replace( ' ', '_', trim( $this->lingoPage ) );
	}

	/**
	 * @inheritDoc
	 */
	public function onPageSaveComplete( $wikiPage, $user, $summary, $flags, $revisionRecord, $editResult ) {
		if ( $this->lingoPage && $wikiPage->getTitle()->getPrefixedDBkey() === $this->lingoPage ) {
			$this->scheduler?->scheduleContextProvider( 'terminology' );
		}
	}
}
