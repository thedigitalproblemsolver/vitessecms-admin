<?php declare(strict_types=1);

namespace VitesseCms\Admin;

use Phalcon\Exception;
use ReflectionClass;
use stdClass;
use VitesseCms\Admin\Utils\AdminListUtil;
use VitesseCms\Content\Models\Item;
use VitesseCms\Core\AbstractController;
use VitesseCms\Core\Factories\PaginatonFactory;
use VitesseCms\Admin\Forms\AdminlistForm;
use VitesseCms\Datagroup\Helpers\DatagroupHelper;
use VitesseCms\Core\Helpers\ItemHelper;
use VitesseCms\Datagroup\Models\Datagroup;
use VitesseCms\Core\Models\Elasticsearch;
use VitesseCms\Core\Models\Log;
use VitesseCms\Core\Utils\FileUtil;
use VitesseCms\Database\AbstractCollection;
use VitesseCms\Database\Interfaces\BaseCollectionInterface;
use VitesseCms\Form\AbstractForm;
use VitesseCms\Form\Helpers\ElementHelper;
use VitesseCms\Language\Helpers\LanguageHelper;
use VitesseCms\Language\Models\Language;
use MongoDB\BSON\ObjectId;
use Phalcon\Forms\Element\Check;
use Phalcon\Forms\Element\Numeric;
use Phalcon\Forms\Element\Select;
use Phalcon\Forms\Element\Text;
use Phalcon\Http\Request;
use function count;
use function get_class;
use function in_array;
use function is_array;
use function is_callable;

abstract class AbstractAdminController extends AbstractController
{
    /**
     * @var string
     */
    protected $link;

    /**
     * @var array
     */
    protected $unDeletable;

    /**
     * @var AbstractCollection
     */
    protected $class;

    /**
     * @var string
     */
    protected $classForm;

    /**
     * @var string
     */
    protected $listOrder;

    /**
     * @var int
     */
    protected $listOrderDirection;

    /**
     * @var bool
     */
    protected $listSortable;

    /**
     * @var bool
     */
    protected $listNestable;

    /**
     * @var string
     */
    protected $listTemplate;

    /**
     * @var string
     */
    protected $listTemplatePath;

    /**
     * @var string
     */
    protected $controllerName;

    /**
     * @var array
     */
    protected $renderParams;

    /**
     * @var bool
     */
    protected $displayEditButton;

    public function onConstruct()
    {
        parent::onConstruct();

        $this->link = $this->url->getBaseUri() . 'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName();
        $this->unDeletable = [];
        $this->class = null;
        $this->classForm = null;
        $this->listOrder = 'name';
        $this->listOrderDirection = 1;
        $this->listSortable = false;
        $this->listNestable = false;
        $this->listTemplate = 'adminList';
        $this->listTemplatePath = $this->configuration->getVendorNameDir() . 'admin/src/Resources/views/';
        $this->controllerName = (new ReflectionClass($this))->getShortName();
        $this->renderParams = [];
        $this->displayEditButton = true;
    }

    public function adminListAction(): void
    {
        $adminListButtons = $this->view->renderModuleTemplate(
            $this->router->getModuleName(),
            str_replace('admin', '', $this->router->getControllerName()) . 'Buttons',
            'admin/list/'
        );

        $this->view->set(
            'content',
            $this->view->renderTemplate(
                $this->listTemplate,
                $this->listTemplatePath,
                [
                    'list' => $this->recursiveAdminList($this->getAdminListPagination()),
                    'editBaseUri' => $this->link,
                    'isAjax' => $this->request->isAjax(),
                    'filter' => $this->eventsManager->fire(
                        get_class($this) . ':adminListFilter',
                        $this,
                        new AdminlistForm()
                    ),
                    'adminListButtons' => $adminListButtons,
                    'displayEditButton' => $this->displayEditButton
                ]
            )
        );

        $this->prepareView();
    }

