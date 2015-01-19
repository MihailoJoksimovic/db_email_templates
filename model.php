<?php

namespace Externals\DB_Email_Templates;

class Model
{
    private $id;
    private $template_name;
    private $category_id;
    private $subject;
    private $body;
    private $dt_added;
    private $dt_last_used;
    private $slug;


    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
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
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $subject
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
    }

    /**
     * @return mixed
     */
    public function getSubject()
    {
        return $this->subject;
    }

    /**
     * @param mixed $template_name
     */
    public function setTemplateName($template_name)
    {
        $this->template_name = $template_name;
    }

    /**
     * @return mixed
     */
    public function getTemplateName()
    {
        return $this->template_name;
    }

    /**
     * @param mixed $category_id
     */
    public function setCategoryId($category_id)
    {
        $this->category_id = $category_id;
    }

    /**
     * @return mixed
     */
    public function getCategoryId()
    {
        return $this->category_id;
    }

    /**
     * @param mixed $dt_added
     */
    public function setDtAdded($dt_added)
    {
        $this->dt_added = $dt_added;
    }

    /**
     * @return mixed
     */
    public function getDtAdded()
    {
        return $this->dt_added;
    }

    /**
     * @param mixed $dt_last_used
     */
    public function setDtLastUsed($dt_last_used)
    {
        $this->dt_last_used = $dt_last_used;
    }

    /**
     * @return mixed
     */
    public function getDtLastUsed()
    {
        return $this->dt_last_used;
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
    public function getSlug()
    {
        return $this->slug;
    }

    public function exchange(array $data)
    {

        $this->setTemplateName($data['template_name']);
        $this->setCategoryId($data['category_id']);
        $this->setSubject($data['subject']);
        $this->setBody($data['body']);
        $this->setDtAdded($data['dt_added']);
        $this->setDtLastUsed($data['dt_last_used']);
        $this->setSlug($data['slug']);

        return $this;
    }
}