<?php
namespace Wikibase;

use Diff\Diff;
use Diff\DiffOp;

/**
 * Creates an array structure with comment information for storing
 * in the rc_params column of the RecentChange table, for use in
 * generating recent change comments for wikibase changes.
 *
 * @since 0.5
 *
 * @file
 * @ingroup WikibaseClient
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
class SiteLinkCommentCreator {

	/**
	 * @var string
	 */
	protected $siteId;

	/**
	 * @param string $siteId
	 */
	public function __construct( $siteId ) {
		$this->siteId = $siteId;
	}

	/**
	 * Returns the comment to use in RC and history entries for this change.
	 * This may be a complex structure. It will be interpreted by
	 *
	 * @since 0.5
	 *
	 * @param Diff $siteLinkDiff
	 * @param string $action
	 * @param string $comment
	 *
	 * @return array|string
	 */
	public function getEditComment( Diff $siteLinkDiff, $action, $comment ) {
		if ( $siteLinkDiff !== null && !$siteLinkDiff->isEmpty() ) {
			$siteLinkComment = $this->getSiteLinkComment( $action, $siteLinkDiff );

			if ( !empty( $siteLinkComment ) ) {
				return $siteLinkComment;
			}
		}

		return $comment;
	}

	/**
	 * Returns an array structure suitable for building an edit summary for the respective
	 * change to site links.
	 *
	 * @param string $action Change action
	 * @param Diff $siteLinkDiff The change's site link diff
	 *
	 * @return array|null
	 */
	protected function getSiteLinkComment( $action, Diff $siteLinkDiff ) {
		if ( $siteLinkDiff->isEmpty() ) {
			return null;
		}

		//TODO: Implement comments specific to the affected page.
		//       Different pages may be affected in different ways by the same change.
		//       Also, merged changes may affect the same page in multiple ways.
		$diffOps = $siteLinkDiff->getOperations();
		$siteId = $this->siteId;

		// change involved site link to client wiki
		if ( array_key_exists( $siteId, $diffOps ) ) {

			// $siteLinkDiff changed from containing atomic diffs to
			// containing map diffs. For B/C, handle both cases.
			$diffOp = $diffOps[$siteId];

			if ( $diffOp instanceof Diff ) {
				if ( array_key_exists( 'name', $diffOp ) ) {
					$diffOp = $diffOp['name'];
				} else {
					// change to badges only
					return null;
				}
			}

			$params = $this->getSiteLinkAddRemoveParams( $diffOp, $action, $siteId );
		} else {
			$diffOpCount = count( $diffOps );
			if ( $diffOpCount === 1 ) {
				$params = $this->getSiteLinkChangeParams( $diffOps );
			} else {
				// @todo report how many changes
				$params = array(
					'message' => 'wikibase-comment-update'
				);
			}
		}

		return $params;
	}

	/**
	 * @param Diff[] $diffs
	 *
	 * @return array|null
	 */
	protected function getSiteLinkChangeParams( array $diffs ) {
		$messagePrefix = 'wikibase-comment-sitelink-';
		/* Messages used:
			wikibase-comment-sitelink-add wikibase-comment-sitelink-change wikibase-comment-sitelink-remove
		*/
		$params['message'] = $messagePrefix . 'change';

		foreach( $diffs as $siteId => $diff ) {
			// backwards compatibility in case of old, pre-badges changes in the queue
			$diffOp = ( ( $diff instanceof Diff ) && array_key_exists( 'name', $diff ) ) ? $diff['name'] : $diff;
			$args = $this->getChangeParamsForDiffOp( $diffOp, $siteId, $messagePrefix );

			if ( empty( $args ) ) {
				return null;
			}

			$params = array_merge(
				$params,
				$args
			);

			// todo handle if there are multiple diffOps here
			break;
		}

		return $params;
	}

	/**
	 * @param DiffOp $diffOp
	 * @param string $siteId
	 * @param string $messagePrefix
	 *
	 * return array|null
	 */
	protected function getChangeParamsForDiffOp( DiffOp $diffOp, $siteId, $messagePrefix ) {
		$params = array();

		if ( $diffOp instanceof \Diff\DiffOpAdd ) {
			$params['message'] = $messagePrefix . 'add';
			$params['sitelink'] = array(
				'newlink' => array(
					'site' => $siteId,
					'page' => $diffOp->getNewValue()
				)
			);
		} elseif ( $diffOp instanceof \Diff\DiffOpRemove ) {
			$params['message'] = $messagePrefix . 'remove';
			$params['sitelink'] = array(
				'oldlink' => array(
					'site' => $siteId,
					'page' => $diffOp->getOldValue()
				)
			);
		} elseif ( $diffOp instanceof \Diff\DiffOpChange ) {
			$params['sitelink'] = array(
				'oldlink' => array(
					'site' => $siteId,
					'page' => $diffOp->getOldValue()
				),
				'newlink' => array(
					'site' => $siteId,
					'page' => $diffOp->getNewValue()
				)
			);
		} else {
			// whatever
			$params = null;
		}

		return $params;
	}

	/**
	 * @param DiffOp $diffOp
	 * @param string $action
	 * @param string $siteId
	 *
	 * @return array|null
	 */
	protected function getSiteLinkAddRemoveParams( DiffOp $diffOp, $action, $siteId ) {
		$params = array();

		if ( in_array( $action, array( 'remove', 'restore' ) ) ) {
			// Messages: wikibase-comment-remove, wikibase-comment-restore
			$params['message'] = 'wikibase-comment-' . $action;
		} elseif ( $diffOp instanceof \Diff\DiffOpAdd ) {
			$params['message'] = 'wikibase-comment-linked';
		} elseif ( $diffOp instanceof \Diff\DiffOpRemove ) {
			$params['message'] = 'wikibase-comment-unlink';
		} elseif ( $diffOp instanceof \Diff\DiffOpChange ) {
			$params['message'] = 'wikibase-comment-sitelink-change';

			// FIXME: this code appears to be doing something incorrect as "best effort"
			// rather than allowing for proper error handling
			$params['sitelink'] = array(
				'oldlink' => array(
					'site' => $siteId,
					'page' => $diffOp->getOldValue()
				),
				'newlink' => array(
					'site' => $siteId,
					'page' => $diffOp->getNewValue()
				)
			);
		} else {
			// whatever
			$params = null;
		}

		return $params;
	}
}
