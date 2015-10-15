<?php

namespace Victoire\Widget\SearchBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Victoire\Bundle\PageBundle\Entity\Page;
use Victoire\Bundle\WidgetBundle\Entity\Widget;

/**
 * WidgetSearch.
 *
 * @ORM\Table("vic_widget_search")
 * @ORM\Entity
 */
class WidgetSearch extends Widget
{
    /**
     * @var string
     *
     * @ORM\Column(name="emitter", type="boolean", nullable=true)
     */
    private $emitter = true;

    /**
     * @var string
     *
     * @ORM\Column(name="receiver", type="boolean", nullable=true)
     */
    private $receiver;

    /**
     * @var string
     *
     * @ORM\ManyToOne(targetEntity="\Victoire\Bundle\PageBundle\Entity\Page", cascade={"persist"})
     * @ORM\JoinColumn(name="results_page_id", referencedColumnName="id", onDelete="SET NULL")
     */
    protected $resultsPage;

    /**
     * To String function
     * Used in render choices type (Especially in VictoireWidgetRenderBundle).
     *
     * @return string
     */
    public function __toString()
    {
        return 'Search #'.$this->id;
    }

    /**
     * @return bool
     */
    public function isEmitter()
    {
        return $this->emitter;
    }

    /**
     * @param bool $emitter
     */
    public function setEmitter($emitter)
    {
        $this->emitter = $emitter;
    }

    /**
     * @return bool
     */
    public function isReceiver()
    {
        return $this->receiver;
    }

    /**
     * @param bool $receiver
     */
    public function setReceiver($receiver)
    {
        $this->receiver = $receiver;
    }

    /**
     * Set resultsPage.
     *
     * @param Page $resultsPage
     *
     * @return WidgetSearch
     */
    public function setResultsPage(Page $resultsPage)
    {
        $this->resultsPage = $resultsPage;

        return $this;
    }

    /**
     * Get resultsPage.
     *
     * @return string
     */
    public function getResultsPage()
    {
        return $this->resultsPage;
    }
}
