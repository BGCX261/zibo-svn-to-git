<?php

namespace zibo\library\validation\validator;

/**
 * Validator to check whether a value is a valid URL. This validator implements
 * http://www.ietf.org/rfc/rfc1738.txt
 */
class UrlValidator extends RegexValidator {

    /**
     * Code of the error when the value is not a URL
     * @var unknown_type
     */
    const CODE = 'error.validation.url';

    /**
     * Message of the error when the value is not a URL
     * @var string
     */
    const MESSAGE = 'Field is not a valid url';

    /**
     * Regular expression for a alpha value
     * @var string
     */
    protected $regexAlpha;

    /**
     * Regular expression for a digit value
     * @var string
     */
    protected $regexDigit;

    /**
     * Regular expression for a alpha or digit value
     * @var string
     */
    protected $regexAlphaDigit;

    /**
     * Regular expression for a safe value
     * @var string
     */
    protected $regexSafe;

    /**
     * Regular expression for a extra value
     * @var string
     */
    protected $regexExtra;

    /**
     * Regular expression for a national value
     * @var string
     */
    protected $regexNational;

    /**
     * Regular expression for a reserved value
     * @var string
     */
    protected $regexReserved;

    /**
     * Regular expression for a hex value
     * @var string
     */
    protected $regexHex;

    /**
     * Regular expression for a escape value
     * @var string
     */
    protected $regexEscape;

    /**
     * Regular expression for a unreserved value
     * @var string
     */
    protected $regexUnreserved;

    /**
     * Regular expression for a uchar value
     * @var string
     */
    protected $regexUchar;

    /**
     * Regular expression for a xchar value
     * @var string
     */
    protected $regexXchar;

    /**
     * Regular expression for a multiple digit values
     * @var string
     */
    protected $regexDigits;

    /**
     * Regular expression for a scheme value
     * @var string
     */
    protected $regexScheme;

    /**
     * Regular expression for a user value
     * @var string
     */
    protected $regexUser;

    /**
     * Regular expression for a password value
     * @var string
     */
    protected $regexPassword;

    /**
     * Regular expression for a top domain value
     * @var string
     */
    protected $regexTopLabel;

    /**
     * Regular expression for a domain part value
     * @var string
     */
    protected $regexDomainLabel;

    /**
     * Regular expression for a server port value
     * @var string
     */
    protected $regexPort;

    /**
     * Regular expression for a IP address value
     * @var string
     */
    protected $regexHostNumber;

    /**
     * Regular expression for a host name value
     * @var string
     */
    protected $regexHostName;

    /**
     * Regular expression for a host value
     * @var string
     */
    protected $regexHost;

    /**
     * Regular expression for a host value with optional port
     * @var string
     */
    protected $regexHostPort;

    /**
     * Regular expression for a login value
     * @var string
     */
    protected $regexLogin;

    /**
     * Regular expression for a FTP type value
     * @var string
     */
    protected $regexFtpType;

    /**
     * Regular expression for a FTP segment value
     * @var string
     */
    protected $regexFtpSegment;

    /**
     * Regular expression for a FTP path value
     * @var string
     */
    protected $regexFtpPath;

    /**
     * Regular expression for a FTP url
     * @var string
     */
    protected $regexFtp;

    /**
     * Regular expression for a file url
     * @var string
     */
    protected $regexFile;

    /**
     * Regular expression for a HTTP search value
     * @var string
     */
    protected $regexHttpSearch;

    /**
     * Regular expression for a HTTP segment value
     * @var string
     */
    protected $regexHttpSegment;

    /**
     * Regular expression for a HTTP path value
     * @var string
     */
    protected $regexHttpPath;

    /**
     * Regular expression for a HTTP url value
     * @var string
     */
    protected $regexHttp;

    /**
     * Regular expression for a URL value
     * @var string
     */
    protected $regexUrl;

    /**
     * Construct a new URL validator instance
     * @param array $options options for this instance
     * @return null
     */
    public function __construct(array $options = array()) {
        $this->initRegularExpressions();
        $options[RegexValidator::OPTION_REGEX] = '/^' . $this->regexUrl . '$/';

        parent::__construct($options);

        $this->code = self::CODE;
        $this->message = self::MESSAGE;
    }

