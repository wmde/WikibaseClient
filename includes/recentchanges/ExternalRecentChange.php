<?php

namespace Wikibase;

class ExternalRecentChange {

	public $mAttribs = array();

	/**
	 * Builds a new external recent change object from attribute array
	 *
	 * @since 0.3
	 *
	 * @param array $attribs
	 * @param \Title $title
	 *
	 * @return ExternalRecentChange
	 */
	public static function newFromAttribs( $attribs, $title ) {
		$rc = new ExternalRecentChange;
		$rc->buildAttributes( $attribs, $title );
		return $rc;
	}

	/**
	 * Builds the attribute array for saving into recentchanges table
	 *
	 * @since 0.3
	 *
	 * @param array @attribs
	 * @param \Title $title
	 */
	private function buildAttributes( $attribs, $title ) {
		$metadata = $attribs['wikibase-repo-change'];

		$isBot = false;
		if ( array_key_exists( 'bot', $metadata ) ) {
			$isBot = $metadata['bot'];
		}

		// compatibility
		if ( array_key_exists( 'user_text', $metadata ) ) {
			$userText = $metadata['user_text'];
		} else {
			$userText = $metadata['rc_user_text'];
		}

		$time = isset( $metadata['time'] ) ? $metadata['time'] : wfTimestamp( TS_MW );

		$this->mAttribs = array(
			'rc_namespace' => $title->getNamespace(),
			'rc_title' => $title->getDBkey(),
			'rc_user' => 0,
			'rc_user_text' => $userText,
			'rc_type' => RC_EXTERNAL,
			'rc_minor' => true, // for now, always consider these minor
			'rc_bot' => $isBot,
			'rc_patrolled' => true,
			'rc_old_len' => $title->getLength(),
			'rc_new_len' => $title->getLength(),
			'rc_this_oldid' => $title->getLatestRevID(),
			'rc_last_oldid' => $title->getLatestRevID(),
			'rc_params' => serialize( $attribs ),
			'rc_cur_id' => $title->getArticleID(),
			'rc_comment' => '',
			'rc_timestamp' => $time,
			'rc_log_action' => '',
			'rc_source' => 'wb'
		);
	}

	/**
	 * Get a param from wikibase-repo-change array in rc_params
	 *
	 * @since 0.4
	 *
	 * @param string $param metadata array key
	 * @param $rc_params[]
	 *
	 * @return mixed|bool
	 */
	public function getParam( $param, $rc_params ) {
		if ( is_string( $rc_params ) ) {
			$rc_params = unserialize( $rc_params );
		}

		if ( is_array( $rc_params ) && array_key_exists( 'wikibase-repo-change', $rc_params ) ) {
			$metadata = $rc_params['wikibase-repo-change'];
			if ( is_array( $metadata ) && array_key_exists( $param, $metadata ) ) {
				return $metadata[$param];
			}
		}
		return false;
	}

	/**
	 * Checks if a recent change entry already exists in the recentchanges table
	 *
	 * @since 0.4
	 *
	 * @param \DatabaseBase $db
	 *
	 * @throws \MWException
	 *
	 * @return bool
	 */
	public function exists( \DatabaseBase $db = null ) {
		if ( ! is_array( $this->mAttribs ) ) {
			throw new \MWException( 'Recent change attributes are missing.' );
		}

		// because this is used before a write operation, to help ensure
		// data integrity and issues with slave rep lag
		if ( $db === null ) {
			$db = wfGetDB( DB_SLAVE );
		}

		$res = $db->select(
			'recentchanges',
			array( 'rc_id', 'rc_timestamp', 'rc_type', 'rc_params' ),
			array(
				'rc_namespace' => $this->mAttribs['rc_namespace'],
				'rc_title' => $this->mAttribs['rc_title'],
				'rc_timestamp' => $this->mAttribs['rc_timestamp'],
				'rc_type' => RC_EXTERNAL
			),
			__METHOD__
		);

		if ( $res->numRows() == 0 ) {
			return false;
		}

		$changeRevId = self::getParam( 'rev_id', $this->mAttribs['rc_params'] );
		$changeParentId = self::getParam( 'parent_id', $this->mAttribs['rc_params'] );

		foreach ( $res as $rc ) {
			$parent_id = self::getParam( 'parent_id', $rc->rc_params );
			$rev_id = self::getParam( 'rev_id', $rc->rc_params );

			if ( $rev_id === $changeRevId
				&& $parent_id === $changeParentId ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Saves an external recent change
	 *
	 * @since 0.3
	 *
	 * @return bool
	 */
	public function save() {
		if ( !isset( $this->mAttribs ) || !is_array( $this->mAttribs ) ) {
			wfDebugLog( __CLASS__, __FUNCTION__ . ': mAttribs in ExternalRecentChange is missing.' );
			return false;
		}

		$dbw = wfGetDB( DB_MASTER );
		if ( $this->exists( $dbw ) === false ) {
			$res = $dbw->insert( 'recentchanges', $this->mAttribs, __METHOD__ );
			return $res;
		}

		return false;
	}

}
