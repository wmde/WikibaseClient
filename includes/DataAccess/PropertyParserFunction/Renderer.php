<?php

namespace Wikibase\DataAccess\PropertyParserFunction;

use Wikibase\DataModel\Entity\EntityId;

/**
 * Renderer for the {{#property}} parser function.
 *
 * @since 0.5
 *
 * @licence GNU GPL v2+
 * @author Katie Filbert < aude.wiki@gmail.com >
 */
interface Renderer {

	/**
	 * @param EntityId $entityId
	 * @param string $propertyLabelOrId property label or ID (pXXX)
	 *
	 * @return string
	 */
	public function render( EntityId $entityId, $propertyLabelOrId );

}
