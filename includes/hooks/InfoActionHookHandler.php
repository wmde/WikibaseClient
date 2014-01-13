<?php

namespace Wikibase\Client\Hooks;

use IContextSource;
use Title;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\SimpleSiteLink;
use Wikibase\NamespaceChecker;
use Wikibase\RepoLinker;
use Wikibase\SiteLinkLookup;

/**
 * @since 0.5
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class InfoActionHookHandler {

	/**
	 * @var NamespaceChecker
	 */
	protected $namespaceChecker;

	/**
	 * @var RepoLinker
	 */
	protected $repoLinker;

	/**
	 * @var SiteLinkLookup
	 */
	protected $siteLinkLookup;

	/**
	 * @var string
	 */
	protected $siteId;

	public function __construct( NamespaceChecker $namespaceChecker, RepoLinker $repoLinker,
		SiteLinkLookup $siteLinkLookup, $siteId ) {
		$this->namespaceChecker = $namespaceChecker;
		$this->repoLinker = $repoLinker;
		$this->siteLinkLookup = $siteLinkLookup;
		$this->siteId = $siteId;
	}

	/**
	 * @param IContextSource $context
	 * @param array $pageInfo
	 *
	 * @return array
	 */
	public function handle( IContextSource $context, array $pageInfo ) {
		// Check if wikibase namespace is enabled
		$title = $context->getTitle();

		if ( $this->namespaceChecker->isWikibaseEnabled( $title->getNamespace() ) && $title->exists() ) {
			$pageInfo['header-basic'][] = $this->getPageInfoRow( $context, $title );
		}

		return $pageInfo;
	}

	/**
	 * @param IContextSource $context
	 * @param Title $title
	 *
	 * @return array
	 */
	private function getPageInfoRow( IContextSource $context, Title $title ) {
		$entityId = $this->siteLinkLookup->getEntityIdForSiteLink(
			new SimpleSiteLink( $this->siteId, $title->getFullText() )
		);

		$row = $entityId ? $this->getItemPageInfo( $context, $entityId )
			: $this->getUnconnectedItemPageInfo( $context );

		return $row;
	}

	/**
	 * Creating a Repo link with Item ID as anchor text
	 *
	 * @param IContextSource $context
	 * @param ItemId $itemId
	 *
	 * @return array
	 */
	private function getItemPageInfo( IContextSource $context, ItemId $itemId ) {
		$itemLink = $this->repoLinker->buildEntityLink(
			$itemId,
			array( 'external' )
		);

		return array(
			$context->msg( 'wikibase-pageinfo-entity-id' ),
			$itemLink
		);
	}

	/**
	 * @param IContextSource $context
	 *
	 * @return array
	 */
	private function getUnconnectedItemPageInfo( IContextSource $context ) {
		return array(
			$context->msg( 'wikibase-pageinfo-entity-id' ),
			$context->msg( 'wikibase-pageinfo-entity-id-none' )
		);
	}

}