    protected function recursiveAdminList(stdClass $pagination, int $level = 0): string
    {
        $params = [
            'id' => false,
            'ajaxurl' => false,
            'class' => 'list-group admin-list',
            'listSortable' => false,
        ];
        $templatePath = $this->configuration->getVendorNameDir() . 'admin/src/Resources/views/admin';
        if ($level === 0 && $this->listSortable) :
            $params = [
                'id' => uniqid('item-', false),
                'ajaxurl' => 'admin' . '/' .
                    $this->router->getModuleName() . '/' .
                    $this->router->getControllerName() . '/' .
                    'saveorder',
                'class' => 'list-group admin-list sortable',
                'listSortable' => true,
            ];
        endif;

        $return = $this->view->renderTemplate('recursiveAdminListStart', $templatePath, $params);

        /** @var AbstractCollection $item */
        foreach ($pagination->items as $item) :
            $this->eventsManager->fire($this->controllerName . ':adminListItem', $this, $item);

            $return .= $this->view->renderTemplate(
                'recursiveAdminListItemStart',
                $templatePath,
                [
                    'item' => $item,
                    'adminListButtons' => AdminListUtil::getAdminListButtons(
                        $item,
                        str_replace($this->url->getBaseUri(), '', $this->link),
                        $this->acl
                    ),
                    'adminListRowClass' => ItemHelper::getRowStateClass($item->isPublished()),
                    'adminListName' => $item->getAdminlistName(),
                    'editBaseUri' => $this->link,
                    'adminListExtra' => $item->getAdminListExtra(),
                ]
            );

            if ($item->hasChildren() && $this->configuration->renderAdminListChildren()) :
                if ($item->_('datagroup')) :
                    Datagroup::setFindValue('parentId', $item->_('datagroup'));
                    $datagroup = Datagroup::findFirst();
                    if ($datagroup && $datagroup->_('itemOrdering')) :
                        switch ($datagroup->_('itemOrdering')) :
                            case 'createdAt':
                                $item::addFindOrder($datagroup->_('itemOrdering'), -1);
                                break;
                            default:
                                $item::addFindOrder($datagroup->_('itemOrdering'), 1);
                                break;
                        endswitch;
                    endif;
                endif;
                $childPagination = $this->getAdminListPagination((string)$item->getId());

                $return .= $this->recursiveAdminList($childPagination, $level + 1);
            elseif ($this->listNestable):
                $return .= '<ol></ol>';
            endif;
            $return .= $this->view->renderTemplate('recursiveAdminListItemEnd', $templatePath);
        endforeach;

        if ($pagination->total_pages > 1) :
            $return .= $this->view->renderTemplate(
                'recursiveAdminListPagination',
                $templatePath,
                ['pagination' => $pagination]
            );
        endif;

        $return .= $this->view->renderTemplate('recursiveAdminListEnd', $templatePath);

        return $return;
    }

    protected function getAdminListPagination(?string $parentId = null): stdClass
    {
        /** @var AbstractCollection $item */
        $item = new $this->class();
        if ($parentId === null) :
            $this->applyFilter($item);
            $item::setFindParseFilter(true);
        endif;
        $item::setFindPublished(false);
        $item::addFindOrder($this->listOrder, $this->listOrderDirection);
        $item::setRenderFields(false);
        $item::setFindLimit(999);
        if ($parentId !== null) :
            $item::setFindValue('parentId', $parentId);
        endif;
        $items = $item::findAll();

        if (count($items) === 0) :
            $this->flash->setError('ADMIN_NO_ITEMS_FOUND');
        endif;

        return PaginatonFactory::createFromArray(
            $items,
            $this->request,
            $this->url,
            'page_' . $parentId
        );
    }

