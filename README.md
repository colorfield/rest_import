# Drupal 8 REST import

The focus of this module is to allow import and conversion of REST resources into Drupal 8 entities.
Currently, only JSON is supported, further development can include XML and HAL.

The scope of the operations are create, update, delete.

It is based on a mapping defined in in the EntityMapper class that 
- maps a source entity (e.g. "news") and a target
Drupal 8 entity type and entity id, also known as "bundle" (e.g. "node" and "article").
- defines a converter class, each external entity can be converted into a Drupal entity by defining a class that implements 
the EntityConverterInterface.

The proposed web service definition is based on [jsonapi.org](http://jsonapi.org/).
Further explanations can be found in the json directory.

Behind the scene, the import can be triggered by a cron or manually. Each request to import resources 
are then enqueued via the Queue API.