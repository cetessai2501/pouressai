<?php
namespace App\Annotations;


/**
 * @Annotation
 * @Target("PROPERTY")
 */
final class MyAnnotation
{
    /**
     * @var string
     */
    public $repositoryClass;

    /**
     * @var boolean
     */
    public $readOnly = false;
}


