<?php
namespace App\View\Helper;

use Cake\I18n\FrozenDate;
use Cake\Utility\Text;
use Cake\View\Helper;

/**
 * Famiree Helper
 *
 * @property \Cake\View\Helper\HtmlHelper $Html
 * @property \Cake\View\Helper\TextHelper $Text
 */
class FamireeHelper extends Helper
{
    public $helpers = ['Html', 'Text'];

    /**
     * Returns duration in form HH:MM
     *
     * @param int $seconds Duration in seconds
     * @return string
     */
    public function duration($seconds)
    {
        $hours = str_pad((string)(floor($seconds / 3600)), 2, '0', STR_PAD_LEFT);
        $minutes = str_pad((string)(floor($seconds / 60) % 60), 2, '0', STR_PAD_LEFT);

        return $hours . ':' . $minutes;
    }

    /**
     * Returns month name
     *
     * @param int $month Month number from 1..12
     * @return string
     */
    public function getMonthName($month)
    {
        $_time = new FrozenDate('2018-' . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '-01');
        $_format = 'MMMM';

        return $_time->i18nFormat($_format);
    }

    /**
     * Returns month names
     *
     * @return array
     */
    public function getMonthNames()
    {
        $ret = [];
        $_format = 'MMMM';

        for ($month = 1; $month < 13; $month++) {
            $ret[$month] = (new FrozenDate('2018-' . str_pad((string)$month, 2, '0', STR_PAD_LEFT) . '-01'))->i18nFormat($_format);
        }

        return $ret;
    }

    /**
     * Returns age
     *
     * @param string $dateString Date string
     * @return int
     */
    public function age($dateString)
    {
        if (strlen($dateString) == 4) {
            $dateString .= '-12-31';
        }

        $date = new FrozenDate($dateString);
        $now = new FrozenDate();

        $ret = $now->year - $date->year;
        if ($date->month > $now->month) {
            $ret--;
        } elseif ($date->month == $now->month && $date->day > $now->day) {
            $ret--;
        }

        return $ret;
    }

    /**
     * Creates a HTML link. Behaves exactly like Html::link with ability to use
     * nicer links in form like "[Link] additional data"
     *
     * @param string $title The content to be wrapped by <a> tags.
     * @param mixed $url Cake-relative URL or array of URL parameters, or external URL (starts with http://)
     * @param array $options Array of HTML attributes.
     * @param bool $confirmMessage JavaScript confirmation message.
     * @return string An <a /> element.
     */
    public function link($title, $url = null, $options = [], $confirmMessage = false)
    {
        if (preg_match('/\[(.*)\]/', $title, $matches)) {
            $link_element = $this->Html->link($matches[1], $url, array_merge((array)$options, ['confirm' => $confirmMessage]));

            return str_replace($matches[0], $link_element, $title);
        } else {
            return $this->Html->link($title, $url, array_merge((array)$options, ['confirm' => $confirmMessage]));
        }
    }

    /**
     * Extracts excerpt from text
     *
     * @param string $body Text
     * @param int $max_length Max length of text
     * @param string $page_delimiter Page delimiter
     * @return string
     */
    public function excerpt($body = null, $max_length = 300, $page_delimiter = '<!-- -- -->')
    {
        $ret = '';
        if (stripos($body, $page_delimiter) !== false) {
            $ret = substr($body, 0, stripos($body, $page_delimiter));
        } else {
            $ret = $this->Text->truncate($body, $max_length, ['ending' => '...', 'exact' => false, 'html' => true]);
        }

        return $ret;
    }

