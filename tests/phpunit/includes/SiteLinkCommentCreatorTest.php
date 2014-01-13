<?php

namespace Wikibase\Test;

use Diff\Diff;
use Diff\DiffOpChange;
use Diff\MapDiff;
use Wikibase\DataModel\Entity\ItemId;
use Wikibase\DataModel\SimpleSiteLink;
use Wikibase\Item;
use Wikibase\ItemChange;
use Wikibase\SiteLinkCommentCreator;

/**
 * @covers Wikibase\SiteLinkCommentCreator
 *
 * @since 0.5
 *
 * @group Wikibase
 * @group WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class SiteLinkCommentCreatorTest extends \PHPUnit_Framework_TestCase {

	/**
	 * @dataProvider getEditCommentProvider
	 */
	public function testGetEditComment( Diff $siteLinkDiff, $action, $comment, $expected ) {
		$commentCreator = new SiteLinkCommentCreator( 'enwiki' );
		$comment = $commentCreator->getEditComment( $siteLinkDiff, $action, $comment );

		$this->assertEquals( $expected, $comment );
	}

	public function getEditCommentProvider() {
		$changes = array();

		$updates = $this->getUpdates();

		foreach( $updates as $update ) {
			$changes[] = array(
				$update[0],
				ItemChange::UPDATE,
				'wikibase-comment-update',
				$update[1]
			);
		}

		$changes[] = array(
			$this->getDeleteDiff(),
			ItemChange::REMOVE,
			'wikibase-comment-remove',
			array( 'message' => 'wikibase-comment-remove' )
		);

		$changes[] = array(
			$this->getRestoreDiff(),
			ItemChange::RESTORE,
			'wikibase-comment-restore',
			array( 'message' => 'wikibase-comment-restore' )
		);

		return $changes;
	}

	protected function getNewItem() {
		$item = Item::newEmpty();
		$item->setId( new ItemId( 'q1' ) );

		return $item;
	}

	protected function getConnectDiff() {
		$item = $this->getNewItem();
		$item2 = $item->copy();
		$item2->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getUnlinkDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$item2 = $item->copy();
		$item2->removeSiteLink( 'enwiki', 'Japan' );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getLinkChangeDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$item2 = $item->copy();
		$item2->addSiteLink( new SimpleSiteLink( 'enwiki', 'Tokyo' ) );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getOldLinkChangeDiff() {
		$diff = new MapDiff( array(
			'enwiki' => new DiffOpChange( 'Japan', 'Tokyo' )
		) );

		return $diff;
	}

	protected function getBadgeChangeDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$item2 = $item->copy();
		$item2->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan', array( new ItemId( 'Q17' ) ) ) );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getAddLinkDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$item2 = $item->copy();
		$item2->addSiteLink( new SimpleSiteLink( 'dewiki', 'Japan' ) );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getAddMultipleLinksDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$item2 = $item->copy();
		$item2->addSiteLink( new SimpleSiteLink( 'dewiki', 'Japan' ) );
		$item2->addSiteLink( new SimpleSiteLink( 'frwiki', 'Japan' ) );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getRemoveLinkDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );
		$item->addSiteLink( new SimpleSiteLink( 'dewiki', 'Japan' ) );

		$item2 = $item->copy();
		$item2->removeSiteLink( 'dewiki', 'Japan' );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getChangeLinkDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );
		$item->addSiteLink( new SimpleSiteLink( 'dewiki', 'Japan' ) );

		$item2 = $item->copy();
		$item2->addSiteLink( new SimpleSiteLink( 'dewiki', 'Tokyo' ) );

		$change = ItemChange::newFromUpdate( ItemChange::UPDATE, $item, $item2 );

		return $change->getSiteLinkDiff();
	}

	protected function getDeleteDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$change = ItemChange::newFromUpdate( ItemChange::REMOVE, $item, null );

		return $change->getSiteLinkDiff();
	}

	protected function getRestoreDiff() {
		$item = $this->getNewItem();
		$item->addSiteLink( new SimpleSiteLink( 'enwiki', 'Japan' ) );

		$change = ItemChange::newFromUpdate( ItemChange::RESTORE, null, $item );

		return $change->getSiteLinkDiff();
	}

	protected function getUpdates() {
		$updates = array();

		$updates[] = array(
			$this->getConnectDiff(),
			array( 'message' => 'wikibase-comment-linked' )
		);

		$updates[] = array(
			$this->getUnlinkDiff(),
			array( 'message' => 'wikibase-comment-unlink' ),
		);

		$updates[] = array(
			$this->getLinkChangeDiff(),
			array(
				'message' => 'wikibase-comment-sitelink-change',
				'sitelink' => array(
					'oldlink' => array(
						'site' => 'enwiki',
						'page' => 'Japan'
					),
					'newlink' => array(
						'site' => 'enwiki',
						'page' => 'Tokyo'
					)
				)
			)
		);

		$updates[] = array(
			$this->getOldLinkChangeDiff(),
			array(
				'message' => 'wikibase-comment-sitelink-change',
				'sitelink' => array(
					'oldlink' => array(
						'site' => 'enwiki',
						'page' => 'Japan'
					),
					'newlink' => array(
						'site' => 'enwiki',
						'page' => 'Tokyo'
					)
				)
			)
		);

		$updates[] = array(
			$this->getBadgeChangeDiff(),
			'wikibase-comment-update',
		);

		$updates[] = array(
			$this->getAddLinkDiff(),
			array(
				'message' => 'wikibase-comment-sitelink-add',
				'sitelink' => array(
					'newlink' => array(
						'site' => 'dewiki',
						'page' => 'Japan'
					)
				)
			)
		);

		$updates[] = array(
			$this->getAddMultipleLinksDiff(),
			array(
				'message' => 'wikibase-comment-update'
			)
		);

		$updates[] = array(
			$this->getRemoveLinkDiff(),
			array(
				'message' => 'wikibase-comment-sitelink-remove',
				'sitelink' => array(
					'oldlink' => array(
						'site' => 'dewiki',
						'page' => 'Japan'
					)
				)
			)
		);

		$updates[] = array(
			$this->getChangeLinkDiff(),
			array(
				'message' => 'wikibase-comment-sitelink-change',
				'sitelink' => array(
					'oldlink' => array(
						'site' => 'dewiki',
						'page' => 'Japan'
					),
					'newlink' => array(
						'site' => 'dewiki',
						'page' => 'Tokyo'
					)
				)
			)
		);

		return $updates;
	}

}
