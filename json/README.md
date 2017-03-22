# Web service routes and static JSON examples 

This directory contains example files for 
- route definitions for the web service
- JSON files schema per entity type

They can also be used for static file import or debugging purpose.

## Routes

### Select entities

#### Unique

/select/unique/{entity_type}/{entity_id}/{optional_language}

#### Multiple

/select/multiple/{entity_type}/{limit}/{optional_language}

### Result of an update after a select entities query

/result/{entity_type}/{entity_id}/{log_id}

### Status and error handling

Contains a description of the status and error codes.

/status

## Error handling

Each JSON file will begin with a errors header, that can contain an array of errors.

```
"errors": [
  {
    "status": "501",
    "source": { "pointer": "/data/update/attributes/id" },
    "title":  "Entity does not exists",
    "detail": "The requested entity has probably been deleted."
  }
],
```

If no errors are thrown, the header will be empty
 
```
"errors": [
],
```

## Standards

- The JSON schema is inspired by jsonapi.org
- Language codes are based on [ISO-639]( https://en.wikipedia.org/wiki/ISO_639), we will follow the ISO 639-1 where language code 
are defined with two letters (English = 'en') and no language is defined with '-'.
