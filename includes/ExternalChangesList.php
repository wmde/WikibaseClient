<?php

namespace Wikibase;

// TODO: watched pages should be bold in RC
// TODO: Pages which have been changed since you last visited them are shown in bold
// TODO: document

class ExternalChangesList {

	/**
	 * Generates a recent change line
	 *
	 * @since 0.2
	 *
	 * @param \OldChangesList $cl
	 * @param \RecentChange $rc
	 *
	 * @return string
	 */
	public static function changesLine( &$cl, $rc ) {
		$userName = $rc->getAttribute( 'rc_user_text' );

		$params = unserialize( $rc->getAttribute( 'rc_params' ) );
		$entityData = $params['wikibase-repo-change'];

		if ( !is_array( $entityData ) ) {
			wfDebug( 'Wikibase data missing in recent changes.' );
			return false;
		}

		if ( array_key_exists( 'type', $entityData ) ) {
			$parts = explode( '~', $entityData['type'] );
			$changeType = $parts[1];
		} else {
			wfDebug( 'Wikibase change type missing.' );
			return false;
		}

		$entityTitle = self::titleTextFromEntityData( $entityData );

		if ( $entityTitle === false ) {
			wfDebug( 'Invalid entity data in external change.' );
			return false;
		}

		$repoIndex = str_replace( 'api.php', 'index.php', Settings::get( 'repoApi' ) );

		$line = '';

		if ( in_array( $changeType, array( 'remove', 'restore' ) ) ) {
			$deletionLog = ClientUtils::repoLink( 'Special:Log/delete', wfMessage( 'dellogpage' )->text() );
			$line .= wfMessage( 'parentheses' )->rawParams( $deletionLog );
		} else if ( $changeType === 'update' ) {

			// build a diff link from an RC
			$diffParams = array(
				'title' => $entityTitle,
				'curid' => $rc->getAttribute( 'rc_curid' ),
				'diff' => $rc->getAttribute( 'rc_this_oldid' ),
				'oldid' => $rc->getAttribute( 'rc_last_oldid' )
			);

			$diffQuery = wfArrayToCgi( $diffParams );
			$diffUrl = $repoIndex . '?' . $diffQuery;
			$diffLink = self::diffLink(
				$diffUrl,
				wfMessage( 'diff' )->escaped(),
				array(
					'class' => 'plainlinks',
					'tabindex' => $rc->counter
				)
			);

			$historyQuery = wfArrayToCgi( array(
				'title' => $entityTitle,
				'curid' => $rc->getAttribute( 'rc_curid' ),
				'action' => 'history'
			) );
			$historyUrl = $repoIndex . '?' . $historyQuery;

			$historyLink = self::historyLink(
				$historyUrl,
				wfMessage( 'hist' )->escaped(),
				array(
					'class' => 'plainlinks'
				)
			);

			$line .= wfMessage( 'parentheses' )->rawParams(
				$cl->getLanguage()->pipeList( array( $diffLink, $historyLink ) ) )->escaped();
		} else {
			wfDebug( 'Invalid Wikibase change type.' );
			return false;
		}

		$line .= self::changeSeparator();

		$line .= \Linker::link( \Title::newFromText( $rc->getAttribute( 'rc_title' ) ) );

		if ( $changeType === 'update' ) {
			$entityLink = self::entityLink( $entityData );
			if ( $entityLink !== false ) {
				$line .= wfMessage( 'word-separator' )->plain()
				 . wfMessage( 'parentheses' )->rawParams( self::entityLink( $entityData ) )->escaped();
			}
		}

		$cl->insertTimestamp( $line, $rc );

		if ( \User::isIP( $userName ) ) {
			$userlinks = self::userContribsLink( $userName, $userName );
			$userlinks .= wfMessage( 'word-separator' )->plain()
				. wfMessage( 'parentheses' )->rawParams( self::userTalkLink( $userName ) )->escaped();
		} else {
			$userlinks = self::userLink( $userName );
			$usertools = array(
				self::userTalkLink( $userName ),
				self::userContribsLink( $userName )
			);

			$userlinks .= wfMessage( 'word-separator' )->plain()
				. '<span class="mw-usertoollinks">'
				. wfMessage( 'parentheses' )->rawParams( $cl->getLanguage()->pipeList( $usertools ) )->escaped()
				. '</span>';
		}

		$line .= $userlinks;

		if ( array_key_exists( 'comment', $entityData  ) ) {
			$commentText = self::parseComment( $entityData );
		} else {
			$commentText = '';
		}
		$line .= \Linker::commentBlock( $commentText );

		return $line;
	}

	/**
	 * @since 0.2
	 *
	 * @return string
	 */
	protected static function changeSeparator() {
		return ' <span class="mw-changeslist-separator">. .</span> ';
	}

