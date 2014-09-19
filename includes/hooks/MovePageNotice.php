<?php

namespace Wikibase\Client;

use Html;
use Title;
use Wikibase\DataModel\SiteLink;
use Wikibase\Lib\Store\SiteLinkLookup;

/**
 * Adds a notice about the Wikibase Item belonging to the current page
 * after a move (in case there's one).
 *
 * @since 0.4
 *
 * @licence GNU GPL v2+
 * @author Marius Hoch < hoo@online.de >
 */
final class MovePageNotice {

	/**
	 * @var SiteLinkLookup
	 */
	private $siteLinkLookup;

	/**
	 * @var string
	 */
	private $siteId;

	/**
	 * @var RepoLinker
	 */
	private $repoLinker;

	/**
	 * @param SiteLinkLookup $siteLinkLookup
	 * @param string $siteId Global id of the client wiki
	 * @param RepoLinker $repoLinker
	 */
	public function __construct( SiteLinkLookup $siteLinkLookup, $siteId, RepoLinker $repoLinker ) {
		$this->siteLinkLookup = $siteLinkLookup;
		$this->siteId = $siteId;
		$this->repoLinker = $repoLinker;
	}

	/**
	 * Create a repo link directly to the item.
	 * We can't use Special:ItemByTitle here as the item might have already been updated.
	 *
	 * @param Title $title
	 *
	 * @return string|null
	 */
	private function getItemUrl( Title $title ) {
		$entityId = $this->siteLinkLookup->getEntityIdForSiteLink(
			new SiteLink(
				$this->siteId,
				$title->getFullText()
			)
		);

		if ( !$entityId ) {
			return null;
		}

		return $this->repoLinker->getEntityUrl( $entityId );
	}

	/**
	 * Append the appropriate content to the page
	 *
	 * @param Title $oldTitle Title of the page before the move
	 * @param Title $newTitle Title of the page after the move
	 *
	 * @return string|null
	 */
	public function getPageMoveNoticeHtml( Title $oldTitle, Title $newTitle ) {
		$itemLink = $this->getItemUrl( $oldTitle );

		if ( !$itemLink ) {
			return null;
		}

		$msg = $this->getPageMoveMessage( $newTitle );

		$html = Html::rawElement(
			'div',
			array(
				'id' => 'wbc-after-page-move',
				'class' => 'plainlinks'
			),
			wfMessage( $msg, $itemLink )->parse()
		);

		return $html;
	}

	private function getPageMoveMessage( Title $newTitle ) {
		if ( isset( $newTitle->wikibasePushedMoveToRepo ) ) {
			// We're going to update the item using the repo job queue \o/
			return 'wikibase-after-page-move-queued';
		}

		// The user has to update the item per hand for some reason
		return 'wikibase-after-page-move';
	}

}
