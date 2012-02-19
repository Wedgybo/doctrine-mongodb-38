<?php

namespace Documents;

use Doctrine\Common\Collections\ArrayCollection;

/** @Document */
class File
{

    /** @Id */
    protected $id;

    /** @File */
    protected $file;

    /** @ReferenceMany(targetDocument="Documents\Tag") */
    protected $tags;

    public function __construct()
    {
        $this->tags = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function setId($id)
    {
        $this->id = $id;
    }

    public function getFile()
    {
        return $this->file;
    }

    public function setFile($file)
    {
        $this->file = $file;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags($tags)
    {
        $this->tags = $tags;
    }

    public function addTag(Tag $tag)
    {
        $this->tags->add($tag);
    }

}