	/**
	 * @since 0.3
	 *
	 * @todo incorporate this logic in the change processing; store the
	 * message key and param in rc_params instead of here
	 *
	 * @param string $comment
	 *
	 * @return string
	 */
	public static function parseComment( $entityData ) {
		$comment = $entityData['comment'];
		$message = '';
		$param = null;

		if ( is_array( $comment ) ) {
			if ( array_key_exists( 'sitelink', $comment ) ) {
				$sitelinks = $comment['sitelink'];
				if ( array_key_exists( 'oldlink', $sitelinks ) && array_key_exists( 'newlink', $sitelinks ) ) {
					$oldLink = self::wikiLink( $sitelinks['oldlink']['lang'], $sitelinks['oldlink']['page'] );
					$newLink = self::wikiLink( $sitelinks['newlink']['lang'], $sitelinks['newlink']['page'] );
					$param = array( $oldLink, $newLink );
				} else if ( array_key_exists( 'oldlink', $sitelinks ) ) {
					$param = self::wikiLink( $sitelinks['oldlink']['lang'], $sitelinks['oldlink']['page'] );
				} else if ( array_key_exists( 'newlink', $sitelinks ) ) {
					$param = self::wikiLink( $sitelinks['newlink']['lang'], $sitelinks['newlink']['page'] );
				}
			}

			if ( $param !== null ) {
				if ( is_array( $param ) ) {
					$message = wfMessage( $comment['message'] )->rawPArams( $param[0], $param[1] )->parse();
				} else {
					$message = wfMessage( $comment['message'] )->rawParams( $param )->parse();
				}
			} else {
				$message = wfMessage( $comment['message'] )->text();
			}
		}

		return $message;
	}

	/**
	 * @since 0.3
	 *
	 * @param string $siteLang
	 * @param string $page
	 *
	 * @return string
	 */
	protected static function wikiLink( $siteLang, $page ) {
		return "[[:$siteLang:$page|$siteLang:$page]]";
	}

	/**
	 * @since 0.2
	 *
	 * @param string $url
	 * @param string $text
	 * @param array $attribs
	 *
	 * @return string
	 */
	protected static function diffLink( $url, $text, $attribs = array() ) {
		// build a diff link from an RC
		$attribs['href'] = $url;
		return \Html::rawElement( 'a', $attribs, $text );
	}

	/**
	 * @since 0.2
	 *
	 * @param string $url
	 * @param string $text
	 * @param array $attribs
	 *
	 * @return string
	 */
	protected static function historyLink( $url, $text, $attribs = array() ) {
		$attribs['href'] = $url;
		return \Html::rawElement( 'a', $attribs, $text );
	}

	/**
	 * @since 0.2
	 *
	 * @param string $userName
	 *
	 * @return string
	 */
	protected static function userLink( $userName ) {
		$link = "User:$userName";
		$attribs = array(
			 'class' => 'mw-userlink'
		);
		return ClientUtils::repoLink( $link, $userName, $attribs );
	}

	/**
	 * @since 0.2
	 *
	 * @param string $userName
	 * @param string $text
	 *
	 * @return string
	 */
	protected static function userContribsLink( $userName, $text = null ) {
		$link = "Special:Contributions/$userName";
		if ( $text === null ) {
			$text = wfMessage( 'contribslink' );
		}
		return ClientUtils::repoLink( $link, $text );
	}

	/**
	 * @since 0.2
	 *
	 * @param string $userName
	 *
	 * @return string
	 */
	protected static function userTalkLink( $userName ) {
		$link = "User_talk:$userName";
		$text = wfMessage( 'talkpagelinktext' )->escaped();
		return ClientUtils::repoLink( $link, $text );
	}

	/**
	 * @since 0.2
	 *
	 * @param array $entityData
	 *
	 * @return string
	 */
	protected static function entityLink( $entityData ) {
		$entityText = self::titleTextFromEntityData( $entityData );
		$entityId = self::titleTextFromEntityData( $entityData, false );

		if ( $entityText === false ) {
			return false;
		}

		return ClientUtils::repoLink( $entityText, $entityId, array( 'class' => 'wb-entity-link' ) );
	}

	/**
	 * TODO: returning a string as namespace like this is odd.
	 * Returning the namespace ID would make more sense.
	 * If the result of this is not handled to a Title object
	 * we miss out on proper localization and stuff.
	 *
	 * @since 0.2
	 *
	 * @param array $entityData
	 *
	 * @return string
	 */
	protected static function getNamespace( $entityData ) {
		$nsList = Settings::get( 'repoNamespaces' );
		$ns = null;

		switch( $entityData['entity_type'] ) {
			case 'wikibase-item':
				$ns = $nsList['wikibase-item'];
				break;
			case 'wikibase-property':
				$ns = $nsList['wikibase-property'];
				break;
			default:
				// invalid entity type
				// todo: query data type
				return false;
		}
		if ( ! empty( $ns ) ) {
			$ns = $ns . ':';
		}
		return $ns;
	}

	/**
	 * @since 0.2
	 *
	 * @param array $entityData
	 * @param bool $includeNamespace include namespace in title, such as Item:Q1
	 *
	 * @return string|bool
	 */
	protected static function titleTextFromEntityData( $entityData, $includeNamespace = true ) {
		if ( isset( $entityData['object_id'] ) ) {
			$entityId = $entityData['object_id'];

			if ( ctype_digit( $entityId ) || is_numeric( $entityId ) ) {
				// FIXME: this is evil; we seem to have lost all encapsulation at this point,
				// so some refactoring is needed to have sane access to the info here.
				$entityType = explode( '-', $entityData['entity_type'], 2 );

				$entityId = new EntityId( $entityType, (int)$entityId );
			}
			else {
				$entityId = EntityId::newFromPrefixedId( $entityId );
			}

			// TODO: ideally the uppercasing would be handled by a Title object
			$titleText = strtoupper( $entityId->getPrefixedId() );

			if ( $includeNamespace ) {
				$ns = self::getNamespace( $entityData );
				$titleText = $ns . $titleText;
			}

			return $titleText;
		}

		return false;
	}
}
