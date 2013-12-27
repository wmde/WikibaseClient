<?php

namespace Wikibase\Client\Specials;

use DatabaseBase;
use Html;
use Linker;
use MWException;
use Title;
use Wikibase\Lib\Specials\SpecialWikibaseQueryPage;
use Wikibase\NamespaceChecker;
use Wikibase\Settings;

/**
 * List client pages that is not connected to repository items.
 *
 * @since 0.4
 * @licence GNU GPL v2+
 * @author John Erling Blad < jeblad@gmail.com >
 */
class SpecialUnconnectedPages extends SpecialWikibaseQueryPage {

	/**
	 * The title as a string to start search at
	 *
	 * @since 0.4
	 *
	 * @var string
	 */
	protected $startPage = '';

	/**
	 * The startPage as a title to start search at
	 *
	 * @since 0.4
	 *
	 * @var Title
	 */
	protected $startTitle = null;

	/**
	 * The namespaceChecker
	 *
	 * @since 0.4
	 *
	 * @var NamespaceChecker
	 */
	protected $namespaceChecker = null;

	/**
	 * If the search should only include pages with iw-links
	 *
	 * @since 0.4
	 *
	 * @var string
	 */
	protected $iwData = '';

	public function __construct() {
		parent::__construct( 'UnconnectedPages' );

	}

	/**
	 * @see SpecialWikibasePage::execute
	 *
	 * @since 0.4
	 */
	public function execute( $subPage ) {
		if ( !parent::execute( $subPage ) ) {
			return false;
		}

		$output = $this->getOutput();
		$request = $this->getRequest();

		$this->startPage = $request->getText( 'page', $subPage );
		if ( $this->startPage !== '' ) {
			$this->startTitle = \Title::newFromText( $this->startPage );
		}

		$this->iwData = $request->getText( 'iwdata', '' );

		$out = '';

		if ( $this->startPage !== '' && $this->startTitle === null ) {
			$out .= Html::element( 'div', array(), $this->msg( 'wikibase-unconnectedpages-page-warning' )->text() );
		}

		$out .= Html::openElement(
			'form',
			array(
				'action' => $this->getPageTitle()->getLocalURL(),
				'name' => 'unconnectedpages',
				'id' => 'wbc-unconnectedpages-form'
			)
		)
		. Html::openElement( 'fieldset' )
		. Html::element( 'legend', array(), $this->msg( 'wikibase-unconnectedpages-legend' )->text() )
		. Html::openElement( 'p' )
		. Html::element( 'label', array( 'for' => 'language' ), $this->msg( 'wikibase-unconnectedpages-page' )->text() )
		. ' '
		. Html::input(
			'page',
			$this->startPage,
			'text',
			array(
				'id' => 'page'
			)
		)
		. Html::input(
			'submit',
			$this->msg( 'wikibase-unconnectedpages-submit' )->text(),
			'submit',
			array(
				'id' => 'wbc-unconnectedpages-submit',
				'class' => 'wbc-input-button'
			)
		)
		. ' '
		. Html::input(
			'iwdata',
			'only',
			'checkbox',
			array(
				'id' => 'wbc-unconnectedpages-iwdata',
				'class' => 'wbc-input-button',
				$this->iwData === 'only' ? 'checked' : 'unchecked' => ''
			)
		)
		. ' '
		. Html::element( 'label', array( 'for' => 'wbc-unconnectedpages-iwdata' ), $this->msg( 'wikibase-unconnectedpages-iwdata-label' )->text() )
		. Html::closeElement( 'p' )
		. Html::closeElement( 'fieldset' )
		. Html::closeElement( 'form' );
		$output->addHTML( $out );

		$query = array();
		if ( $this->iwData === 'only' ) {
			$query['iwdata'] = $this->iwData;
		}

		$this->showQuery( $query );
	}

