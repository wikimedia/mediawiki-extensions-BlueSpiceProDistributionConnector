<?php

namespace BlueSpice\ProDistributionConnector\PDFCreator\Processor;

use BlueSpice\ProDistributionConnector\PDFCreator\Utility\ImageFinder;
use MediaWiki\Config\Config;
use MediaWiki\Extension\Math\Render\RendererFactory;
use MediaWiki\Extension\PDFCreator\IProcessor;
use MediaWiki\Extension\PDFCreator\Utility\ExportContext;
use MediaWiki\Utils\UrlUtils;
use MWStake\MediaWiki\Component\FileStorageUtilities\StorageHandler;

class Math implements IProcessor {

	/** @var UrlUtils */
	private $urlUtils;

	/** @var RendererFactory */
	private $rendererFactory;

	/** @var Config */
	private $config;

	/** @var StorageHandler */
	private $storageHandler;

	/**
	 * @param UrlUtils $urlUtils
	 * @param RendererFactory $rendererFactory
	 * @param Config $config
	 * @param StorageHandler $storageHandler
	 */
	public function __construct(
		UrlUtils $urlUtils, RendererFactory $rendererFactory, Config $config, StorageHandler $storageHandler
	) {
		$this->urlUtils = $urlUtils;
		$this->rendererFactory = $rendererFactory;
		$this->config = $config;
		$this->storageHandler = $storageHandler;
	}

	/**
	 * @inheritDoc
	 */
	public function execute( array &$pages, array &$images, array &$attachments,
		ExportContext $context, string $module = '', $params = [] ): void {
		$imageFinder = new ImageFinder( $this->urlUtils, $this->rendererFactory, $this->config, $this->storageHandler );
		$results = $imageFinder->execute( $pages, $images );

		/** @var WikiFileResource */
		foreach ( $results as $result ) {
			$filename = $result->getFilename();
			$images[$filename] = $result->getAbsolutePath();
		}
	}

	/**
	 * @inheritDoc
	 */
	public function getPosition(): int {
		return 50;
	}
}
