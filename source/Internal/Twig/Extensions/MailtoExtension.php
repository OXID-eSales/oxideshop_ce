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
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('mailto', [$this, 'mailto'], ['is_safe' => ['html']])
        ];
    }

    public function mailto($address, array $parameters = [])
    {
        $extra = '';
        $text = $address;
        // Netscape and Mozilla do not decode %40 (@) in BCC field (bug?), so don't encode it.
        $search = ['%40', '%2C'];
        $replace = ['@', ','];
        $mailParameters = [];

        foreach ($parameters as $var => $value) {
            switch ($var) {
                case 'cc':
                case 'bcc':
                case 'followupto':
                    if (!empty($value))
                        $mailParameters[] = $var . '=' . str_replace($search, $replace, rawurlencode($value));
                    break;

                case 'subject':
                case 'newsgroups':
                    $mailParameters[] = $var . '=' . rawurlencode($value);
                    break;

                case 'extra':
                case 'text':
                    $$var = $value;
            }
        }

        $mailParametersString = '';
        for ($i = 0; $i < count($mailParameters); $i++) {
            $mailParametersString .= (0 == $i) ? '?' : '&';
            $mailParametersString .= $mailParameters[$i];
        }
        $address .= $mailParametersString;

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
     * @param $address
     * @param $text
     * @param $extra
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
     * Encode using
     *
     * @param $address
     * @param $text
     * @param $extra
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
     * @param $address
     * @param $text
     * @param $extra
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

        return "<a href=\"$mailto$addressEncode\"$extra>$textEncode</a>";
    }
}