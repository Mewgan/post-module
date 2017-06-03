<?php

namespace Jet\Modules\Post\Models;

use Doctrine\Common\Collections\ArrayCollection;
use Jet\Models\Media;
use JetFire\Db\Model;
use Doctrine\ORM\Mapping;

/**
 * Class Post
 * @package Jet\Models
 * @Entity(repositoryClass="Jet\Modules\Post\Models\PostRepository")
 * @Table(name="posts")
 * @HasLifecycleCallbacks
 */
class Post extends Model implements \JsonSerializable
{
    /**
     * @Id
     * @Column(type="integer")
     * @GeneratedValue
     */
    protected $id;
    /**
     * @Column(type="string")
     */
    protected $title;
    /**
     * @Column(type="string")
     */
    protected $slug;
    /**
     * @Column(type="text", nullable=true)
     */
    protected $description = '';
    /**
     * @Column(type="text", nullable=true)
     */
    protected $content = '';
    /**
     * @ManyToOne(targetEntity="Jet\Models\Media")
     * @JoinColumn(name="thumbnail_id", referencedColumnName="id", nullable=true, onDelete="SET NULL")
     */
    protected $thumbnail;
    /**
     * @ManyToMany(targetEntity="PostCategory", inversedBy="posts")
     * @JoinTable(name="posts_categories")
     */
    protected $categories;
    /**
     * @ManyToOne(targetEntity="Jet\Models\Website")
     * @JoinColumn(name="website_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $website;
    /**
     * @Column(type="boolean")
     */
    protected $published = true;
    /**
     * @Column(type="datetime")
     */
    public $created_at;
    /**
     * @Column(type="datetime", nullable=true)
     */
    public $updated_at;

    /**
     * Post constructor.
     */
    public function __construct() {
        $this->categories = new ArrayCollection();
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @param mixed $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * @param mixed $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * @return Media
     */
    public function getThumbnail()
    {
        return $this->thumbnail;
    }

    /**
     * @param mixed $thumbnail
     */
    public function setThumbnail($thumbnail)
    {
        $this->thumbnail = $thumbnail;
    }

    /**
     * @return mixed
     */
    public function getPostCategories()
    {
        return $this->categories;
    }

    /**
     * @param $categories
     */
    public function setPostCategories($categories)
    {
        $this->categories = $categories;
    }

    /**
     * @param PostCategory $category
     */
    public function addPostCategory(PostCategory $category)
    {
        if (!$this->categories->contains($category)) $this->categories[] = $category;
    }

    /**
     * @param PostCategory $category
     */
    public function removePostCategory(PostCategory $category)
    {
        if ($this->categories->contains($category)) $this->categories->removeElement($category);

    }

    /**
     * @return mixed
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param mixed $website
     */
    public function setWebsite($website)
    {
        $this->website = $website;
    }

    /**
     * @return mixed
     */
    public function isPublished()
    {
        return $this->published;
    }

    /**
     * @param mixed $published
     */
    public function setPublished($published)
    {
        $this->published = $published;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param mixed $created_at
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt(\DateTime $updated_at)
    {
        $this->updated_at = $updated_at;
    }

    /**
     * @PrePersist
     */
    public function onPrePersist(){
        $this->setCreatedAt(new \DateTime('now'));
        $this->setUpdatedAt(new \DateTime('now'));
    }
    /**
     * @PreUpdate
     */
    public function onPreUpdate(){
        $this->setUpdatedAt(new \DateTime('now'));
    }

    /**
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by <b>json_encode</b>,
     * which is a value of any type other than a resource.
     * @since 5.4.0
     */
    function jsonSerialize()
    {
        return [
            'id' => $this->getId(),
            'title' => $this->getTitle(),
            'slug' => $this->getSlug(),
            'thumbnail' => $this->getThumbnail(),
            'description' => $this->getDescription(),
            'content' => $this->getContent(),
            'website' => [
                'id' => $this->getWebsite()->getId(),
                'domain' => $this->getWebsite()->getDomain(),
            ],
            'categories' => $this->getPostCategories()->toArray(),
            'published' => $this->isPublished(),
            'created_at' => $this->getCreatedAt(),
            'updated_at' => $this->getUpdatedAt()
        ];
    }
}