    protected function applyFilter(AbstractCollection $item): void
    {
        $filter = [];

        if (
            is_array($this->request->get('filter'))
            && !empty($this->request->get('filter'))
        ):
            $resetSortable = false;
            foreach ($this->request->get('filter') as $filterName => $filterValue) :
                if (!empty($filterValue)) :
                    $filter[$filterName] = trim($filterValue);
                    $resetSortable = true;
                endif;
            endforeach;
            $this->session->set('filter_' . $this->class, $filter);
            if ($resetSortable) :
                $this->listSortable = false;
                $this->listNestable = false;
            endif;
        elseif (
            is_array($this->session->get('filter_' . $this->class))
            && !empty($this->session->get('filter_' . $this->class))
        ) :
            $_REQUEST['filter'] = $filter = $this->session->get('filter_' . $this->class);
            $this->listSortable = false;
            $this->listNestable = false;
        else :
            $item::setFindValue('parentId', ['$in' => ['', null]]);
        endif;

        if (count($filter) > 1 && isset($filter['datagroup'])) :
            $datagroup = Datagroup::findById($this->request->get('filter')['datagroup']);
            if ($datagroup->_('itemOrdering')) :
                $this->listOrder = $datagroup->_('itemOrdering');
            endif;

            if ($datagroup->_('sortable')) :
                $this->listSortable = true;
                $this->listNestable = true;
            endif;

            $datagroups = [];
            $datagroupChildren = DatagroupHelper::getChildrenFromRoot($datagroup);
            foreach ($datagroupChildren as $datagroupChild) :
                $datagroups[] = (string)$datagroupChild->getId();
            endforeach;
            $item::setFindValue('datagroup', ['$in' => $datagroups]);
        endif;
    }

    public function saveAction(?string $itemId = null, AbstractCollection $item = null, AbstractForm $form = null): void
    {
        /** @deprecated item should be passes in controller */
        if ($item === null) :
            $this->class::setFindPublished(false);
            $this->class::setRenderFields(false);
            if ($itemId !== null) :
                $item = $this->class::findById($itemId);
            else :
                $item = new $this->class();
            endif;
        endif;

        $this->eventsManager->fire(get_class($this) . ':beforePostBinding', $this, $item);
        if ($form === null) :
            $form = $this->getItemForm($form, $item);
        endif;
        $form->bind($this->request->getPost(), $item);

        if ($form->validate($this)) :
            $item = $this->parseFormElement($form, $item);
            $item = $this->parseSubmittedFiles($item);
            $this->eventsManager->fire(get_class($this) . ':beforeModelSave', $this, $item);
            $item->save();

            if ($item->_('parentId')) :
                Item::setFindPublished(false);
                $parentItem = Item::findById($item->_('parentId'));
                if ($parentItem) :
                    $parentItem->set('hasChildren', true);
                    $parentItem->save();
                endif;
            endif;

            $this->afterSave($item);
            $this->cache->flush();

            $this->log->write($item->getId(), get_class($item), 'Item saved');

            $this->flash->setSucces('ADMIN_ITEM_SAVED');
        endif;

        $this->redirect($this->link . '/edit/' . $item->getId(), [], false);
    }

    protected function getItemForm(?AbstractForm $form, AbstractCollection $item): ?AbstractForm
    {
        if ($form === null && $this->classForm !== null) :
            /** @var AbstractForm $form */
            $form = new $this->classForm($item);
        endif;

        if ($form !== null) :
            $form->setEntity($item);
            if (method_exists($form, 'setRepositories')) :
                $form->setRepositories($this->repositories);
            endif;
            if (method_exists($form, 'buildForm')) :
                $form->buildForm();
            endif;
        endif;

        return $form;
    }

    /**\
     * @param AbstractForm $form
     * @param AbstractCollection $item
     *
     * @return AbstractCollection
     */
    protected function parseFormElement(AbstractForm $form, AbstractCollection $item): AbstractCollection
    {
        foreach ($form->getElements() as $element) :
            switch (get_class($element)) :
                case Check::class:
                    if ($this->request->getPost($element->getName()) === null) :
                        $item->set($element->getName(), null);
                    endif;
                    break;
                case Numeric::class:
                    if (is_array($element->getValue())) :
                        $values = (array)$element->getValue();
                        foreach ($values as $key => $value) :
                            $values[$key] = (float)$value;
                        endforeach;
                        $item->set($element->getName(), $values);
                    else :
                        $item->set($element->getName(), (float)$element->getValue());
                    endif;
                    break;
                case Select::class:
                    if ($element->getAttribute('multiple')) :
                        $fieldName = str_replace('[]', '', $element->getName());
                        $post = (new Request())->getPost();

                        if (isset($post[$fieldName])) :
                            $item->set($fieldName, $post[$fieldName]);
                        else :
                            $item->set($fieldName, null);
                        endif;

                        if ($element->getAttribute('multilang')) :
                            $values = $item->getRaw($fieldName);
                            if (empty($values)) :
                                $values = [];
                            endif;
                            foreach (Language::findAll() as $language) :
                                if (!isset($values[$language->_('short')])) :
                                    $values[$language->_('short')] = [];
                                endif;
                            endforeach;
                            $item->set($fieldName, $values);
                        endif;
                    elseif (
                        !empty($element->getValue())
                        && substr_count($element->getName(), '[') > 0
                        && substr_count($element->getName(), ']') > 0
                    ) :
                        $index = ElementHelper::parseTextNameAttribute($element->getName());
                        $item->add($index[0], $element->getValue(), $index[1]);
                    endif;
                    break;
                case Text::class:
                    if (
                        !empty($element->getValue())
                        && substr_count($element->getName(), '[') > 0
                        && substr_count($element->getName(), ']') > 0
                    ) :
                        $index = ElementHelper::parseTextNameAttribute($element->getName());
                        $item->add($index[0], $element->getValue(), $index[1]);
                    endif;
                    break;
            endswitch;
        endforeach;

        return $item;
    }

