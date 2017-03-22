# REST import

*Currently under development, issues that needs to be fixed prior to publishing on Drupal.org are marked as 8.1.x-dev*

The focus of this module is to allow import and conversion of REST resources into Drupal 8 entities. 
It provides the tools for easily import contents from other applications via a web service.
The approach is to use inheritance of converters instead of configuration via YAML (this is the main difference with Migrate style).

Currently, only JSON is supported, further development can include XML and HAL.

The scope of the operations are create, update, translate and delete.

It is based on a mapping defined in in the EntityMapper class that 
- maps a source entity (e.g. "news") and a target
Drupal 8 entity type and entity id, also known as "bundle" (e.g. "node" and "article").
- defines a converter class, each external entity can be converted into a Drupal entity by defining a class that implements 
the EntityConverterInterface.

## JSON model definition

The proposed web service definition is based on [jsonapi.org](http://jsonapi.org/).
Further explanations can be found in the json directory.

The import can be triggered by a cron or manually. Each request to import resources 
are then enqueued via the Queue API.

## Installation

1. Enable the rest_import module
2. Configure the web service (/admin/config/rest_import/web_service), see config below or get the active one from configuration management.
3. Make sure that the web service is reachable.

## One time import

Use manual operations

1. Populate the queue by entity, respect the sequence: /admin/rest_import/operations
2. Click on Process queue or process the queue using drush `drush queue-run manual_entity_process`

## Regular import

This handles regular import based on update of each entity, each time. 
It triggers a sequence of entity types for each language then enqueue the results and execute the queue via a cron worker.

## Diff import

This import method relies on bi-directional web service exchange : the web service waits for confirmation of import then provides only the items that were not imported yet.
The action passed for each entity can the be create, update or delete.

It is not currently being implemented.

## Example of web service configuration

- Endpoint: http://my.domain.org/webservice
- Debug endpoint: http://my.local.dev/modules/custom/rest_import/json
- Debug mode: No
- Limit per batch: 0
- Default language: en
- Import user: admin
- Format: JSON
