<?php
namespace verbb\navigation\services;

use Craft;
use craft\base\Component;
use craft\base\ElementInterface;
use craft\helpers\StringHelper;
use craft\helpers\UrlHelper;

class Breadcrumbs extends Component
{
    // Public Methods
    // =========================================================================

    public function getBreadcrumbs(array $options = []): array
    {
        $limit = $options['limit'] ?? null;

        $breadcrumbs = [];

        // Add the homepage to the breadcrumbs
        if ($element = Craft::$app->getElements()->getElementByUri('__home__')) {
            $breadcrumbs[] = $this->_getBreadcrumbItem($element, '');
        }

        $path = '';

        foreach (Craft::$app->getRequest()->getSegments() as $segment) {
            $path .= '/' . $segment;

            // Try and fetch an element based on the path
            $element = Craft::$app->getElements()->getElementByUri(ltrim($path, '/'));

            if ($element) {
                $breadcrumbs[] = $this->_getBreadcrumbItem($element, $segment, $path);
            } else {
                $breadcrumbs[] = $this->_getBreadcrumbItem($segment, $segment, $path);
            }
        }

        if ($limit) {
            return array_slice($breadcrumbs, 0, $limit);
        }

        return $breadcrumbs;
    }

    private function _getBreadcrumbItem($item, $segment, $path = ''): array
    {
        // Generate the title from the segment or element
        $title = StringHelper::titleize((string)$item);
        $isElement = false;
        $elementId = null;

        if ($item instanceof ElementInterface) {
            $isElement = true;
            $elementId = $item->id;

            // Check if the element has titles setup
            if ($item->hasTitles()) {
                $title = $item->title;
            }
        }

        return [
            'title' => $title,
            'url' => UrlHelper::siteUrl($path),
            'segment' => $segment,
            'isElement' => $isElement,
            'elementId' => $elementId,
        ];
    }
}