    /**
     * Initialize the regular expressions
     * @return null
     */
    protected function initRegularExpressions() {
        $this->regexAlpha = '[a-zA-Z]';
        $this->regexDigit = '[0-9]';
        $this->regexAlphaDigit = '[a-zA-Z0-9]'; // aplha | digit
        $this->regexSafe = '[$+_.-]'; // "$" | "-" | "_" | "." | "+"
        $this->regexExtra = '[!*\'(),]'; // "!" | "*" | "'" | "(" | ")" | ","
        $this->regexNational = '([\\\\{\\\\[\\\\}\\\\|`^~\\]|])'; // "{" | "}" | "|" | "\" | "^" | "~" | "[" | "]" | "`"
        $this->regexReserved = '[;\\/?:@&=]'; // ";" | "/" | "?" | ":" | "@" | "&" | "="
        $this->regexHex = '[0-9A-Fa-f]'; // digit | "A" | "B" | "C" | "D" | "E" | "F" | "a" | "b" | "c" | "d" | "e" | "f"
        $this->regexEscape = '%' . $this->regexHex . $this->regexHex; // "%" hex hex
        $this->regexUnreserved = '(' . $this->regexAlpha . '|' . $this->regexDigit . '|' . $this->regexSafe . '|' . $this->regexExtra . ')'; // alpha | digit | safe | extra
        $this->regexUchar = '(' . $this->regexUnreserved . '|' . $this->regexEscape . ')'; // unreserved | escape
        $this->regexXchar = '(' . $this->regexUnreserved . '|' . $this->regexReserved . '|' . $this->regexEscape . ')'; // unreserved | reserved | escape
        $this->regexDigits = '(' . $this->regexDigit . ')+'; // 1*digit

        $this->regexScheme = '(' . $this->regexAlphaDigit . '|[+_.])+'; // 1*[ lowalpha | digit | "+" | "-" | "." ]
        $this->regexUser = '(' . $this->regexUchar . '|[;?&=])+'; // *[ uchar | ";" | "?" | "&" | "=" ]
        $this->regexPassword = '(' . $this->regexUchar . '|[;?&=])+'; // *[ uchar | ";" | "?" | "&" | "=" ]
        $this->regexTopLabel = '(' . $this->regexAlpha . '|' . $this->regexAlpha . '(' . $this->regexAlphaDigit . '|-)*' . $this->regexAlphaDigit . ')'; // alpha | alpha *[ alphadigit | "-" ] alphadigit
        $this->regexDomainLabel = '(' . $this->regexAlphaDigit . '|' . $this->regexAlphaDigit . '(' . $this->regexAlphaDigit . '|-)*' . $this->regexAlphaDigit . ')'; // alphadigit | alphadigit *[ alphadigit | "-" ] alphadigit
        $this->regexPort = $this->regexDigits; // digits

        $this->regexHostNumber = $this->regexDigits . '\\.' . $this->regexDigits . '\\.' . $this->regexDigits . '\\.' . $this->regexDigits; // digits "." digits "." digits "." digits
        $this->regexHostName = '(' . $this->regexDomainLabel . '\\.)*' . $this->regexTopLabel; // *[ domainlabel "." ] toplabel --> changed 1 or more to 0 or more to valid local hostnames
        $this->regexHost = '(' . $this->regexHostNumber . '|' . $this->regexHostName . ')'; // hostname | hostnumber
        $this->regexHostPort = $this->regexHost . '(:' . $this->regexPort . ')?'; // host [ ":" port ]
        $this->regexLogin = '(' . $this->regexUser . '(:' . $this->regexPassword . ')?@)?' . $this->regexHostPort; // [ user [ ":" password ] "@" ] hostport

        $this->regexFtpType = '[AIDaid]'; // "A" | "I" | "D" | "a" | "i" | "d"
        $this->regexFtpSegment = '(' . $this->regexUchar . '|[?:@&=])+'; // *[ uchar | "?" | ":" | "@" | "&" | "=" ]
        $this->regexFtpPath = $this->regexFtpSegment . '(\\/' . $this->regexFtpSegment . ')*'; // fsegment *[ "/" fsegment ]
        $this->regexFtp = 'ftp:\\/\\/' . $this->regexLogin . '(\\/' . $this->regexFtpPath . '(;type=' . $this->regexFtpType . ')?)?'; // "ftp://" login [ "/" ftppath [ ";type=" ftptype ]]

        $this->regexFile = 'file:\\/\\/(' . $this->regexHost . '|localhost)\\/' . $this->regexFtpPath;

        $this->regexHttpSearch = '(' . $this->regexUchar . '|[;:@&=\\/\\[\\]])*'; // *[ uchar | ";" | ":" | "@" | "&" | "=" ] --> added / to support friendly urls
        $this->regexHttpSegment = '(' . $this->regexUchar . '|[;:@&=])*'; // *[ uchar | ";" | ":" | "@" | "&" | "=" ]
        $this->regexHttpPath = $this->regexHttpSegment . '(\\/' . $this->regexHttpSegment . ')*'; // hsegment *[ "/" hsegment ]
        $this->regexHttp = 'http(s)?:\\/\\/' . $this->regexHostPort . '(\\/' . $this->regexHttpPath . '(\\?' . $this->regexHttpSearch . ')?)?';

        $this->regexUrl = '(' . $this->regexHttp . '|' . $this->regexFtp . '|' . $this->regexFile . ')';
    }

}