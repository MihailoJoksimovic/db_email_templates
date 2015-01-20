<?php

namespace Externals\DB_Email_Templates;

/**
 * Utility class used to ease off the AB (Split Testing) of templates
 *
 * @auhtor Mihailo Joksimovic <tinzey@gmail.com>
 */
class AbTesting
{
    /**
     * @var DataAdapter
     */
    private $emailTemplatesDa;

    /**
     * @var
     */
    private $templates;

    public function __construct(DataAdapter $da = null, array $templates = array())
    {
        $this->setEmailTemplatesDa($da);
        $this->setTemplates($templates);
    }

    public function initByCategorySlug($category_slug)
    {
        $da = $this->getEmailTemplatesDa();

        $templates  = $da->getActiveTemplatesByCategorySlug($category_slug);

        $this->setTemplates($templates);
    }

    public function hasTemplates()
    {
        return count($this->getTemplates()) > 0;
    }

    public function getTemplateCount()
    {
        return count($this->getTemplates());
    }

    /**
     * @return Model
     */
    public function nextTemplate()
    {
        $template = array_shift($this->templates);

        if (!empty($template)) {
            array_push($this->templates, $template);
        }

        return $template;
    }

    /**
     * @param \Externals\DB_Email_Templates\DataAdapter $emailTemplatesDa
     */
    public function setEmailTemplatesDa($emailTemplatesDa)
    {
        $this->emailTemplatesDa = $emailTemplatesDa;
    }

    /**
     * @return \Externals\DB_Email_Templates\DataAdapter
     */
    public function getEmailTemplatesDa()
    {
        return $this->emailTemplatesDa;
    }

    /**
     * @param array $templates
     */
    public function setTemplates($templates)
    {
        $this->templates = $templates;
    }

    /**
     * @return \SplStack
     */
    public function getTemplates()
    {
        return $this->templates;
    }

}

