<?php

namespace App\Helpers;

use App\Entity\ForumPost;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class ForumHelper implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private TranslatorInterface $translator;

    /**
     * @var StaticDataHelper
     */
    private StaticDataHelper $staticDataHelper;

    /**
     * @param TranslatorInterface $translator
     * @param StaticDataHelper $staticDataHelper
     */
    public function __construct(TranslatorInterface $translator, StaticDataHelper $staticDataHelper)
    {
        $this->translator = $translator;
        $this->staticDataHelper = $staticDataHelper;
    }

    /**
     * @param ForumPost $post
     * @param string|null $highlight
     * @return string
     * @throws Exception
     */
    public function getDisplayForumPost(ForumPost $post, string $highlight = null): string
    {
        if ($post->text->newStyle) {
            $text = strip_tags(
                str_replace(['&nbsp;', "\r\n", '<p>&nbsp;</p>'], ' ', $post->text->text),
                '<p><a><img><ul><ol><li><blockquote><strong><em><s><hr>'
            );
        } else {
            $text = $this->doSpecialText($post->text->text);
        }
        $text = nl2br($this->replaceStaticData($text));

        if (!is_null($post->editTimestamp)) {
            $text .= '<br /><br /><i><span class="edit_text">Laatst bewerkt door ' . $post->editor->username .
                ' op ' . $post->editTimestamp->format('d-m-Y H:i').
                (strlen($post->editReason) > 0 ? ', reden: ' . $post->editReason : '') . '</span></i>';
        }
        if ($post->signatureOn && strlen($post->author->info->info) > 0) {
            $text .= '<br /><hr style="margin-left:0; width:15%;" />' . $post->author->info->info;
        }
        if (!is_null($highlight) && strlen($highlight) > 0) {
            $text = $this->doHighlight($text, $highlight);
        }

        return $text;
    }

    /**
     * @param string $text
     * @param string $needle
     * @return string
     */
    private function doHighlight(string $text, string $needle): string
    {
        // Note the single quotes, they are necessary because of the usage of a backslash
        $regex = sprintf('#(?!<.*?)(%s)(?![^<>]*?>)#i', preg_quote($needle));
        return preg_replace($regex, '<strong>\1</strong>', $text);
    }

    /**
     * @param string $text
     * @return string
     */
    function doSpecialText(string $text): string
    {
        // Put a space before all unquotes or else they can give a Javascript error in IE (if proceeded by a link)
        $text = str_replace('%unquote%', ' %unquote%', $text);

        $text = $this->replaceLinks($text);
        $text = $this->replaceSmileys($text);

        // Replace %quote% and %unquote% with their correct HTML tags
        $numberOfQuote = 0;
        $numberOfUnquote = 0;
        while (stripos($text, '%quote%') !== false) {
            $text = preg_replace(
                '[%quote%]',
                '<blockquote><span style="font-size:8px; font-weight:bold;">Quote' . '</span><hr />',
                $text,
                1
            );
            ++$numberOfQuote;
        }
        while (stripos($text, '%unquote%') !== false) {
            $text = preg_replace('[%unquote%]', '<hr /></blockquote> ', $text, 1);
            ++$numberOfUnquote;
        }

        // Place extra quotes if necessary
        $doQuotes = $numberOfUnquote - $numberOfQuote;
        for ($doQuote = 0; $doQuote < $doQuotes; ++$doQuote) {
            $text = '<blockquote><span style="font-size:8px; font-weight:bold;">Quote' . '</span><hr />' . $text;
        }
        // Place extra unquotes if necessary
        $doQuotes = $numberOfQuote - $numberOfUnquote;
        for ($doUnquote = 0; $doUnquote < $doQuotes; ++$doUnquote) {
            $text .= ' <hr /></blockquote>';
        }

        return str_replace('&', '&amp;', $text);
    }

    /**
     * @param string $text
     * @return string
     */
    private function replaceLinks(string $text): string
    {
        $pattern = '/((((http|https|ftp|ftps)\:\/\/))' .
            '(([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,63})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(\/[^) \n\r]*)?)/';
        $replacement = '<a href="\1" rel="nofollow" target="_blank">\5</a>';
        $text = preg_replace($pattern, $replacement, $text);
        if (isset($_SERVER['HTTP_HOST'])) {
            $server = $_SERVER['HTTP_HOST'];
            if (substr($server, 0, 4) != 'http') {
                $server = 'https://' . $server;
            }
            $replace = [
                'http://www.somda.nl', 'http://somda.nl', 'http://test.somda.nl',
                'https://www.somda.nl', 'https://somda.nl', 'https://test.somda.nl'
            ];
            $text = str_replace($replace, $server, $text);
        }
        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    private function replaceSmileys(string $text): string
    {
        // Replace smileys with percent-codes (%xx%)
        for ($smileyNumber = 1; $smileyNumber <= 18; ++$smileyNumber) {
            $text = str_replace(
                '%' . sprintf('%2d', $smileyNumber) . '%',
                '<img alt="" src="/images/smileys/' . sprintf('%2d', $smileyNumber) . '.gif" />',
                $text
            );
        }
        // Replace smileys with standard codes
        $smileys = [
            ' =>' => '01', ' :)' => '02', ' :-)' => '02', ' :s' => '03', ' :-s' => '03', ' :S' => '03', ' :-S' => '03',
            ' :d' => '08', ' :-d' => '08', ' :D' => '08', ' :-D' => '08', ' :p' => '11', ' :-p' => '11', ' :P' => '11',
            ' :-P' => '11', ' :$' => '12', ' :-$' => '12', ' :(' => '14', ' :-(' => '14', ' :o' => '16', ' :-o' => '16',
            ' :O' => '16', ' :-O' => '16', ' ;)' => '17', ' ;-)' => '17',
        ];
        foreach ($smileys as $smileyCode => $smileyNumber) {
            $text = str_replace($smileyCode, '<img alt="" src="/images/smileys/' . $smileyNumber . '.gif" />', $text);
        }

        return $text;
    }

    /**
     * @param string $text
     * @return string
     * @throws Exception
     */
    private function replaceStaticData(string $text): string
    {
        $locations = $this->staticDataHelper->getLocations();
        $users = $this->staticDataHelper->getUsers();
        $routes = $this->staticDataHelper->getRoutes();

        $locationsDone = [];
        $usersDone = [];
        $routesDone = [];

        $textChunks = array_diff(str_word_count(strip_tags($text), 2, '@0123456789'), ['nbsp']);
        foreach ($textChunks as $chunk) {
            $word = trim($chunk);
            if (preg_match('/^[A-Z][A-Za-z]*$/', $word)) {
                // Match on an abbreviation (uppercase character followed by a 0 or more lowercase characters)
                if (!isset($locationsDone[$word]) && isset($locations[$word])) {
                    $text = preg_replace(
                        '/(^|[<\s.-?:;().-\/\[\]])(' . $word . ')($|[<\s,-?:;().-\/\[\]])/m',
                        '\\1<!-- s\\2 --><span class="tooltip" title="' .
                            strtolower(htmlspecialchars($locations[$word])) . '">\\2<!-- s\\2 --></span>\\3',
                        $text
                    );
                    $locationsDone[$word] = true;
                }
            } elseif (!isset($usersDone[$word]) && isset($users[$word])) {
                $text = preg_replace(
                    '/(^|[<\s.-?:;().-\/\[\]])(' . $word . ')($|[<\s,-?:;().-\/\[\]])/m',
                    '\\1<!-- s\\2 --><span class="tooltip" title="Somda gebruiker ' .
                        htmlspecialchars($users[$word]) . '">' . substr($word, 1) . '<!-- \\2 --></span>\\3',
                    $text
                );
                $usersDone[$word] = true;
            } elseif (!isset($routesDone[$word]) && isset($routes[$word])) {
                $text = preg_replace(
                    '/(^|[<\s.-?:;().-\/\[\]])(' . $word . ')($|[<\s,-?:;().-\/\[\]])/m',
                    '\\1<!-- s\\2 --><span class="tooltip" title="' . htmlspecialchars($routes[$word]) .
                        '">\\2<!-- s\\2 --></span>\\3',
                    $text,
                    1
                );
                $routesDone[$word] = true;
            }
        }

        return $text;
    }
}
