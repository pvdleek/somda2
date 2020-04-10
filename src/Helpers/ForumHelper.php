<?php

namespace App\Helpers;

use App\Entity\ForumPost;
use App\Entity\Jargon;
use App\Entity\Location;
use App\Entity\TrainTable;
use App\Entity\TrainTableYear;
use App\Entity\User;
use App\Traits\DateTrait;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Exception;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Extension\RuntimeExtensionInterface;

class ForumHelper implements RuntimeExtensionInterface
{
    use DateTrait;

    private const ALLOWED_HTML_TAGS = '<p><a><img><ul><ol><li><blockquote><strong><em><s><hr>';

    /**
     * @var ManagerRegistry
     */
    private $doctrine;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var array
     */
    private $locations = null;

    /**
     * @var array
     */
    private $users = null;

    /**
     * @var array
     */
    private $routes = null;

    /**
     * @param ManagerRegistry $doctrine
     * @param TranslatorInterface $translator
     */
    public function __construct(ManagerRegistry $doctrine, TranslatorInterface $translator)
    {
        $this->doctrine = $doctrine;
        $this->translator = $translator;
    }

    /**
     * @param ForumPost $post
     * @return string
     */
    public function getDisplayForumPost(ForumPost $post): string
    {
        if ($post->getText()->isNewStyle()) {
            $text = strip_tags(
                str_replace(['&nbsp;', "\r\n", '<p>&nbsp;</p>'], ' ', $post->getText()->getText()),
                self::ALLOWED_HTML_TAGS
            );
        } else {
            $text = $this->doSpecialText($post->getText()->getText());
        }
        $text = nl2br($this->replaceLocationsAndUsers($text));

        if (!is_null($post->getEditTimestamp())) {
            $text .= '<br /><br /><i><span class="edit_text">Laatst bewerkt door ' . $post->getEditor()->getUsername() .
                ' op ' . $post->getEditTimestamp()->format('d-m-Y H:i').
                (strlen($post->getEditReason()) > 0 ? ', reden: ' . $post->getEditReason() : '') . '</span></i>';
        }
        if ($post->isSignatureOn() && strlen($post->getAuthor()->getInfo()->getInfo()) > 0) {
            $text .= '<br /><hr style="margin-left:0; width:15%;" />' . $post->getAuthor()->getInfo()->getInfo();
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
     * @return string
     */
    function doSpecialText(string $text): string
    {
        // Put a space before all unquotes or else they can give a Javascript error in IE (if proceeded by a link)
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
     * @param string $text
     * @return string
     */
    private function doLinksAndSmileys(string $text): string
    {
        $in = '/((((http|https|ftp|ftps)\:\/\/))(([a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,4})|([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}))(\/[^) \n\r]*)?)/';
        $out = '<a href="\1" rel="nofollow" target="_blank">\5</a>';
        $text = preg_replace($in, $out, $text);
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

        // Replace smileys with percent-codes (%xx%)
        for ($smileyNumber = 1; $smileyNumber <= 18; ++$smileyNumber) {
            $text = str_replace(
                '%' . sprintf('%2d', $smileyNumber) . '%',
                '<img alt="" src="/images/smileys/' . sprintf('%2d', $smileyNumber) . '" />',
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
     *
     */
    private function loadStaticData(): void
    {
        if (!is_null($this->locations)) {
            return;
        }

        // Get all locations
        $locations = $this->doctrine->getRepository(Location::class)->findAll();
        foreach ($locations as $location) {
            $this->locations[$location->getName()] = $location->getDescription();
        }
        $jargons = $this->doctrine->getRepository(Jargon::class)->findAll();
        foreach ($jargons as $jargon) {
            $this->locations[$jargon->getTerm()] = $jargon->getDescription();
        }

        $users = $this->doctrine->getRepository(User::class)->findBy(['active' => true]);
        foreach ($users as $user) {
            $this->users['@' . $user->getUsername()] =
                strlen($user->getName()) > 0 ? $user->getName() : $user->getUsername();
        }

        $routes = $this->doctrine->getRepository(TrainTable::class)->findAllTrainTablesForForum(
            $this->getDefaultTrainTableYear()
        );
        foreach ($routes as $route) {
            $this->routes[$route['routeNumber']] = 'Trein ' . $route['routeNumber'] . ' rijdt als ' .
                $route['characteristicName'] . ' (' . $route['characteristicDescription'] . ') voor ' .
                $route['transporter'] . ' van ' . $route['firstLocation'] . ' (' .
                $this->timeDatabaseToDisplay($route['firstTime']) . ') tot ' . $route['lastLocation'] . ' (' .
                $this->timeDatabaseToDisplay($route['lastTime']) . ')';

            $seriesCount = [];
            $seriesRouteNumber = 100 * (int)($route['routeNumber'] / 100);
            if (!isset($this->routes[$seriesRouteNumber])
                || !isset($seriesCount[$seriesRouteNumber])
                || $seriesCount[$seriesRouteNumber] < 2
            ) {
                if (!isset($seriesCount[$seriesRouteNumber])) {
                    $seriesCount[$seriesRouteNumber] = 1;
                } else {
                    ++$seriesCount[$seriesRouteNumber];
                }
                if (strlen($route['section']) > 0) {
                    $this->routes[$seriesRouteNumber] = 'Treinserie ' . $seriesRouteNumber . ' rijdt als ' .
                        $route['characteristicName'] . ' (' . $route['characteristicDescription'] . ') voor ' .
                        $route['transporter'] . ' over traject ' . $route['section'];
                } else {
                    $this->routes[$seriesRouteNumber] = 'De ' .
                        ($seriesCount[$seriesRouteNumber] === 1 ? 'eerste' : 'tweede') . ' trein (' .
                        $route['routeNumber'] . ') van serie ' . $seriesRouteNumber . ' rijdt als ' .
                        $route['characteristicName'] . ' (' . $route['characteristicDescription'] . ') voor ' .
                        $route['transporter'] . ' van ' . $route['firstLocation'] . ' (' .
                        $this->timeDatabaseToDisplay($route['firstTime']) . ') tot ' . $route['lastLocation'] . ' (' .
                        $this->timeDatabaseToDisplay($route['lastTime']) . ')';
                }
            }
        }
    }

    /**
     * @return TrainTableYear
     * @throws Exception
     */
    private function getDefaultTrainTableYear(): TrainTableYear
    {
        $trainTableYears = $this->doctrine->getRepository(TrainTableYear::class)->findAll();
        foreach ($trainTableYears as $trainTableYear) {
            if ($trainTableYear->getStartDate() <= new DateTime() && $trainTableYear->getEndDate() >= new DateTime()) {
                return $trainTableYear;
            }
        }
        return $trainTableYears[0];
    }

    /**
     * @param string $text
     * @return string
     */
    private function replaceLocationsAndUsers(string $text): string
    {
        $this->loadStaticData();

        $locationsDone = [];
        $usersDone = [];

        $textChunks = array_diff(str_word_count(strip_tags($text), 2, '@0123456789'), ['nbsp']);
        foreach ($textChunks as $chunk) {
            $word = trim($chunk);
            if (preg_match('/^[A-Z]{1}[A-Za-z]*$/', $word)) {
                // Match on an abbreviation (uppercase character followed by a 0 or more lowercase characters)
                if (!isset($locationsDone[$word]) && isset($this->locations[$word])) {
                    $text = preg_replace(
                        '/(^|[<\s.-?:;().-\/\[\]])(' . $word . ')($|[<\s,-?:;().-\/\[\]])/m',
                        '\\1<!-- s\\2 --><span class="tooltip" title="' .
                            strtolower(htmlspecialchars($this->locations[$word])) . '">\\2<!-- s\\2 --></span>\\3',
                        $text
                    );
                    $locationsDone[$word] = true;
                }
            } elseif (!isset($usersDone[$word]) && isset($this->users[$word])) {
                $text = preg_replace(
                    '/(^|[<\s.-?:;().-\/\[\]])(' . $word . ')($|[<\s,-?:;().-\/\[\]])/m',
                    '\\1<!-- s\\2 --><span class="tooltip" title="Somda gebruiker ' .
                        htmlspecialchars($this->users[$word]) . '">' . substr($word, 1) . '<!-- \\2 --></span>\\3',
                    $text
                );
                $usersDone[$word] = true;
            }
        }

        $routeDone = [];
        foreach ($textChunks as $chunk) {
            $word = trim($chunk);
            if (!isset($routeDone[$word]) && isset($this->routes[$word])) {
                $text = preg_replace(
                    '/(^|[<\s.-?:;().-\/\[\]])(' . $word . ')($|[<\s,-?:;().-\/\[\]])/m',
                    '\\1<!-- s\\2 --><span class="tooltip" title="' . htmlspecialchars($this->routes[$word]) .
                        '">\\2<!-- s\\2 --></span>\\3',
                    $text,
                    1
                );
                $routeDone[$word] = true;
            }
        }

        return $text;
    }

    /**
     * @param string $text
     * @param string $tags
     * @return string
     */
    private function stripTagsAndContent(string $text, string $tags = ''): string
    {
        preg_match_all('/<(.+?)[\s]*\/?[\s]*>/si', trim($tags), $tags);
        $tags = array_unique($tags[1]);

        if (is_array($tags) && count($tags) > 0) {
            return preg_replace('@<(?!(?:' . implode('|', $tags) . ')\b)(\w+)\b.*?>.*?</\1>@si', '', $text);
        } else {
            return preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $text);
        }
    }
}