    /**
     * @param AbstractCollection $item
     *
     * @return AbstractCollection
     * @throws Exception
     */
    protected function parseSubmittedFiles(AbstractCollection $item): AbstractCollection
    {
        if ($this->request->hasFiles() === true) :
            foreach ($this->request->getUploadedFiles() as $file) :
                if (!empty($file->getName())) :
                    $name = FileUtil::sanatize($file->getName());
                    if ($file->moveTo($this->config->get('uploadDir') . $name)) :
                        $key = $file->getKey();
                        if (substr_count($key, '.') > 0) :
                            $tmp = explode('.', $key);
                            $valueName = $tmp[0];
                            if (!is_array($item->$valueName)) :
                                $item->$valueName = [];
                            endif;
                            $item->$valueName[$tmp[1]] = $name;
                        else :
                            $item->$key = $name;
                        endif;
                    else :
                        $this->flash->setError('FILE_UPLOAD_FAILED', [$file->getName()]);
                    endif;
                endif;
            endforeach;
        endif;

        return $item;
    }

    /**
     * @param AbstractCollection $item
     */
    public function beforeModelSave(AbstractCollection $item): void
    {
    }

    /**
     * @param AbstractCollection $item
     */
    public function afterSave(AbstractCollection $item): void
    {
    }

    public function editAction(
        string $itemId = null,
        string $template = 'editForm',
        string $templatePath = 'form/src/Resources/views/admin/',
        AbstractForm $form = null
    ): void
    {
        $adminEditForm = '';
        /** @var AbstractCollection $item */
        $item = new $this->class();
        if ($itemId !== null) :
            $this->class::setFindPublished(false);
            $this->class::setRenderFields(false);
            $item = $this->class::findById($itemId);
        endif;

        $this->eventsManager->fire(get_class($this) . ':beforeEdit', $this, $item);

        $form = $this->getItemForm($form, $item);
        if ($form !== null) :
            $adminEditForm = $form->renderForm(
                'admin/' . $this->router->getModuleName() . '/' . $this->router->getControllerName() . '/save/' . $itemId
            );
        endif;

        $adminButtons = $this->view->renderModuleTemplate(
            $this->router->getModuleName(),
            str_replace('admin', '', $this->router->getControllerName()) . 'Buttons',
            '/admin/edit/',
            ['editId' => $item->getId()]
        );

        $this->view->setVar('content', $this->view->renderTemplate(
            $template,
            $this->configuration->getVendorNameDir() . $templatePath,
            array_merge([
                'adminEditItem' => $item,
                'adminButtons' => $adminButtons,
                'adminEditForm' => $adminEditForm,
            ], $this->renderParams)
        ));
        $this->prepareView();
    }