	/**
	 * Set the NamespaceChecker
	 *
	 * @since 0.4
	 *
	 * @param NamespaceChecker $namespaceChecker
	 */
	public function setNamespaceChecker( NamespaceChecker $namespaceChecker ) {
		$this->namespaceChecker = $namespaceChecker;
	}

	/**
	 * Get the NamespaceChecker
	 *
	 * @since 0.4
	 *
	 * @return \Wikibase\NamespaceChecker
	 */
	public function getNamespaceChecker() {
		if ( $this->namespaceChecker === null ) {
			$this->namespaceChecker = new NamespaceChecker(
				Settings::get( 'excludeNamespaces' ),
				Settings::get( 'namespaces' )
			);
		}
		return $this->namespaceChecker;
	}

	/**
	 * Build conditionals for namespace
	 *
	 * @since 0.4
	 *
	 * @param DatabaseBase $dbr
	 * @param Title $title
	 * @param NamespaceChecker $checker
	 * @return string[]
	 */
	public function buildConditionals( $dbr, Title $title = null, $checker = null ) {
		if ( !isset( $title ) ) {
			$title = $this->startTitle;
		}
		if ( !isset( $checker ) ) {
			$checker = $this->getNamespaceChecker();
		}
		if ( $title !== null ) {
			$conds[] = 'page_title >= ' . $dbr->addQuotes( $title->getDBkey() );
			$conds[] = 'page_namespace = ' . $title->getNamespace();
		}
		$conds[] = 'page_namespace IN (' . implode(',', $checker->getWikibaseNamespaces() ) . ')';
		return $conds;
	}

	/**
	 * @see SpecialWikibaseQueryPage::getResult
	 *
	 * @since 0.4
	 */
	public function getResult( $offset = 0, $limit = 0 ) {

		$dbr = wfGetDB( DB_SLAVE );

		$conds = $this->buildConditionals( $dbr );
		$conds["page_is_redirect"] = '0';
		$conds[] = "pp_propname IS NULL";
		if ( $this->iwData === 'only' ) {
			$conds[] = 'll_from IS NOT NULL';
		}

		$rows = $dbr->select(
			array(
				'page',
				'page_props',
				'langlinks'
			),
			array(
				'page_namespace',
				'page_title',
				'page_id',
				'page_len',
				'page_is_redirect',
				'page_num_iwlinks' => 'count(ll_from)'
			),
			$conds,
			__METHOD__,
			array(
				'OFFSET' => $offset,
				'LIMIT' => $limit,
				'GROUP BY' => 'page_namespace, page_title',
				'ORDER BY' => 'page_namespace, page_title',
				'USE INDEX' => array( 'page' => 'name_title' )
			),
			array(
				'page_props' => array( 'LEFT JOIN', array( 'page_id = pp_page', "pp_propname = 'wikibase_item'" ) ),
				'langlinks' => array( 'LEFT JOIN', 'll_from = page_id' )
			)
		);

		$entries = array();
		foreach ( $rows as $row ) {
			$title = \Title::newFromRow( $row );
			$numIWLinks = $row->page_num_iwlinks;
			$entries[] = array( 'title' => $title, 'num' => $numIWLinks);
		}
		return $entries;
	}

	/**
	 * @see SpecialWikibaseQueryPage::formatRow
	 *
	 * @since 0.4
	 */
	protected function formatRow( $entry ) {
		try {
			$out = Linker::linkKnown( $entry['title'] );
			if ( $entry['num'] > 0 ) {
				$out .= ' ' . $this->msg( 'wikibase-unconnectedpages-format-row' )
					->numParams( $entry['num'] )->text();
			}
			return $out;
		} catch ( MWException $e ) {
			wfWarn( "Error formatting result row: " . $e->getMessage() );
			return false;
		}
	}

	/**
	 * @see SpecialWikibaseQueryPage::getTitleForNavigation
	 *
	 * @since 0.4
	 */
	protected function getTitleForNavigation() {
		return $this->getPageTitle( $this->startPage );
	}

}
