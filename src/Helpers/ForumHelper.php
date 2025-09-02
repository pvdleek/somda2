<?php

namespace App\Helpers;

use App\Entity\ForumPost;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class ForumHelper implements RuntimeExtensionInterface
{
    private const REPLACE_WORD_START = '/(.*)\b(';
    private const REPLACE_WORD_END = ')\b(.*)/m';

    public function __construct(
        private readonly TranslatorInterface $translator,
        private readonly StaticDataHelper $static_data_helper,
        private readonly UserHelper $user_helper,
    ) {
    }

    /**
     * @throws \Exception
     */
    public function getDisplayForumPost(ForumPost $post): string
    {
        if ($post->text->new_style) {
            $text = \strip_tags(
                \str_replace(['&nbsp;', "\r\n", '<p>&nbsp;</p>'], ' ', $post->text->text),
                '<br><p><a><img><ul><ol><li><blockquote><strong><em><s><u><hr><i>'
            );
            $text = $this->replaceLinks($text);
        } else {
            $text = $this->doSpecialText($post->text->text);
        }
        $text = \nl2br($this->replaceStaticData($text));

        if (null !== $post->edit_timestamp) {
            $text .= '<br /><br /><i><span class="edit_text">Laatst bewerkt door '.$post->editor->username .
                ' op '.$post->edit_timestamp->format('d-m-Y H:i').
                (\strlen($post->edit_reason ?? '') > 0 ? ', reden: '.$post->edit_reason : '').'</span></i>';
        }
        if ($post->signature_on && strlen($signature = $this->user_helper->getSignatureForUser($post->author)) > 0) {
            $text .= '<br /><br /><hr style="margin-left:0; width:15%;" />'.$signature;
        }

        return $text;
    }

    private function doSpecialText(string $text): string
    {
        // Put a space before all unquotes or else they can give a Javascript error in IE (if proceeded by a link)
        $text = \str_replace('%unquote%', ' %unquote%', $text);

        $text = $this->replaceLinks($text);
        $text = $this->replaceSmileys($text);

        // Replace %quote% and %unquote% with their correct HTML tags
        $numberOfQuote = 0;
        $numberOfUnquote = 0;
        while (\stripos($text, '%quote%') !== false) {
            $text = \preg_replace('[%quote%]', '<blockquote><strong>Quote'.'</strong><hr />', $text, 1);
            ++$numberOfQuote;
        }
        while (\stripos($text, '%unquote%') !== false) {
            $text = \preg_replace('[%unquote%]', '<hr /></blockquote> ', $text, 1);
            ++$numberOfUnquote;
        }

        // Place extra quotes if necessary
        $doQuotes = $numberOfUnquote - $numberOfQuote;
        for ($doQuote = 0; $doQuote < $doQuotes; ++$doQuote) {
            $text = '<blockquote><strong>Quote'.'</strong><hr />'.$text;
        }
        // Place extra unquotes if necessary
        $doQuotes = $numberOfQuote - $numberOfUnquote;
        for ($doUnquote = 0; $doUnquote < $doQuotes; ++$doUnquote) {
            $text .= ' <hr /></blockquote>';
        }

        return \str_replace('&', '&amp;', $text);
    }

    private function replaceLinks(string $text): string
    {
        $pattern = '/!"((((http|https|ftp|ftps)\:\/\/))' .
            '(([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,63})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(\/[^) \n\r]*)?)/';
        $replacement = '<a href="\1" rel="ugc" target="_blank">\5</a>';
        $text = \preg_replace($pattern, $replacement, $text);

        $pattern = '/">((((http|https|ftp|ftps)\:\/\/))' .
            '(([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,63})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(\/[^<^) \n\r]*)?)/';
        $replacement = '">$5</a>';
        $text = \preg_replace($pattern, $replacement, $text);

        if (isset($_SERVER['HTTP_HOST'])) {
            $server = $_SERVER['HTTP_HOST'];
            if (\substr($server, 0, 4) != 'http') {
                $server = 'https://'.$server;
            }
            $replace = [
                'http://www.somda.nl', 'http://somda.nl', 'http://test.somda.nl',
                'https://somda.nl', 'https://somda.nl', 'https://test.somda.nl'
            ];
            $text = \str_replace($replace, $server, $text);
        }

        return $text;
    }

    private function replaceSmileys(string $text): string
    {
        // Replace smileys with percent-codes (%xx%)
        for ($smileyNumber = 1; $smileyNumber <= 18; ++$smileyNumber) {
            $text = \str_replace(
                '%'.\sprintf('%2d', $smileyNumber).'%',
                '<img alt="" src="/images/smileys/'.\sprintf('%2d', $smileyNumber).'.png" />',
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
            $text = \str_replace($smileyCode, '<img alt="" src="/images/smileys/'.$smileyNumber.'.png" />', $text);
        }

        return $text;
    }

    /**
     * @throws \Exception
     */
    private function replaceStaticData(string $text): string
    {
        $locations = $this->static_data_helper->getLocations();
        $users = $this->static_data_helper->getUsers();
        $routes = $this->static_data_helper->getRoutes();

        $text_chunks = \array_unique(\array_diff(\str_word_count(\strip_tags($text), 2, '@0123456789'), ['nbsp']));
        foreach ($text_chunks as $chunk) {
            $word = \trim($chunk);
            if (isset($locations[$word])) {
                $text = \preg_replace(
                    self::REPLACE_WORD_START.$word.self::REPLACE_WORD_END,
                    '\\1<!-- s\\2 --><span class="tooltip" title="' .
                    \strtolower(\htmlspecialchars($locations[$word])).'">\\2<!-- s\\2 --></span>\\3',
                    $text
                );
            }
        }
        foreach ($text_chunks as $chunk) {
            $word = \trim($chunk);
            if (isset($users[$word])) {
                $text = \preg_replace(
                    self::REPLACE_WORD_START.$word.self::REPLACE_WORD_END,
                    '\\1<!-- s\\2 --><span class="tooltip" title="Somda gebruiker ' .
                        \htmlspecialchars($users[$word]).'">'.\substr($word, 1).'<!-- \\2 --></span>\\3',
                    $text
                );
            } elseif (isset($routes[$word])) {
                $text = \preg_replace(
                    self::REPLACE_WORD_START.$word.self::REPLACE_WORD_END,
                    '\\1<!-- s\\2 --><span class="tooltip" title="'.\htmlspecialchars($routes[$word]) .
                        '">\\2<!-- s\\2 --></span>\\3',
                    $text
                );
            }
        }

        return $text;
    }
}
