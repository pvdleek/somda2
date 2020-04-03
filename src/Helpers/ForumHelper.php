<?php

namespace App\Helpers;

use App\Entity\ForumPost;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class ForumHelper implements RuntimeExtensionInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @param TranslatorInterface $translator
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    /**
     * @param ForumPost $post
     * @return string
     */
    public function getDisplayForumPost(ForumPost $post): string
    {
        $text = nl2br($this->doVerkortingenEnUsernames($this->doSpecialText($post->getText()->getText())));

        if (!is_null($post->getEditTimestamp())) {
            $text .= '<br /><br /><i><span class="edit_text">Laatst bewerkt door ' . $post->getEditor()->getUsername() .
                ' op ' . $post->getEditTimestamp()->format('d-m-Y H:i').
                (strlen($post->getEditReason()) > 0 ? ', reden: ' . $post->getEditReason() : '') . '</span></i>';
        }
        if ($post->isSignatureOn() && strlen($post->getAuthor()->getInfo()->getInfo()) > 0) {
            $text .= '<br /><hr align="left" width="15%" />' . $post->getAuthor()->getInfo()->getInfo();
        }
//        if (isset($highlight) && strlen($highlight) > 0) {
//            $text = doHighlight($text, $highlight);
//        }

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
     * @param string $class
     * @return string|string[]|null
     */
    private function doLinksAndSmileys(string $text, string $class = '')
    {
        // Make the links clickable
        if (strlen($class) > 0) {
            $classText = ' class="' . $class . '"';
        } else {
            $classText = '';
        }

        $in = '/((((http|https|ftp|ftps)\:\/\/))(([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(\/[^) \n\r]*)?)/';
        $out = '<a href="\1" rel="nofollow" target="_blank">\5</a>';
        $text = preg_replace($in, $out, $text);
        if (isset($_SERVER['HTTP_HOST'])) {
            $server = $_SERVER['HTTP_HOST'];
            if (substr($server, 0, 4) != 'http') {
                $server = 'https://' . $server;
            }
            $text = str_replace(['http://www.somda.nl', 'http://somda.nl', 'http://test.somda.nl'], $server, $text);
        }

        // Create an array of possible smileys
        $smileys = [
            ' =>' => '01',
            ' :)' => '02',
            ' :-)' => '02',
            ' :s' => '03',
            ' :-s' => '03',
            ' :S' => '03',
            ' :-S' => '03',
            ' :d' => '08',
            ' :-d' => '08',
            ' :D' => '08',
            ' :-D' => '08',
            ' :p' => '11',
            ' :-p' => '11',
            ' :P' => '11',
            ' :-P' => '11',
            ' :$' => '12',
            ' :-$' => '12',
            ' :(' => '14',
            ' :-(' => '14',
            ' :o' => '16',
            ' :-o' => '16',
            ' :O' => '16',
            ' :-O' => '16',
            ' ;)' => '17',
            ' ;-)' => '17'
        ];
/*
        // Replace smileys with percent-codes (%xx%)
        $path = '../../public/images/smileys';
        $handle = @opendir($path);
        while ($file = @readdir($handle)) {
            if (is_file($path . $file) && $file != '.' && $file != '..') {
                if (substr($path . $file, -4) == '.gif') {
                    $size = getimagesize(geefConfig('images_location') . '/smileys/' . $file);
                    $text = str_replace('%' . substr($file, 0, 2) . '%', '<img src="/smileys/' .
                        $file . '" ' . $size[3] . ' alt="" />', $text);
                }
            }
        }
        // Replace smileys with the above standard codes (e.g. :-D)
        foreach ($smileys as $smileyCode => $smileyNumber) {
            $size = getimagesize('../../public/images/smileys/' . $smileyNumber . '.gif');
            $text = str_replace($smileyCode, '<img src="/smileys/' . $smileyNumber . '.gif" ' .
                $size[3] . ' alt="" />', $text);
        }
*/
        foreach (['b' => 'strong', 'i' => 'em'] as $item => $replacement) {
            // Turn uppercase bold codes into lowercase
            $text = str_replace(
                ['%' . strtoupper($item) . '%', '%/' . strtoupper($item) . '%'],
                ['%' . strtoupper($item) . '%', '%/' . strtoupper($item) . '%'],
                $text
            );

            // Replace items by their correct HTML tags
            $numberOfItems = 0;
            $numberOfItemsDone = 0;
            while (strpos($text, '%' . $item . '%') !== false) {
                $text = preg_replace('[%' . $item . '%]', '<' . $replacement . '>', $text, 1);
                ++$numberOfItems;
            }
            while (strpos($text, '%/' . $item . '%') !== false) {
                $text = preg_replace('[%/' . $item . '%]', '</' . $replacement . '>', $text, 1);
                ++$numberOfItemsDone;
            }

            // Put an extra bold up front if necessary
            $extraItems = $numberOfItemsDone - $numberOfItems;
            for ($doExtraItem = 0; $doExtraItem < $extraItems; ++$doExtraItem) {
                $text = '<' . $replacement . '>' . $text;
            }
            // Put an extra bold-close after the text if necessary
            $extraItemsDone = $numberOfItems - $numberOfItemsDone;
            for ($doExtraItem = 0; $doExtraItem < $extraItemsDone; ++$doExtraItem) {
                $text .= '</' . $replacement . '>';
            }
        }

        return $text;
    }

    /**
     * @param string $text
     * @return string
     */
    function doSpecialText(string $text): string
    {
        // Put a space before all unquotes or else they can give a Javascript error in IE (if procedeed by a link)
        $text = str_replace('%unquote%', ' %unquote%', $text);

        $text = $this->doLinksAndSmileys($text);

        // Change the BB codes (limited to [b], [i], [u], [s], [code] and [color]
        $bbPatterns = [
            '/\[b\](.+)\[\/b\]/Uis',
            '/\[i\](.+)\[\/i\]/Uis',
            '/\[u\](.+)\[\/u\]/Uis',
            '/\[s\](.+)\[\/s\]/Uis',
            '/\[code\](.+)\[\/code\]/Uis',
            '/\[color=(\#[0-9a-f]{6}|[a-z]+)\](.+)\[\/color\]/Ui',
            '/\[color=(\#[0-9a-f]{6}|[a-z]+)\](.+)\[\/color\]/Uis'
        ];
        $bbReplacements = [
            '<b>\1</b>',
            '<i>\1</i>',
            '<u>\1</u>',
            '<s>\1</s>',
            '<pre>\1</pre>',
            '<span style = "color: \1;">\2</span>',
            '<div style = "color: \1;">\2</div>'
        ];
        $text = preg_replace($bbPatterns, $bbReplacements, $text);

        // Remove all whitespace before %quote%
        $parts = explode('%quote%', $text);
        $partCount = count($parts);
        if ($partCount > 1) {
            for ($part = 0; $part < $partCount; ++$part) {
                $parts[$part] = str_replace('<br>', '<br />', $parts[$part]);
                $brs = explode('<br />', $parts[$part]);
                $startBr = -1;
                $brCount = count($brs);
                for ($br = 0; $br < $brCount; ++$br) {
                    if ($startBr < 0 && strlen($brs[$br]) > 0) {
                        $startBr = $br;
                    }
                }
                if ($startBr < 0) {
                    $startBr = 0;
                }
                $parts[$part] = '';
                for ($br = $startBr; $br < $brCount; ++$br) {
                    if (strlen($parts[$part]) > 0) {
                        $parts[$part] .= '<br />';
                    }
                    $parts[$part] .= trim($brs[$br]);
                }
            }
            $text = implode('%quote%', $parts);
        } else {
            $text = $parts[0];
        }

        // Remove all whitespace after %unquote%
        $parts = explode('%unquote%', $text);
        $partCount = count($parts);
        if ($partCount > 1) {
            for ($part = 0; $part < $partCount; ++$part) {
                $parts[$part] = str_replace('<br>', '<br />', $parts[$part]);
                $brs = explode('<br />', $parts[$part]);
                $startBr = -1;
                $brCount = count($brs);
                for ($br = 0; $br < $brCount; ++$br) {
                    if ($startBr < 0 && strlen($brs[$br]) > 0) {
                        $startBr = $br;
                    }
                }
                if ($startBr < 0) {
                    $startBr = 0;
                }
                $parts[$part] = '';
                for ($br = $startBr; $br < $brCount; ++$br) {
                    if (strlen($parts[$part]) > 0) {
                        $parts[$part] .= '<br />';
                    }
                    $parts[$part] .= trim($brs[$br]);
                }
            }
            $text = implode('%unquote%', $parts);
        } else {
            $text = $parts[0];
        }

        // Replace %quote% and %unquote% with their correct HTML tags
        $numberOfQuote = 0;
        $numberOfUnquote = 0;
        while (strpos($text, '%quote%') !== false) {
            $text = preg_replace(
                '[%quote%]',
                '<blockquote><span><font size="1"><b>Quote' . '</b></font></span><hr />',
                $text,
                1
            );
            ++$numberOfQuote;
        }
        while (strpos($text, '%unquote%') !== false) {
            $text = preg_replace('[%unquote%]', '<hr /></blockquote> ', $text, 1);
            ++$numberOfUnquote;
        }
        // Zet quotes vooraan als er teveel unquotes zijn
        $doQuotes = $numberOfUnquote - $numberOfQuote;
        for ($doQuote = 0; $doQuote < $doQuotes; ++$doQuote) {
            $text = '<blockquote><span><font size="1"><strong>Quote' . '</strong></font></span><hr />' . $text;
        }
        // Zet unquotes achteraan als er teveel quotes zijn
        $doQuotes = $numberOfQuote - $numberOfUnquote;
        for ($doUnquote = 0; $doUnquote < $doQuotes; ++$doUnquote) {
            $text .= ' <hr /></blockquote>';
        }

        // Change all ampersands (&) into &amp;
        $text = str_replace('&', '&amp;', $text);

        return $text;
    }

    /**
     * @param $text
     * @param bool $do_usernames
     * @return string|string[]|null
     */
    private function doVerkortingenEnUsernames($text, $do_usernames = true)
    {
        return $text;
        global $session, $verk_array, $user_array, $treinnr_array;

        $verk_done = [];
        $user_done = [];
        $text_without_html = strip_tags_and_content($text);
        $text_chunks = preg_split("/[<\s,!?:;().\/\[\]]+/", $text_without_html);
        foreach ($text_chunks as $chunk) {
            $woord = trim($chunk);
            if (preg_match('/^[A-Z]{1}[A-Za-z]*$/', $woord)) {
                // Match op een verkorting (hoofdletter gevolgd door 0 of meer kleine letters)
                if (!isset($verk_done[$woord]) && isset($verk_array[$woord])) {
                    $text = preg_replace('/(^|[<\s.-?:;().-\/\[\]])(' . $woord . ')($|[<\s,-?:;().-\/\[\]])/m', '\\1<!-- s\\2 --><span class="tooltip" title="' . strtolower(htmlspecialchars($verk_array[$woord])) . '">\\2<!-- s\\2 --></span>\\3', $text);
                    $verk_done[$woord] = true;
                }
            } elseif ($do_usernames && $session && $session->logged_in) {
                // Match op een username (start met @)
                if (!isset($user_done[$woord]) && isset($user_array[$woord])) {
                    $text = preg_replace('/(^|[<\s.-?:;().-\/\[\]])(' . $woord . ')($|[<\s,-?:;().-\/\[\]])/m', '\\1<!-- s\\2 --><span class="tooltip" title="Somda gebruiker ' . htmlspecialchars($user_array[$woord]) . '">' . substr($woord, 1) . '<!-- \\2 --></span>\\3', $text);
                    $user_done[$woord] = true;
                }
            }
        }

        $treinnr_done = array();
        $text_without_html = strip_tags_and_content($text, 'a');
        $text_chunks = preg_split("/[<\s,!?:;().\/\[\]]+/", $text_without_html);
        foreach ($text_chunks as $chunk) {
            $woord = trim($chunk);
            if (!isset($treinnr_done[$woord]) && isset($treinnr_array[$woord])) {
                if (substr_count($text, $woord) !== substr_count($text_without_html, $woord)) {
                    // Oppassen, het treinnummer komt namelijk ook ergens in een HTML tag voor
                    if (strpos($text, $woord) == strpos($text_without_html, $woord)) {
                        // Treinnummer komt voor de eerste HTML tag, dus we kunnen veilig vervangen
                        $text = preg_replace('/(^|[<\s.-?:;().-\/\[\]])(' . $woord . ')($|[<\s,-?:;().-\/\[\]])/m', '\\1<!-- s\\2 --><span class="tooltip" title="' . htmlspecialchars($treinnr_array[$woord]) . '">\\2<!-- s\\2 --></span>\\3', $text, 1);
                    } else {
                        // Treinnummer zit sowieso niet voor de HTML, dus om te voorkomen dat het mis gaat doen we deze maar niet
                    }
                } else {
                    $text = preg_replace('/(^|[<\s.-?:;().-\/\[\]])(' . $woord . ')($|[<\s,-?:;().-\/\[\]])/m', '\\1<!-- s\\2 --><span class="tooltip" title="' . htmlspecialchars($treinnr_array[$woord]) . '">\\2<!-- s\\2 --></span>\\3', $text, 1);
                }
                $treinnr_done[$woord] = true;
            }
        }

        return $text;
    }

    function strip_tags_and_content($text, $tags = '', $invert = false)
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) && count($tags) > 0) {
            if ($invert) {
                return preg_replace('@<(' . implode('|', $tags) . ')\b.*?>.*?</\1>@si', '', $text);
            } else {
                return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
            }
        } elseif (!$invert) {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
        return $text;
    }
}
