<?php

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class MailtoExtension
 */
class MailtoExtension extends AbstractExtension
{

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('mailto', [$this, 'mailto'], ['is_safe' => ['html']]),
            new TwigFunction('oxmailto', [$this, 'oxmailto'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Twig mailto function imported from Smarty
     *
     * @param string $address
     * @param array  $parameters
     *
     * @return string
     */
    public function mailto($address, array $parameters = [])
    {
        $text = isset($parameters['text']) ? $parameters['text'] : $address;
        $extra = isset($parameters['extra']) ? $parameters['extra'] : '';

        $address .= $this->prepareMailParametersString($parameters);

        $encode = (empty($parameters['encode'])) ? 'none' : $parameters['encode'];
        if (!in_array($encode, ['javascript', 'javascript_charcode', 'hex', 'none'])) {
            throw new \Twig_Error_Runtime("mailto: 'encode' parameter must be none, javascript or hex");
        }

        switch ($encode) {
            case 'javascript':
                return $this->mailJavascript($address, $text, $extra);

            case 'javascript_charcode':
                return $this->mailJavascriptCharcode($address, $text, $extra);

            case 'hex':
                return $this->mailHex($address, $text, $extra);

            default:
                // no encoding
                return "<a href=\"mailto:$address\" $extra>$text</a>";
        }
    }

    /**
     * @param string $address
     * @param array  $parameters
     *
     * @return string
     */
    public function oxmailto($address, array $parameters = [])
    {
        if (isset($parameters['encode']) && $parameters['encode'] == 'javascript') {
            $text = isset($parameters['text']) ? $parameters['text'] : $address;
            $extra = isset($parameters['extra']) ? $parameters['extra'] : '';

            $address .= $this->prepareMailParametersString($parameters);

            $string = "document.write('<a href=\"mailto:$address\" $extra>$text</a>');";
            $encodedString = "%" . wordwrap(current(unpack("H*", $string)), 2, "%", true);

            return "<script type=\"text/javascript\">eval(decodeURIComponent('$encodedString'))</script>";
        } else {
            return $this->mailto($address, $parameters);
        }
    }

    /**
     * @param array $parameters
     *
     * @return string
     */
    private function prepareMailParametersString($parameters)
    {
        // Netscape and Mozilla do not decode %40 (@) in BCC field (bug?), so don't encode it.
        $search = ['%40', '%2C'];
        $replace = ['@', ','];

        $mailParameters = [];
        foreach ($parameters as $var => $value) {
            switch ($var) {
                case 'cc':
                case 'bcc':
                case 'followupto':
                    if ($value) {
                        $mailParameters[] = $var . '=' . str_replace($search, $replace, rawurlencode($value));
                    }
                    break;
                case 'subject':
                case 'newsgroups':
                    $mailParameters[] = $var . '=' . rawurlencode($value);
                    break;
            }
        }

        $parametersString = '';
        if (count($mailParameters)) {
            $parametersString = '?' . implode('&', $mailParameters);
        }

        return $parametersString;
    }

    /**
     * Encode using javascript
     *
     * @param string $address
     * @param string $text
     * @param string $extra
     *
     * @return string
     */
    private function mailJavascript($address, $text, $extra)
    {
        $string = "document.write('<a href=\"mailto:$address\" $extra>$text</a>');";

        $jsEncode = '';
        for ($x = 0; $x < strlen($string); $x++) {
            $jsEncode .= '%' . bin2hex($string[$x]);
        }

        return "<script type=\"text/javascript\">eval(unescape('$jsEncode'))</script>";
    }

    /**
     * Encode using charcode
     *
     * @param string $address
     * @param string $text
     * @param string $extra
     *
     * @return string
     */
    private function mailJavascriptCharcode($address, $text, $extra)
    {
        $string = "<a href=\"mailto:$address\" $extra>$text</a>";

        $ord = [];
        for ($x = 0, $y = strlen($string); $x < $y; $x++) {
            $ord[] = ord($string[$x]);
        }

        return
            "<script type=\"text/javascript\" language=\"javascript\">\n"
            . "<!--\n"
            . "{document.write(String.fromCharCode(" . implode(',', $ord) . "))}\n"
            . "//-->\n"
            . "</script>";
    }

    /**
     * Encode using hex
     *
     * @param string $address
     * @param string $text
     * @param string $extra
     *
     * @return string
     */
    private function mailHex($address, $text, $extra)
    {
        $match = [];
        preg_match('!^(.*)(\?.*)$!', $address, $match);

        if (!empty($match[2])) {
            throw new \Twig_Error_Runtime("mailto: hex encoding does not work with extra attributes. Try javascript.");
        }

        $addressEncode = '';
        for ($x = 0; $x < strlen($address); $x++) {
            if (preg_match('!\w!', $address[$x])) {
                $addressEncode .= '%' . bin2hex($address[$x]);
            } else {
                $addressEncode .= $address[$x];
            }
        }

        $textEncode = '';
        for ($x = 0; $x < strlen($text); $x++) {
            $textEncode .= '&#x' . bin2hex($text[$x]) . ';';
        }

        $mailto = "&#109;&#97;&#105;&#108;&#116;&#111;&#58;";

        return "<a href=\"$mailto$addressEncode\" $extra>$textEncode</a>";
    }
}
