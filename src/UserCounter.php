<?php

namespace BlueSpice\ProDistributionConnector;

use Config;
use Html;
use MediaWiki\Block\BlockManager;
use Message;
use User;
use Wikimedia\Rdbms\ILoadBalancer;

class UserCounter {

	/** @var array */
	private $userLimits = [
		'9d3cc888b83410c13b1406f784fb55129c36b520' => 50,
		'a416fa165396d208d806b63ba409b86e23c2cb10' => 100,
		'9d30f6794f1ee6bc3d8d4e7495aff9e6ffb12d75' => 250,
		'88530acf24588b4b8ad0cae873ab22d19f77f959' => 500,
		'20942e9dc9d1eed4c73f90add45f650b64d8f5e2' => 1000,
		'c9ece401393ea5533037675658c4299826a02859' => 2000,
		'2d5eddf952cfebd4966242c464acb3842f6f648c' => 2500,
		'c0e5ea7b86dde728f5d33de195210bbad57d9b71' => 5000,
		'0a6d3a57ad1565dbd78f1126df3d315932fd5489' => 10000,
		// Unlimited
		'5185bf3320c8930f2cd4b7bbbe500b7151ead181' => -1,
	];

	/** @var Config */
	protected $config;
	/** @var Config */
	protected $mainConfig;
	/** @var ILoadBalancer */
	protected $lb;
	/** @var BlockManager */
	protected $blockManager;

	/** @var EditionProvider */
	private $editionProvider;

	/** @var string[] */
	private $warningLevels = [
		70 => 'orange',
		90 => 'red'
	];

	/**
	 * @param Config $config
	 * @param Config $mainConfig
	 * @param ILoadBalancer $lb
	 * @param BlockManager $blockManager
	 * @param EditionProvider $editionProvider
	 */
	public function __construct(
		Config $config, Config $mainConfig, ILoadBalancer $lb,
		BlockManager $blockManager, EditionProvider $editionProvider
	) {
		$this->config = $config;
		$this->mainConfig = $mainConfig;
		$this->lb = $lb;
		$this->blockManager = $blockManager;
		$this->editionProvider = $editionProvider;
	}

	/**
	 * Get the maximum allowed active user from license
	 *
	 * @return int
	 */
	public function getUserLimit() {
		if ( !$this->editionProvider->checkRequiresLicense() ) {
			return -1;
		}
		$licenseKey = $this->normalizeLicenseKey( (string)$this->config->get( 'LicenseKey' ) );
		if ( !$licenseKey || !isset( $this->userLimits[$licenseKey] ) ) {
			return array_values( $this->userLimits )[0];
		}
		return $this->userLimits[$licenseKey];
	}

	/**
	 * Get the current number of active user.
	 * Bots and an additional whitelist are ignored.
	 *
	 * @return int
	 */
	public function getCurrentNumberOfUser() {
		$whitelist = $this->getUserNameWhitelist();
		$userCount = 0;

		$dbr = $this->lb->getConnection( DB_REPLICA );
		$result = $dbr->select(
			'user',
			[ '*' ],
			[ 'user_name NOT IN (' . $dbr->makeList( $whitelist ) . ')' ],
			__METHOD__
		);

		if ( !$result ) {
			return $userCount;
		}

		foreach ( $result as $row ) {
			$user = User::newFromRow( $row );
			$blockStatus = $this->blockManager->getUserBlock( $user, null, true );

			if ( $blockStatus === null ) {
				$userCount++;
			}
		}

		return (int)$userCount;
	}

	/**
	 * Get the formatted status sentence as HTML
	 * @return string
	 */
	public function getSentenceHtml() {
		return $this->getSentenceInternal( true );
	}

	/**
	 * Get status sentence
	 * @return string
	 */
	public function getSentence() {
		return $this->getSentenceInternal( false );
	}

	/**
	 * Get ratio of usage
	 *
	 * @param int|null $currentCount
	 * @param int|null $limit
	 * @return int|null
	 */
	public function getRatio( ?int $currentCount = null, ?int $limit = null ) {
		if ( $currentCount === null ) {
			$currentCount = $this->getCurrentNumberOfUser();
		}
		if ( $limit === null ) {
			$limit = $this->getUserLimit();
			if ( $limit < 1 ) {
				return null;
			}
		}
		return intval( ( $currentCount * 100 ) / $limit );
	}

	/**
	 * @param bool|null $html
	 * @return string
	 */
	protected function getSentenceInternal( $html = false ) {
		$currentCount = $this->getCurrentNumberOfUser();
		$limit = $this->getUserLimit();
		$percent = null;

		if ( $limit === 0 ) {
			$status = Message::newFromKey(
				'bs-pro-distribution-instance-status-number-of-users-unknown'
			)->params( $currentCount )->parse();
		} elseif ( $limit === -1 ) {
			$status = Message::newFromKey(
				'bs-pro-distribution-instance-status-number-of-users-unlimited'
			)->params( $currentCount )->parse();
		} else {
			$percent = $this->getRatio( $currentCount, $limit );
			if ( $percent > 99 ) {
				$status = Message::newFromKey(
					'bs-pro-distribution-instance-status-number-of-users-limited-full'
				)->params( $currentCount )->parse();
			} else {
				$status = Message::newFromKey(
					'bs-pro-distribution-instance-status-number-of-users-limited'
				)->params( $currentCount, $limit, $percent )->parse();
			}
		}

		if ( !$html ) {
			return $status;
		}

		if ( $percent !== null ) {
			$color = 'green';
			foreach ( $this->warningLevels as $level => $levelColor ) {
				if ( $level <= $percent ) {
					$color = $levelColor;
				}
			}

			$html = Html::openElement( 'span', [
				'style' => "color: $color",
			] );
		} else {
			$html = Html::openElement( 'span' );
		}

		$html .= $status;
		$html .= Html::closeElement( 'span' );

		return $html;
	}

	/**
	 * Get a whitelist with user which are not counted for the limit
	 *
	 * @return array
	 */
	private function getUserNameWhitelist() {
		return array_merge(
			$this->mainConfig->get( 'ReservedUsernames' ) ?? [],
			$this->config->get( 'UserLimitWhitelist' ) ?? []
		);
	}

	/**
	 * @param string $key
	 *
	 * @return string
	 */
	private function normalizeLicenseKey( string $key ): string {
		if ( !$key ) {
			return '';
		}
		$key = trim( strtolower( str_replace( '-', '', $key ) ) );
		return sha1( $key );
	}
}
