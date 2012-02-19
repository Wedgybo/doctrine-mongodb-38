<?php

echo get_include_path();

require_once 'Doctrine/Common/ClassLoader.php';

use Doctrine\MongoDB\Connection,
    Doctrine\ODM\MongoDB\Configuration,
    Doctrine\ODM\MongoDB\Mapping\Driver,
    Doctrine\ODM\MongoDB\DocumentManager,
    Doctrine\Common\EventManager,
    Doctrine\ODM\MongoDB\Events,
    Doctrine\Common\ClassLoader,
    Doctrine\Common\Annotations\AnnotationReader,
    Doctrine\ODM\MongoDB\Mapping\Driver\AnnotationDriver;

// ODM Classes
$classLoader = new ClassLoader('Doctrine\ODM\MongoDB', '/usr/local/zend/share/pear');
$classLoader->register();

// Common Classes
$classLoader = new ClassLoader('Doctrine\Common', '/usr/local/zend/share/pear');
$classLoader->register();

// MongoDB Classes
$classLoader = new ClassLoader('Doctrine\MongoDB', '/usr/local/zend/share/pear');
$classLoader->register();

// Document classes
$classLoader = new ClassLoader('Documents', __DIR__);
$classLoader->register();

$config = new Configuration();
$config->setProxyDir(__DIR__ . '/cache');
$config->setProxyNamespace('Proxies');

$config->setHydratorDir(__DIR__ . '/cache');
$config->setHydratorNamespace('Hydrators');

$reader = new AnnotationReader();
$reader->setDefaultAnnotationNamespace('Doctrine\ODM\MongoDB\Mapping\\');
$config->setMetadataDriverImpl(new AnnotationDriver($reader, __DIR__ . '/Documents'));

$dm = DocumentManager::create(new Connection(), $config);

/**
 * Failure Test Code
 */
$tag = new Documents\Tag();
$tag->setTag('test1');


$file = new Documents\File();
$file->setFile('/tmp/test');
$file->addTag($tag);

$dm->persist($tag);
$dm->persist($file);
$dm->flush();

/**
 *  Produces
 *
 * > db.File.files.find();
{ "$pushAll" : { "tags" : [ { "$ref" : "Tag", "$id" : ObjectId("4f417a8405685ac450000000") } ] }, "_id" : ObjectId("4f417a8405685ac450000001"), "chunkSize" : 262144, "filename" : "/tmp/test", "length" : 8, "md5" : "eb1a3227cdc3fedbaec2fe38bf6c044a", "uploadDate" : ISODate("2012-02-19T22:41:08.150Z") }
 */

/**
 * Expected result when File property is not included in the Document
 */
$tag = new Documents\Tag();
$tag->setTag('test1');

$file = new Documents\Filepath();
$file->setFile('/tmp/test');
$file->addTag($tag);

$dm->persist($tag);
$dm->persist($file);
$dm->flush();

/**
 * Expected outcome
 *
 * > db.Filepath.find();
{ "_id" : ObjectId("4f417b6205685aef50000005"), "file" : "/tmp/test", "tags" : [ { "$ref" : "Tag", "$id" : ObjectId("4f417b6205685aef50000004") } ] }
 */