<?php

namespace Wikibase;

use InvalidArgumentException;
use MWNamespace;

/**
 * Checks if a namespace in Wikibase Client shall have wikibase links, etc., based on settings
 *
 * @since 0.4
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 * @author Daniel Kinzler
 */
class NamespaceChecker {

	/**
	 * @var array
	 */
	protected $excludedNamespaces;

	/**
	 * @var array
	 */
	protected $enabledNamespaces;

	/**
	 * @since 0.4
	 *
	 * @param $excluded[]
	 * @param $enabled[] - if empty, setting not in use and all namespaces enabled
	 */
	public function __construct( array $excludedNamespaces, array $enabledNamespaces ) {
		$this->excludedNamespaces = $excludedNamespaces;

		$this->enabledNamespaces = $enabledNamespaces;
	}

	/**
	 * Per the settings, does the namespace have wikibase enabled?
	 * note: excludeNamespaces, if set, overrides namespace (inclusion) settings
	 *
	 * @since 0.4
	 *
	 * @param int $namespace
	 *
	 * @throws InvalidArgumentException
	 * @return boolean
	 */
	public function isWikibaseEnabled( $namespace ) {
		if ( !is_int( $namespace ) ) {
			throw new InvalidArgumentException( '$namespace is must be an integer' );
		}

		if ( $this->isExcluded( $namespace ) ) {
			return false;
		}

		return $this->isEnabled( $namespace );
	}

	/**
	 * Check if the namespace is excluded by settings for having wikibase links, etc.
	 * based on the 'excludeNamespaces' setting.
	 *
	 * @since 0.4
	 *
	 * @param int $namespace
	 *
	 * @return bool
	 */
	protected function isExcluded( $namespace ) {
		return in_array( $namespace, $this->excludedNamespaces );
	}

	/**
	 * Check if namespace is enabled for Wikibase, based on the 'namespaces' setting.
	 *
	 * Note: If no list of enabled namespaces is configured, all namespaces are considered
	 * to be enabled for Wikibase.
	 *
	 * @since 0.4
	 *
	 * @param int $namespace
	 *
	 * @return bool
	 */
	protected function isEnabled( $namespace ) {
		return empty( $this->enabledNamespaces )
			|| in_array( $namespace, $this->enabledNamespaces );
	}

	/**
	 * Get enabled namespaces
	 *
	 * @since 0.4
	 *
	 * @return array|bool
	 */
	public function getEnabledNamespaces() {
		return $this->enabledNamespaces;
	}

	/**
	 * Get excluded namespaces
	 *
	 * @since 0.4
	 *
	 * @return array
	 */
	public function getExcludedNamespaces() {
		return $this->excludedNamespaces;
	}

	/**
	 * Get the namespaces Wikibase is effectively enabled in.
	 *
	 * @since 0.4
	 *
	 * @return array
	 */
	public function getWikibaseNamespaces() {
		$enabled = $this->enabledNamespaces;

		if ( empty( $enabled ) ) {
			$enabled = MWNamespace::getValidNamespaces();
		}

		return array_diff( $enabled, $this->excludedNamespaces );
	}

}