    /**
     * Replaces double line-breaks with paragraph elements.
     *
     * A group of regex replaces used to identify text formatted with newlines and
     * replace double line-breaks with HTML paragraph tags. The remaining
     * line-breaks after conversion become <<br />> tags, unless $br is set to '0'
     * or 'false'.
     *
     * @param string   $pee The text which has to be formatted.
     * @param int|bool $br  Optional. If set, this will convert all remaining
     * line-breaks after paragraphing. Default true.
     * @return string Text which has been converted into correct paragraph tags.
     */
    public function autop($pee, $br = 1)
    {
        if (trim($pee) === '') {
            return '';
        }
        $pee = $pee . "\n"; // just to make things a little easier, pad the end
        $pee = preg_replace('|<br />\s*<br />|', "\n\n", $pee);
        // Space things out a little
        $allblocks = '(?:table|thead|tfoot|caption|col|colgroup|tbody|tr|td|th|' .
            'div|dl|dd|dt|ul|ol|li|pre|select|form|map|area|blockquote|address|' .
            'math|style|input|p|h[1-6]|hr)';
        $pee = preg_replace('!(<' . $allblocks . '[^>]*>)!', "\n$1", $pee);
        $pee = preg_replace('!(</' . $allblocks . '>)!', "$1\n\n", $pee);
        $pee = str_replace(["\r\n", "\r"], "\n", $pee); // cross-platform NL
        if (strpos($pee, '<object') !== false) {
            $pee = preg_replace('|\s*<param([^>]*)>\s*|', "<param$1>", $pee);
            $pee = preg_replace('|\s*</embed>\s*|', '</embed>', $pee);
        }
        //$pee = preg_replace("/\n\n+/", "\n\n", $pee); // take care of duplicates
        // make paragraphs, including one at the end
        //miha: $pees = preg_split('/\n\s*\n/', $pee, -1, PREG_SPLIT_NO_EMPTY);
        $pees = preg_split('/\n/', $pee, -1);
        $pee = '';
        foreach ($pees as $tinkle) {
            $pee .= '<p>' . trim($tinkle, "\n") . "</p>\n";
        }
        // under certain conditions it could create a P of entirely whitespace
        // miha: $pee = preg_replace('|<p>\s*</p>|', '', $pee);
        $pee = preg_replace('|<p>\s*</p>|', '<br />', $pee);
        $pee = preg_replace(
            '!<p>([^<]+)</(div|address|form)>!',
            "<p>$1</p></$2>",
            $pee
        );
        $pee = preg_replace(
            '!<p>\s*(</?' . $allblocks . '[^>]*>)\s*</p>!',
            "$1",
            $pee
        ); // don't pee all over a tag
        $pee = preg_replace("|<p>(<li.+?)</p>|", "$1", $pee); // nested lists
        $pee = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $pee);
        $pee = str_replace('</blockquote></p>', '</p></blockquote>', $pee);
        $pee = preg_replace('!<p>\s*(</?' . $allblocks . '[^>]*>)!', "$1", $pee);
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*</p>!', "$1", $pee);
        if ($br) {
            $pee = preg_replace_callback(
                '/<(script|style).*?<\/\\1>/s',
                function ($matches) {
                    return str_replace("\n", "<PreserveNewline />", $matches[0]);
                },
                $pee
            );
            $pee = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $pee);
            $pee = str_replace('<PreserveNewline />', "\n", $pee);
        }
        $pee = preg_replace('!(</?' . $allblocks . '[^>]*>)\s*<br />!', "$1", $pee);
        $pee = preg_replace(
            '!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)[^>]*>)!',
            '$1',
            $pee
        );
        if (strpos($pee, '<pre') !== false) {
            $pee = preg_replace_callback(
                '!(<pre[^>]*>)(.*?)</pre>!is',
                'self::cleanPre',
                $pee
            );
        }
        $pee = preg_replace("|\n</p>$|", '</p>', $pee);

        return $pee;
    }

    /**
     * CleanPre function
     *
     * Callback function from regex which removes new lines
     *
     * @param mixed $matches Regex matches
     *
     * @return string
     */
    public static function cleanPre($matches)
    {
        if (is_array($matches)) {
            $text = $matches[1] . $matches[2] . "</pre>";
        } else {
            $text = $matches;
        }

        $text = str_replace('<br />', '', $text);
        $text = str_replace('<p>', "\n", $text);
        $text = str_replace('</p>', '', $text);

        return $text;
    }
}
