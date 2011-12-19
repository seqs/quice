<?php

namespace Quice\Http;

class Response
{

    /**
     * List of all known HTTP response codes
     *
     * @var array
     */
    protected static $messages = array(
        // Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',

        // Success 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',

        // Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',  // 1.1
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        // 306 is deprecated but reserved
        307 => 'Temporary Redirect',

        // Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',

        // Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        509 => 'Bandwidth Limit Exceeded'
    );

    /**
     * Default response code
     */
    protected $responseCode = 200;

    /**
     * Custom response message
     */
    protected $customResponseMessage = null;

    /**
     * Cookie
     */
    protected $cookies = array();

    /**
     * Headers (except Cookies)
     */
    protected $headers = array();

    /**
     * Output
     */
    protected $output = '';

    /**
     * Renderer
     */
    public $renderer = null;

    /**
     * Exception
     */
    public $exception = null;

    /**
     * Context
     */
    public $context = null;

    /**
     * Singleton instance
     *
     * Marked only as protected to allow extension of the class. To extend,
     * simply override {@link getInstance()}.
     *
     * @var Response
     */
    protected static $instance = null;

    /**
     * Singleton instance
     *
     * @return Response
     */
    public static function getInstance()
    {
        if (null === self::$instance) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Sets response code
     *
     * @param int $code
     * @return Response
     */
    public function setResponseCode($code)
    {
        $this->responseCode = $code;
        return $this;
    }

    /**
     * Check response code exists
     *
     * @param int $code
     * @return boolean
     */
    public function hasResponseCode($code)
    {
        return isset(self::$messages[$code]);
    }

    /**
     * Set custom response message
     *
     * @param string $message
     * @return Response
     */
    public function setCustomResponseMessage($message)
    {
        $this->customResponseMessage = $message;
        return $this;
    }

    /**
     * Send HTTP response code
     *
     * @return bool
     */
    public function sendResponseCode()
    {
        if (!$this->canSendHeaders()) {
            return false;
        }

        $headerMessage = (string) $this->responseCode;

        if (null != $this->customResponseMessage) {
            $headerMessage .= ' ' . $this->customResponseMessage;
        } elseif (isset(self::$messages[$this->responseCode])) {
            $headerMessage .= ' ' . self::$messages[$this->responseCode];
        }
        header('HTTP/1.x ' . $headerMessage);
        return true;
    }

    /**
     * Set cookie
     *
     * @param string $name
     * @param string $value
     * @param string $expire
     * @param string $path
     * @param string $domain
     * @param bool $secure
     * @param bool $httponly
     * @return Response
     */
    public function setCookie($name, $value = '', $expire = null, $path = null, $domain = null, $secure = false, $httponly = false)
    {
        $name = $name;
        $this->cookies[$name] = array(
            'value'    => $value,
            'expire'   => $expire,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => $secure,
            'httponly' => $httponly,
        );
        return $this;
    }

    /**
     * Remove cookie
     *
     * @param string $name
     * @param string $path
     * @param string $domain
     * @return Response
     */
    public function removeCookie($name, $path = null, $domain = null)
    {
        $name = $name;
        $this->cookies[$name] = array(
            'value'    => '',
            'expire'   => null,
            'path'     => $path,
            'domain'   => $domain,
            'secure'   => false,
            'httponly' => false,
        );
        return $this;
    }

    /**
     * Sends cookies to client.
     */
    public function sendCookies()
    {
        if(count($this->cookies) < 1)
            return;
        foreach ($this->cookies as $name => $jar) {
            setcookie($name, $jar['value'], $jar['expire'], $jar['path'], $jar['domain'], $jar['secure'], $jar['httponly']);
        }

    }

    /**
     * Clears set cookies
     *
     * @return Response
     */
    public function clearCookies()
    {
        $this->cookies = array();
        return $this;
    }

    /**
     * Get cookies data
     *
     * @return array
     */
    public function getCookies()
    {
        return $this->cookies;
    }

    /**
     * Add a header element
     *
     * @param string $header
     * @return Response
     */
    public function setHeader($key, $value)
    {
        $this->headers[$key] = $value;
        return $this;
    }

    public function removeHeader($key)
    {
        if (isset($this->headers[$key])) {
            unset($this->headers[$key]);
        }
        return $this;
    }

    /**
     * Returns headers data
     *
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * Clear headers data
     *
     * @return Response
     */
    public function clearHeaders()
    {
        $this->headers = array();
        return $this;
    }

    /**
     * Can send header or not?
     * If the output was already sent, this function return false
     *
     * @return bool
     */
    public function canSendHeaders()
    {
        return !headers_sent();
    }

    /**
     * Send headers to clients
     *
     * @return Response
     */
    public function sendHeaders()
    {
        foreach ($this->headers as $key => $value) {
            header($key. ': ' . $value);
        }
        return $this;
    }

    /**
     * Redirect to given url
     *
     * @return void
     */
    public function redirect($url)
    {
        $this->setHeader('Location', $url);
        $this->sendCookies();
        $this->sendHeaders();
        exit();
    }

    /**
     * Compress output content
     *
     * @return string $data
     */
    private function compress($data, $level = 4)
    {
        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) {
            $encoding = 'gzip';
        }

        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'], 'x-gzip')) {
            $encoding = 'x-gzip';
        }

        if (!isset($encoding)) {
            return $data;
        }

        if (!extension_loaded('zlib') || ini_get('zlib.output_compression')) {
            return $data;
        }

        if (headers_sent()) {
            return $data;
        }

        if (connection_status()) {
            return $data;
        }

        $size = strlen($data);
        $crc = crc32($data);

        $gzdata = "\x1f\x8b\x08\x00\x00\x00\x00\x00";
        $gzdata .= gzcompress($data, (int)$level);

        $gzdata = substr($gzdata, 0, strlen($gzdata) - 4);
        $gzdata .= pack("V", $crc) . pack("V", $size);

        $gzdata = gzencode($data, (int)$level);

        $this->addHeader('Content-Encoding', $encoding);

        return $gzdata;
    }

    /**
     * Set output
     *
     * @param string $output
     * @param bool $join If true, the $output will join with the current output string
     * @return Response
     */
    public function setOutput($output, $join = false)
    {
        if (!$join) {
            $this->output = $output;
        } else {
            $this->output .= $output;
        }
        return $this;
    }

    /**
     * Send output to client
     *
     * @return void
     */
    public function send($output = null, $join = false)
    {
        if ($this->canSendHeaders()) {
            // $this->sendResponseCode();
            $this->sendHeaders();
            $this->sendCookies();
        }

        if ($output) {
            $this->setOutput($output, $join);
        }

        echo $this->output;
    }

    /**
     * Render content and send output
     *
     * @return void
     */
    public function render($name, $vars = array())
    {
        $this->send($this->renderer->render($name, $vars));
    }

}
