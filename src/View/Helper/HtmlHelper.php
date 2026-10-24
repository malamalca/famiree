<?php
declare(strict_types=1);

namespace App\View\Helper;

use Cake\View\Helper\HtmlHelper as CakeHtmlHelper;

/**
 * Html Helper
 *
 * Titles come straight from user entered data (profile names, post and
 * attachment titles) and can be empty, so a `null` title renders an empty link
 * instead of raising a TypeError.
 */
class HtmlHelper extends CakeHtmlHelper
{
    /**
     * Creates an HTML link.
     *
     * @param array<string>|string|null $title The content to be wrapped by `<a>` tags.
     * @param array|string|null $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
     * @param array<string, mixed> $options Array of options and HTML attributes.
     * @return string An `<a>` element.
     */
    public function link(array|string|null $title, array|string|null $url = null, array $options = []): string
    {
        return parent::link($title ?? '', $url, $options);
    }
}
