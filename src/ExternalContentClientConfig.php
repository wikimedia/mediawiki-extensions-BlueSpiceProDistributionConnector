<?php

namespace BlueSpice\ProDistributionConnector;

class ExternalContentClientConfig {

	/**
	 * Path-aware regexes for known domains.
	 * These ensure only embeddable URLs (pointing to file content) are matched,
	 * not issues, PRs, settings, etc.
	 */
	private const KNOWN_DOMAIN_PATTERNS = [
		// GitHub: requires owner/repo, optionally followed by /blob/ or /tree/
		'github.com' => '^https?:\/\/(www\.)?github\.com\/[^\/]+\/[^\/]+(\/?$|\/(blob|tree)\/.+$)',
		// GitLab: requires group/project, optionally followed by /-/blob/ or /-/tree/
		'gitlab.com' => '^https?:\/\/(www\.)?gitlab\.com\/[^\/]+\/[^\/]+(\/?$|\/-\/(blob|tree)\/.+$)',
		// Bitbucket Server: requires /projects/X/repos/Y structure
		'bitbucket.org' => '^https?:\/\/(www\.)?bitbucket\.org\/projects\/[^\/]+\/repos\/[^\/]+',
	];

	/**
	 * Regex templates keyed by platform type.
	 * Used for self-hosted instances via bsgExternalContentDomainTypes config.
	 */
	private const TYPE_PATTERNS = [
		'github' => '^https?:\/\/(www\.)?%s\/[^\/]+\/[^\/]+(\/?$|\/(blob|tree)\/.+$)',
		'gitlab' => '^https?:\/\/(www\.)?%s\/[^\/]+\/[^\/]+(\/?$|\/-\/(blob|tree)\/.+$)',
		'bitbucket' => '^https?:\/\/(www\.)?%s\/projects\/[^\/]+\/repos\/[^\/]+',
	];

	/**
	 * Provides regexes to match pasted URLs for external content URLs
	 *
	 * @return array
	 */
	public static function getSupportedDomainsForPaste(): array {
		$whitelist = $GLOBALS['wgExternalContentDomainWhitelist'];
		if ( empty( $whitelist ) ) {
			$whitelist = [
				'gitlab.com',
				'github.com',
				'bitbucket.org',
			];
		}

		$domainTypeMap = self::getDomainTypeMap();

		$whitelist = array_map( static function ( $domain ) use ( $domainTypeMap ) {
			if ( isset( self::KNOWN_DOMAIN_PATTERNS[$domain] ) ) {
				return self::KNOWN_DOMAIN_PATTERNS[$domain];
			}
			if ( isset( $domainTypeMap[$domain] ) ) {
				$type = $domainTypeMap[$domain];
				if ( isset( self::TYPE_PATTERNS[$type] ) ) {
					return sprintf( self::TYPE_PATTERNS[$type], preg_quote( $domain ) );
				}
			}
			// Generic fallback: require at least two path segments (e.g. owner/project)
			$domain = preg_quote( $domain );
			return "^https?:\/\/(www\.)?$domain\/[^\/]+\/[^\/]+";
		}, $whitelist );

		$bitbucketPatterns = [ '^https?:\/\/(www\.)?bitbucket\.org\/projects\/[^\/]+\/repos\/[^\/]+' ];
		foreach ( $domainTypeMap as $domain => $type ) {
			if ( $type === 'bitbucket' ) {
				$bitbucketPatterns[] = sprintf(
					self::TYPE_PATTERNS['bitbucket'],
					preg_quote( $domain )
				);
			}
		}

		return [
			// General regexes to determine if the pasted URL is to be converted to an external content PF
			'whitelist' => $whitelist,
			// Dedicated regex(es) for matching bit bucket URLs (for `#bitbucket` PF)
			'bitbucket' => $bitbucketPatterns
		];
	}

	/**
	 * Parses bsgExternalContentDomainTypes config entries
	 * from "domain:type" format into an associative array.
	 *
	 * @return array<string, string>
	 */
	private static function getDomainTypeMap(): array {
		$entries = $GLOBALS['bsgExternalContentDomainTypes'] ?? [];
		$map = [];
		foreach ( $entries as $entry ) {
			$parts = explode( ':', $entry, 2 );
			if ( count( $parts ) === 2 ) {
				$map[trim( $parts[0] )] = trim( $parts[1] );
			}
		}
		return $map;
	}
}
