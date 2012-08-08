-- MySQL version of the database schema for the Wikibase extension.
-- Licence: GNU GPL v2+



-- Cache for entities.
-- This cache is a shared table, so exists only once per master.
CREATE TABLE IF NOT EXISTS /*_*/wbc_entity_cache (
  ec_id                      INT UNSIGNED        NOT NULL PRIMARY KEY AUTO_INCREMENT,

  -- The id of the entity. In itself not unique, but unique per type.
  ec_entity_id               INT unsigned        NOT NULL,

  -- The type of the entity (ie item, property, query)
  ec_entity_type             VARCHAR(20) binary  NOT NULL,

  -- Serialized form of the entity.
  ec_entity_data             BLOB                NOT NULL
) /*$wgDBTableOptions*/;

CREATE UNIQUE INDEX /*i*/wbc_ec_entity_id_type ON /*_*/wbc_entity_cache (ec_entity_id, ec_entity_type);
CREATE INDEX /*i*/wbc_ec_entity_id ON /*_*/wbc_entity_cache (ec_entity_id);
CREATE INDEX /*i*/wbc_ec_entity_type ON /*_*/wbc_entity_cache (ec_entity_type);


-- Links pages with their corresponding items.
-- This is a shared table.
CREATE TABLE IF NOT EXISTS /*_*/wbc_items_per_site (
  ips_item_id                INT unsigned        NOT NULL, -- Id of the item
  ips_site_id                VARBINARY(32)       NOT NULL, -- Site identifier (global)
  ips_site_page              VARCHAR(255)        NOT NULL -- Title of the page
) /*$wgDBTableOptions*/;

CREATE UNIQUE INDEX /*i*/wbc_ips_item_site_page ON /*_*/wbc_items_per_site (ips_site_id, ips_site_page);
CREATE INDEX /*i*/wbc_ips_site_page ON /*_*/wbc_items_per_site (ips_site_page);
CREATE INDEX /*i*/wbc_ips_item_id ON /*_*/wbc_items_per_site (ips_item_id);



-- Table for usage lookups of items.
CREATE TABLE IF NOT EXISTS /*_*/wbc_item_usage (
  -- Foreign key on wbc_entity_cache.ec_entity_id
  iu_item_id                 INT unsigned        NOT NULL,

  -- Foreign key on page.page_id
  iu_page_id                 INT unsigned        NOT NULL
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/wbc_iu_item_id ON /*_*/wbc_item_usage (iu_item_id);
CREATE INDEX /*i*/wbc_iu_page_id ON /*_*/wbc_item_usage (iu_page_id);


-- Table for usage lookups of queries.
CREATE TABLE IF NOT EXISTS /*_*/wbc_query_usage (
  -- Foreign key on wbc_entity_cache.ec_entity_id
  qu_query_id                INT unsigned        NOT NULL,

  -- Foreign key on page.page_id
  qu_page_id                 INT unsigned        NOT NULL
) /*$wgDBTableOptions*/;

CREATE INDEX /*i*/wbc_qu_query_id ON /*_*/wbc_query_usage (qu_query_id);
CREATE INDEX /*i*/wbc_qu_page_id ON /*_*/wbc_query_usage (qu_page_id);