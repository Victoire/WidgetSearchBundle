<?php

namespace Victoire\Widget\SearchBundle\Resolver;

use Doctrine\ORM\EntityManager;
use Elastica\Query;
use Elastica\Query\QueryString;
use Elastica\Result;
use FOS\ElasticaBundle\Configuration\ConfigManager;
use FOS\ElasticaBundle\Doctrine\RepositoryManager;
use FOS\ElasticaBundle\Repository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Victoire\Bundle\PageBundle\Entity\BasePage;
use Victoire\Bundle\PageBundle\Helper\PageHelper;
use Victoire\Bundle\TemplateBundle\Entity\Template;
use Victoire\Bundle\ViewReferenceBundle\Connector\ViewReferenceRepository;
use Victoire\Bundle\WidgetBundle\Model\Widget;
use Victoire\Bundle\WidgetBundle\Resolver\BaseWidgetContentResolver;

class WidgetSearchContentResolver extends BaseWidgetContentResolver
{
    private $request;
    private $elasticORM;
    private $businessIndexConfig;
    private $widgetsIndexConfig;
    protected $entityManager;
    private $viewReferenceRepository;
    private $pageHelper;

    /**
     * $filterChain is not cast because it can be null.
     *
     * @param RequestStack            $requestStack
     * @param RepositoryManager       $elasticORM
     * @param ConfigManager           $configManager
     * @param EntityManager           $entityManager
     * @param ViewReferenceRepository $viewReferenceRepository
     * @param PageHelper              $pageHelper
     *
     * @internal param Index $appIndex
     */
    public function __construct(RequestStack $requestStack, RepositoryManager $elasticORM, ConfigManager $configManager, EntityManager $entityManager, ViewReferenceRepository $viewReferenceRepository, PageHelper $pageHelper)
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->elasticORM = $elasticORM;
        $this->businessIndexConfig = $configManager->getIndexConfiguration('business');
        $this->widgetsIndexConfig = $configManager->getIndexConfiguration('widgets');
        $this->pagesIndexConfig = $configManager->getIndexConfiguration('pages');
        $this->entityManager = $entityManager;
        $this->viewReferenceRepository = $viewReferenceRepository;
        $this->pageHelper = $pageHelper;
    }

    /**
     * Get the static content of the widget.
     *
     * @param Widget $widget
     *
     * @return string
     */
    public function getWidgetStaticContent(Widget $widget)
    {
        // Boolan to check if we have result
        $hasResult = false;
        $parameters = parent::getWidgetStaticContent($widget);
        $parameters['query'] = $this->request->get('q');
        if (true === $parameters['emitter']) {
            $parameters['ajax'] = $parameters['receiver'];
        }
        if (true === $parameters['receiver']) {
            $parameters['search'] = [
                'business'  => [],
                'pages'     => [],
                'widgets'   => [],
            ];
            $alreadyAdded = [];
            if ($parameters['query']) {
                foreach ([$this->businessIndexConfig, $this->pagesIndexConfig, $this->widgetsIndexConfig] as $_indexConfig) {
                    //Search the query in all the business types (from fos_elastica.yml)
                    foreach ($_indexConfig->getTypes() as $_typeConfig) {
                        $parameters['search'][$_indexConfig->getName()][$_typeConfig->getName()] = [];
                        /** @var Repository $_repo */
                        $_repo = $this->elasticORM->getRepository($_typeConfig->getModel());
                        $locale = $widget->getWidgetMap()->getView()->getCurrentLocale();

                        //get fields for query
                        $fields = $this->getIndexedFields($_typeConfig->getMapping()['properties']);

                        if ('pages' == $_indexConfig->getName() && $locale) {
                            $query = self::getI18NQuery($this->request->get('q'), $locale);
                        } else {
                            $query = self::getBaseQuery($this->request->get('q'), $fields);
                        }

                        $query->setHighlight([
                            'order'      => 'score',
                            'tag_schema' => 'styled',
                            'fields'     => [
                                '*' => [
                                    'fragment_size' => 1000,
                                ],
                            ],
                        ]);
                        foreach ($_repo->findHybrid($query) as $_result) {
                            // We have a result
                            $hasResult = true;

                            $_entity = $_result->getTransformed();
                            /** @var Result $_result */
                            $_result = $_result->getResult();

                            if ($_result->getScore() > 0.4) {
                                if ($_entity instanceof Widget) {
                                    $view = $_entity->getWidgetMap()->getView();
                                    if ($this->isPageAccessible($view)) {
                                        if (!in_array($view->getId(), $alreadyAdded) && !$view instanceof Template) {
                                            $parameters['search'][$_indexConfig->getName()][$_typeConfig->getName()][] = [
                                                'page'       => $view,
                                                'result'     => $_result,
                                                'highlights' => $_result->getHighlights(),
                                            ];
                                            $alreadyAdded[] = $view->getId().$view->getName();
                                        }
                                    }
                                } elseif ($_entity instanceof BasePage) {
                                    if ($this->isPageAccessible($_entity)) {
                                        if (!in_array($_entity->getId(), $alreadyAdded) && !$_entity instanceof Template) {
                                            $parameters['search'][$_indexConfig->getName()][$_typeConfig->getName()][] = [
                                                'page'       => $_entity,
                                                'result'     => $_result,
                                                'highlights' => $_result->getHighlights(),
                                            ];
                                            $alreadyAdded[] = $_entity->getId().$_entity->getName();
                                        }
                                    }
                                } else {
                                    //$_entity is a Business Entity
                                    $businessPagesRefs = $this->viewReferenceRepository->getReferencesByParameters(
                                        [
                                            'entityId'        => $_entity->getId(),
                                            'entityNamespace' => $_typeConfig->getModel(),
                                        ]
                                    );

                                    foreach ($businessPagesRefs as $_businessPageRef) {
                                        $_businessPage = $this->pageHelper->findPageByReference($_businessPageRef, $_entity);
                                        if ($this->isPageAccessible($_businessPage, $_entity)) {
                                            if (!in_array($_businessPage->getId(), $alreadyAdded)) {
                                                $parameters['search'][$_indexConfig->getName()][$_typeConfig->getName()][] = [
                                                    'page'       => $_businessPage,
                                                    'result'     => $_result,
                                                    'highlights' => $_result->getHighlights(),
                                                ];
                                                $alreadyAdded[] = $_businessPage->getId().$_businessPage->getName();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $parameters['hasResult'] = $hasResult;

        return $parameters;
    }

    /**
     * @param $term
     * @param $locale
     *
     * @return Query
     */
    protected function getI18NQuery($term, $locale)
    {
        $filters = new \Elastica\Filter\BoolFilter();
        $filters->addMust(
            new \Elastica\Filter\Term(['locale' => $locale])
        );
        $filteredQuery = new \Elastica\Query\Filtered(new QueryString($term), $filters);

        return new Query($filteredQuery);
    }

    /**
     * @param $term
     * @param array $fields
     *
     * @return Query
     */
    protected function getBaseQuery($term, $fields)
    {
        $query = new \Elastica\Query\QueryString();
        $query->setParam('query', $term);
        $query->setParam('fields', $fields);

        return new Query($query);
    }

    /**
     * @param      $page
     * @param null $entity
     *
     * @return bool
     */
    protected function isPageAccessible($page, $entity = null)
    {
        try {
            $this->pageHelper->checkPageValidity($page, $entity);

            return true;
        } catch (AccessDeniedException $e) {
            return false;
        } catch (NotFoundHttpException $e) {
            return false;
        }
    }

    /**
     * @param $entity
     * @param null $parent
     *
     * @return array
     */
    protected function getIndexedFields($entity, $parent = null)
    {
        $fields = [];
        foreach ($entity as $name => $properties) {
            if (isset($properties['properties'])) {
                $fields = array_merge($fields, $this->getIndexedFields($properties['properties'], $parent.$name.'.'));
            } else {
                $fields[] = $parent.$name;
            }
        }

        return $fields;
    }
}
