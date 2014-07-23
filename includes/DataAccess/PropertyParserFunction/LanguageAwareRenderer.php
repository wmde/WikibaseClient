<?php

namespace Wikibase\DataAccess\PropertyParserFunction;

use InvalidArgumentException;
use Language;
use Status;
use Wikibase\Client\WikibaseClient;
use Wikibase\DataModel\Claim\Claims;
use Wikibase\DataModel\Entity\Entity;
use Wikibase\DataModel\Entity\EntityId;
use Wikibase\DataModel\Entity\EntityIdParsingException;
use Wikibase\DataModel\Entity\PropertyId;
use Wikibase\Lib\PropertyLabelNotResolvedException;
use Wikibase\Lib\SnakFormatter;
use Wikibase\Lib\Store\EntityLookup;
use Wikibase\PropertyLabelResolver;

/**
 * Renderer of the {{#property}} parser function.
 *
 * @fixme see what code can be shared with Lua handling code.
 *
 * @since 0.5
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 * @author Jeroen De Dauw < jeroendedauw@gmail.com >
 * @author Daniel Kinzler
 * @author Liangent < liangent@gmail.com >
 */
class LanguageAwareRenderer implements Renderer {

	private $language;
	private $snaksFinder;
	private $snakFormatter;

	public function __construct(
		Language $language,
		SnaksFinder $snaksFinder,
		SnakFormatter $snakFormatter
	) {
		$this->language = $language;
		$this->snaksFinder = $snaksFinder;
		$this->snakFormatter = $snakFormatter;
	}

	/**
	 * @param EntityId $entityId
	 * @param string $propertyLabel property label or ID (pXXX)
	 *
	 * @return string
	 */
	public function render( EntityId $entityId, $propertyLabel ) {
		try {
			$status = $this->getStatus( $entityId, $propertyLabel );
		} catch ( PropertyLabelNotResolvedException $ex ) {
			$status = $this->getStatusForException( $propertyLabel, $ex->getMessage() );
		} catch ( InvalidArgumentException $ex ) {
			$status = $this->getStatusForException( $propertyLabel, $ex->getMessage() );
		}

		if ( !$status->isGood() ) {
			$error = $status->getMessage()->inLanguage( $this->language )->text();
			return '<p class="error wikibase-error">' . $error . '</p>';
		}

		$text = $status->getValue();
		return $text;
	}

	/**
	 * @param string $propertyLabel
	 * @param string $message
	 *
	 * @return Status
	 */
	private function getStatusForException( $propertyLabel, $message ) {
		return Status::newFatal(
			'wikibase-property-render-error',
			$propertyLabel,
			$message
		);
	}

	/**
	 * @param Snak[] $snaks
	 *
	 * @return string wikitext
	 */
	private function formatSnaks( array $snaks ) {
		$formattedValues = array();

		foreach ( $snaks as $snak ) {
			$formattedValues[] = $this->snakFormatter->formatSnak( $snak );
		}

		return $this->language->commaList( $formattedValues );
	}

	/**
	 * @param EntityId $entityId
	 * @param string $propertyLabel
	 *
	 * @return Status a status object wrapping a wikitext string
	 */
	private function getStatus( EntityId $entityId, $propertyLabel ) {
		wfProfileIn( __METHOD__ );

		$snaks = $this->snaksFinder->findSnaks(
			$entityId,
			$propertyLabel,
			$this->language->getCode()
		);

		if ( !$snaks ) {
			return Status::newGood( '' );
		}

		$text = $this->formatSnaks( $snaks, $propertyLabel );
		$status = Status::newGood( $text );

		wfProfileOut( __METHOD__ );
		return $status;
	}

}
