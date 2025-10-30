## yii2-mongodb-filemodels

Module/extension for handling model attachments stored in MongoDB GridFS within Yii2 applications. It provides helpers and UI pieces to associate, upload, and retrieve files bound to your domain models while keeping the binary content in GridFS. Provided by Marco Curatitoli at HalService. Mantained by Pietro Bardone at Anteo Impresa Sociale.

### Features
- Associate one or multiple files with your models
- Store binary data in MongoDB GridFS
- Yii2-friendly helpers, widgets, and bootstrap integration

### Requirements
- PHP compatible with your Yii2 version
- Yii2 framework (`yiisoft/yii2`)
- MongoDB server with GridFS
- The following packages are required by this extension:
  - `yiisoft/yii2-bootstrap`
  - `anteo/yii2-fontawesome`
  - `yii2tech/embedded`

### Installation
Install via Composer:

```bash
composer require anteo/yii2-mongodb-filemodels
```

This package registers a bootstrap class (`anteo\\mongodb\\filemodels\\Bootstrap`) via Composer, so no manual module registration is typically required.

### Configuration
Ensure your Yii2 application is configured with a MongoDB connection. A minimal example:

```php
// config/web.php (or common config)
return [
    'components' => [
        'mongodb' => [
            'class' => '\\yii\\mongodb\\Connection',
            'dsn' => 'mongodb://localhost:27017/your_database_name',
        ],
    ],
];
```

GridFS buckets will be created/used in the same database configured above. Make sure your MongoDB user has appropriate read/write permissions.

### Basic Usage
While usage can vary by project, the typical workflow is:
1. Present a file upload UI bound to a model instance.
2. On save, store the file’s binary content in GridFS and persist the reference on the model (e.g., an ObjectId or reference field).
3. When rendering, use provided helpers/widgets to list, download, or preview the associated files.

Given this is an extension, consult the code (namespaces under `anteo\\mongodb\\filemodels`) for available helpers, widgets, and behaviors you can attach to your models.

### Notes
- Large files should be streamed to avoid high memory usage.
- Consider setting appropriate GridFS bucket names if you need isolation per model type.
- Apply access control to download endpoints to protect private files.

### License
BSD 3-Clause. See the `license` field in `composer.json`.

### Author
- Maintainer: p3pp01 (`pietro.bardone@gruppoanteo.it`) — `https://anteocoop.it`