    public function deleteAction(): void
    {
        /** @var AbstractCollection $item */
        $item = new $this->class();
        $item::setFindPublished(false);
        $item::setRenderFields(false);
        $item = $item::findById($this->dispatcher->getParam(0));
        if ($item) :
            $item->beforeDelete();
            $item->delete();
            $item->afterDelete();

            if ($this->class !== Log::class) :
                $this->log->write(
                    $item->getId(),
                    $this->class,
                    $this->language->get('ADMIN_ITEM_DELETED', [$item->_('name')])
                );
            endif;

            $this->flash->setSucces('ADMIN_ITEM_DELETED', [$item->_('name')]);

            if ($item->hasParent()) :
                $this->class::setFindValue('parentId', $item->getParentId());
                $count = $this->class::count();
                if ($count === 0) :
                    $parent = $this->class::findById($item->getParentId());
                    $parent->hasChildren = false;
                    $parent->save();
                endif;
            endif;
        else :
            $this->flash->setError('ADMIN_ITEM_NOT_FOUND');
        endif;

        $this->redirect($this->link . '/adminList');
    }

    public function copyAction(): void
    {
        if ($this->dispatcher->getParam(0)) :
            $this->class::setFindPublished(false);
            $item = $this->class::findById($this->dispatcher->getParam(0));
            $item->setId(new ObjectId());
            $item->set('createdAt', date('Y-m-d H:i:s'));
            $item->set('published', false);

            $parsedLanguage = [];
            foreach (Language::findAll() as $language) :
                if (!in_array($language->_('short'), $parsedLanguage, true)) :
                    $item->set(
                        'name',
                        $item->_('name', $language->_('short')) . ' - copy',
                        true,
                        $language->_('short')
                    );
                    $parsedLanguage[] = $language->_('short');
                endif;
            endforeach;

            $item->save();
        endif;

        $this->redirect($this->link . '/adminList');
    }

    public function togglePublishAction(): void
    {
        $logMessage = 'ADMIN_ITEM_PUBLISHED';

        /** @var AbstractCollection $item */
        $item = new $this->class();
        $item::setFindPublished(false);
        if (is_callable([$item, 'setRenderFields'])) :
            $item::setRenderFields(false);
        endif;
        $item = $item::findById($this->dispatcher->getParam(0));

        if ($item->_('published') === true) :
            $item->set('published', false);
            $logMessage = 'ADMIN_ITEM_UNPUBLISHED';
            $this->flash->setSucces('ADMIN_ITEM_UNPUBLISHED');
        else :
            $item->set('published', true);
            $this->flash->setSucces('ADMIN_ITEM_PUBLISHED');
        endif;

        $item->beforePublish();
        $item->save();
        $item->afterPublish();
        $this->eventsManager->fire(get_class($this) . ':afterPublish', $this, $item);

        $this->log->write($item->getId(), $this->class, $this->language->get($logMessage));

        $this->redirect();
    }

    public function saveorderAction(): void
    {
        $ordering = (array)json_decode($this->request->get('ordering'));
        $this->recursiveSaveOrder($ordering[0], $this->class);

        $this->flash->setSucces('ADMIN_ORDERING_SAVED');

        $this->redirect($this->link . '/adminList');
    }

    /**
     * @param array $ordering
     * @param string $object
     * @param string|null $parentId
     */
    protected function recursiveSaveOrder(
        array $ordering,
        string $object,
        string $parentId = null
    ): void
    {
        $orderNumber = 0;
        foreach ($ordering as $order) :
            if (isset($order->id)) :
                $hasChildren = false;
                if (
                    isset($order->children)
                    && count($order->children[0]) > 0
                ) :
                    $hasChildren = true;
                endif;

                /** @var AbstractCollection $object */
                $object::setFindPublished(false);
                $item = $object::findById($order->id);
                $item->set('parentId', $parentId);
                $item->set('hasChildren', $hasChildren);
                $item->set('ordering', $orderNumber++);
                $item->save();

                if ($hasChildren) :
                    $this->recursiveSaveOrder($order->children[0], $object, $order->id);
                endif;
            endif;
        endforeach;
    }

    public function addRenderParam(string $key, $value): void
    {
        $this->renderParams[$key] = $value;
    }

    public function isListSortable(): bool
    {
        return $this->listSortable;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param AbstractCollection $item
     *
     * @return string
     *
     * @deprecated wordt deze nog gebruikt? Anders mag hij weg
     */
    protected function getAdminlistName(AbstractCollection $item): string
    {
        return $item->getAdminlistName();
    }

    /**
     * @param string $id
     *
     * @deprecated is this still used
     * @todo       is this still used
     */
    protected function setUnDeletable(string $id): void
    {
        $this->unDeletable[] = $id;
    }
}
