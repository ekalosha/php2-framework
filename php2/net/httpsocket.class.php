<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains PHP2 sockets class
 *
 * PHP version 5
 * @category   System Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 117 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\Net;

/**
 * PHP2 Sockets class
 *
 * @author      Eugene A. Kalosha <ekalosha@gmail.com>
 * @version     $Id: httpsocket.class.php 117 2011-02-27 15:41:23Z eugene $
 * @access      public
 * @package     PHP2\Net
 */
class PHP2_Net_HTTPSocket
{
    /**
     * HTTP methods
     */
    const METHOD_GET   = 'GET';
    const METHOD_POST  = 'POST';

    /**
     * Value of User agent for requests
     *
     * @var     string
     * @access  protected
     */
	protected $_userAgent = 'Mozilla/5.0';

    /**
     * Socket console object
     *
     * @var     PHP2_System_Console_IConsole
     * @access  protected
     */
    protected $_console;

    /**
     * Use Verbose output to the console
     *
     * @var     boolean
     * @access  protected
     */
    protected $_verbose = false;

    /**
     * Connection timeout for socket
     *
     * @var     integer
     * @access  protected
     */
    protected $_connectionTimeout = 5;

    /**
     * Read timeout for socket
     *
     * @var     integer
     * @access  protected
     */
    protected $_readTimeout = 10;

    /**
	 * Proxy server details
	 *
	 * @var     array
	 * @access  protected
	 */
	protected $_proxy;

	/**
     * Source url for Socket
     *
     * @var     string
     * @access  protected
     */
    protected $_sourceUrl;

    /**
     * Source url Info
     *
     * @var     array
     * @access  protected
     */
    protected $_sourceUrlInfo;

    /**
     * Request Headers Info
     *
     * @var     array
     * @access  protected
     */
    protected $_requestHeaders;

    /**
     * Response Headers Info
     *
     * @var     array
     * @access  protected
     */
    protected $_responseHeaders;

    /**
     * Response Headers storage for headers search
     *
     * @var     array
     * @access  protected
     */
    protected $_responseHeadersStorage;

    /**
     * Response Headers String
     *
     * @var     array
     * @access  protected
     */
    protected $_responseHeadersString;

    /**
     * Response parameters info
     *
     * @var     array
     * @access  protected
     */
    protected $_responseParameters;

    /**
     * Response Code
     *
     * @var     integer
     * @access  protected
     */
    protected $_responseCode;

    /**
     * Response Body
     *
     * @var     string
     * @access  protected
     */
    protected $_responseBody;

    /**
     * HEAD redirects count
     *
     * @var     integer
     * @access  protected
     */
    protected $_redirectsCount = 0;

    /**
     * Is Follow redirects or not
     *
     * @var     boolean
     * @access  protected
     */
    protected $_followRedirects = true;

    /**
     * Socket class constructor
     *
     * @param   string $url
     * @access  public
     */
    public function __construct($url = false)
    {
        $this->setUrl($url);

        $this->setHeader('Content-Type', 'application/x-www-form-urlencoded');
        $this->setHeader('User-Agent', $this->_userAgent);
    }

    /**
     * Setup Storage Base Path and Data Path
     *
     * @param   string $url Request url
     * @return  void
     * @access  public
     */
    public function setUrl($url)
    {
        /**
         * Clear previous response
         */
        $this->clear();

        if (!$url) return false;

        preg_match('/([\w]+\:\/\/)(.+)/', $url, $matches);
        if (!isset($matches[0])) $url = 'http://'.$url;

        if (!$urlInfo = parse_url($url)) return false;

        /**
         * Source URL processing
         */
        $this->_sourceUrl      = $url;
        $this->_sourceUrlInfo  = $urlInfo;

        if (!isset($urlInfo['path'])) $urlInfo['path'] = '/';
        $this->_sourceUrlInfo['queryString']  = ((isset($urlInfo['query']) && $urlInfo['query']) ? '?'.$urlInfo['query'] : '');
        $this->_sourceUrlInfo['queryPath']    = str_replace(array('%2F', '%3A'), array('/', ':'), rawurlencode($urlInfo['path'])).(($this->_sourceUrlInfo['queryString']) ? $this->_sourceUrlInfo['queryString'] : '');
        if (!isset($urlInfo['port']))
        {
            switch ($urlInfo['scheme'])
            {
                case 'https':
                    $this->_sourceUrlInfo['port'] = 443;
                break;

                default:
                    $this->_sourceUrlInfo['port'] = 80;
                break;
            }
        }

        /**
         * Set socket host
         */
        switch ($urlInfo['scheme'])
        {
            case 'https':
                $this->_sourceUrlInfo['socketHost'] = 'ssl://'.$this->_sourceUrlInfo['host'];
            break;

            default:
                $this->_sourceUrlInfo['socketHost'] = $this->_sourceUrlInfo['host'];
            break;
        }
    }

    /**
     * Return current URL
     *
     * @return  string
     * @access  public
     */
    public function getUrl()
    {
        return $this->_sourceUrl;
    }

    /**
	 * Set proxy server for requests
	 *
	 * @param   string $host
	 * @param   string $port
	 * @return  void
	 * @access  public
	 */
	public function setProxy($host, $port = 3128)
	{
		$tmpProxyDetails = explode(':', $host);
		$hostName    = $tmpProxyDetails[0];
		$portNumber  = (isset($tmpProxyDetails[1]) ? $tmpProxyDetails[1] : $port);

		if (!$hostName) return;

		$this->_proxy = array('host' => $hostName, 'port' => $portNumber);
	}

	/**
	 * Clear proxy server
	 *
	 * @return  void
	 * @access  public
	 */
	public function clearProxy()
	{
		$this->_proxy = null;
	}

	/**
     * Sets Header
     *
     * @param   string $headerName
     * @param   string $headerValue
     * @return  void
     * @access  public
     */
    public function setHeader($headerName, $headerValue)
    {
        /**
         * Validating header
         */
        if (is_numeric($headerName))
        {
            $headerDetails = explode(':', $headerValue, 2);
            if (count($headerDetails) > 1)
            {
                $headerName  = trim($headerDetails[0]);
                $headerValue = trim($headerDetails[1]);
            }
        }

        $this->_requestHeaders[strtolower($headerName)] = array('headerName' => $headerName, 'headerValue' => $headerValue);
    }

    /**
     * Unsets Header
     *
     * @param   string $headerName
     * @return  void
     * @access  public
     */
    public function unsetHeader($headerName)
    {
        unset($this->_requestHeaders[strtolower($headerName)]);
    }

    /**
     * Set Headers List
     *
     * @param   array $requestHeaders
     * @return  void
     * @access  public
     */
    public function setHeaders($requestHeaders)
    {
        foreach ($requestHeaders as $headerName => $headerValue)
        {
            $this->setHeader($headerName, $headerValue);
        }
    }

    /**
     * Set console for Output
     *
     * @param   PHP2_System_Console_IConsole $console
     * @param   boolean $verbose
     * @return  void
     * @access  public
     */
    public function setConsole(PHP2_System_Console_IConsole $console, $verbose = false)
    {
        $this->_console = $console;
        $this->_verbose = (boolean) $verbose;
    }

    /**
     * Returns Response Code
     *
     * @return  integer
     * @access  public
     */
    public function clear()
    {
        $this->_responseCode            = null;
        $this->_responseBody            = null;
        $this->_responseHeadersString   = null;
        $this->_responseHeaders         = array();
        $this->_responseHeadersStorage  = array();
        $this->_responseParameters      = array();

    }

    // --- Socket Requests Methods --- //

    /**
     * Process GET Request. Return is valid status code of the request.
     *
     * @param   string $url URL for GET
     * @param   array  $headers Headers list for GET
     * @param   array  $forceRange Emulate of getting range in case in server dont support ranges Header
     * @return  boolean
     * @access  protected
     */
    public function get($url = null, $headers = null, $forceRange = null)
    {
        return $this->_processGetResponse($url, $headers, $forceRange, null);
    }

    /**
     * Process POST Request. Return is valid status code of the request.
     *
     * @param   string $url URL for POST
     * @param   array  $postFields
     * @param   array  $headers Headers list for POST
     * @param   array  $forceRange Emulate of getting range in case in server dont support ranges Header
     * @return  boolean
     * @access  protected
     */
    public function post($url = null, $postFields = array(), $headers = null, $forceRange = null)
    {
        return $this->_processGetResponse($url, $headers, $forceRange, $postFields);
    }

    /**
     * Process GET Request. Return is valid status code of the GET request.
     *
     * @param   string $url URL for GET
     * @param   array  $headers Headers list for GET
     * @param   array  $forceRange Emulate of getting range in case in server dont support ranges Header
     * @param   array  $postFields
     * @return  array
     * @access  protected
     */
    protected function _processGetResponse($url = null, $headers = null, $forceRange = null, $postFields = null)
    {
        if ($url) $this->setUrl($url);
        if (is_array($headers) && $headers) $this->setHeaders($headers);

        /**
         * Checking POST request data
         */
        $postData    = false;
        $httpMethod  = 'GET';
        if ($postFields/* && (is_array($postFields) || is_object($postFields)) && count($postFields)*/)
        {
            $postData    = $this->_getPostString($postFields);
            $httpMethod  = 'POST';
        }

        /**
         * Processing Force Range action
         */
        if ($forceRange)
        {
            $tmpRanges = explode(',', $forceRange);
            if (count($tmpRanges) >= 2)
            {
                $startBytes = $tmpRanges[0];
                $endBytes   = $tmpRanges[1];
            }
        }

        $timeStart = microtime(true);

        /**
         * Creating socket
         */
		if (isset($this->_proxy['host']) && $this->_proxy['host'] && isset($this->_proxy['port']) && $this->_proxy['port'])
		{
			if ($this->_console) $this->_console->writeln('Setup proxy: '.$this->_proxy['host'].':'.$this->_proxy['port']);
			$hSocket    = fsockopen($this->_proxy['host'], $this->_proxy['port'], $connectError, $connectErrorMessage, $this->_connectionTimeout);
		}
		else
		{
        $hSocket    = fsockopen($this->_sourceUrlInfo['host'], $this->_sourceUrlInfo['port'], $connectError, $connectErrorMessage, $this->_connectionTimeout);
		}

        /**
         * Maybe we need to generate an exception in this case
         */
        if ($connectError || (!is_resource($hSocket)))
        {
			if ($this->_console) $this->_console->writeln('Socket Connection Error: '.$connectError.'. '.$connectErrorMessage);

            return false;
        }

        stream_set_timeout($hSocket, $this->_readTimeout);

        /**
         * Creating HTTP request string
         */
        $socketRequest  = $httpMethod.' '.$this->_sourceUrlInfo['queryPath']." HTTP/1.1\r\n";
        $socketRequest .= "Host: {$this->_sourceUrlInfo['host']}\r\n";
        if (count($this->_requestHeaders))
        {
            foreach ($this->_requestHeaders as $headerName => $headerDetails)
            {
                $socketRequest .= $headerDetails['headerName'].': '.$headerDetails['headerValue']."\r\n";
            }
        }
        if ($postData) $socketRequest .= "Content-Length: ".strlen($postData)."\r\n";
        $socketRequest .= "Connection: close\r\n";
        $socketRequest .= "\r\n";
        $socketRequest .= $postData;

        if ($this->_console)
        {
            $this->_console->writeln("Request headers:");
            $this->_console->writeln("---------- ---------- ---------- ---------- ---------- ---------- ----------");
            $this->_console->writeln($socketRequest);
        }

        /**
         * Writing Data to the socket and reading socket response
         */
        fwrite($hSocket, $socketRequest);
        $socketResponse  = '';
        // $timeStart = microtime(true);
        while ($hSocket && !feof($hSocket))
        {
            $responseBlock   = fgets($hSocket, 4096);
            $socketResponse .= $responseBlock;
            $responseLength  = strlen($socketResponse);

            /**
             * Fixing bug #43782 in php with sockets freezing. The source of the problem is: server does not closed connection and timeout was reached.
             *
             * Fixes available in php snapshots after 2008-08-26.
             */
            $metaInfo = stream_get_meta_data($hSocket);
            if (!$metaInfo['unread_bytes'] && $metaInfo['timed_out'])
            {
                break;
            }

            // if ($this->_console) $this->_console->write($responseBlock);
            // if ($this->_console) $this->_console->writeln('Bytes read: '.strlen($socketResponse));

            /**
             * Processing response headers
             */
            if (($responseLength > 8092) && (!$this->_responseParameters))
            {
                $this->_processResponceHeaders($socketResponse);
            }

            // $currTime = microtime(true);
            // if ($currTime > $timeStart + 2) exit;
            if ($forceRange && ($endBytes + 4096 < $responseLength))
            {
                fclose($hSocket);
                break;
            }
        }

        $socketResponseCode = $this->_processResponse($socketResponse);

        if ($this->_console)
        {
            $this->_console->writeln("---------- ---------- ---------- ---------- ---------- ---------- ----------");
            $this->_console->writeln("Socket process time: ".(microtime(true) - $timeStart));
            $this->_console->writeln("");
        }

        /**
         * Processing Recursive redirects
         */
        if (!$socketResponseCode && $this->_followRedirects && (($this->_responseCode == 303) || ($this->_responseCode == 302) || ($this->_responseCode == 301)) && ($this->_redirectsCount < 5))
        {
            if (!$location = $this->getResponseHeader('Location')) return false;

            if ($location[0] == '/')
            {
                $location = $this->_sourceUrlInfo['scheme'].'://'.$this->_sourceUrlInfo['host'].$location;
            }

            $this->_redirectsCount++;

            /**
             * Processing request recursively
             */
            return $this->_processGetResponse($location, $headers, $forceRange, $postData);
        }
        else
        {
            $this->_redirectsCount = 0;
        }

        return $socketResponseCode;
    }

    /**
     * Returns encoded post string
     *
     * @param   array $postFields
     * @param   string $parentPostFieldName
     * @return  string
     */
    protected function _getPostString($postFields, $parentPostFieldName = null)
    {
        /**
         * Checking is postfields are Object or Array type
         */
        if (!is_array($postFields) && !is_object($postFields))
        {
            return (($parentPostFieldName) ? $parentPostFieldName.'='.$postFields : $postFields);
        }

        $result  = '';
        $i       = 0;
        foreach ($postFields as $parameterName => $parameterValue)
        {
            $postFieldName = ($parentPostFieldName) ? $parentPostFieldName.'['.rawurlencode($parameterName).']' : rawurlencode($parameterName);
            if (is_array($parameterValue) || is_object($parameterValue))
            {
                $result .= ($i ? '&' : '').$this->_getPostString($parameterValue, $postFieldName);
            }
            else
            {
                $result .= ($i ? '&' : '').$postFieldName.'='.rawurlencode($parameterValue);
            }

            $i++;
        }

        return $result;
    }

    /**
     * Process HEAD Request. Return code of the HEAD request.
     *
     * @param   string $url URL for GET
     * @param   array  $headers Headers list for GET
     * @return  array
     * @access  protected
     */
    public function head($url = null, $headers = null)
    {
        if ($url) $this->setUrl($url);
        if (is_array($headers) && $headers) $this->setHeaders($headers);

        $timeStart = microtime(true);

        /**
         * Creating socket
         */
		if (isset($this->_proxy['host']) && $this->_proxy['host'] && isset($this->_proxy['port']) && $this->_proxy['port'])
		{
			if ($this->_console) $this->_console->writeln('Setup proxy: '.$this->_proxy['host'].':'.$this->_proxy['port']);
			$hSocket    = fsockopen($this->_proxy['host'], $this->_proxy['port'], $connectError, $connectErrorMessage, $this->_connectionTimeout);
		}
		else
		{
			$hSocket    = fsockopen($this->_sourceUrlInfo['host'], $this->_sourceUrlInfo['port'], $connectError, $connectErrorMessage, $this->_connectionTimeout);
		}

        /**
         * Maybe we need to generate an exception in this case
         */
        if ($connectError || (!is_resource($hSocket)))
        {
            if ($this->_console) $this->_console->write('Socket Connection Error: '.$connectError.'. '.$connectErrorMessage);

            return false;
        }

        /**
         * Creating HTTP request string
         */
        $socketRequest  = "HEAD ".$this->_sourceUrlInfo['queryPath']." HTTP/1.1\r\n";
        $socketRequest .= "Host: {$this->_sourceUrlInfo['host']}\r\n";

        if (count($this->_requestHeaders))
        {
            foreach ($this->_requestHeaders as $headerName => $headerDetails)
            {
                $socketRequest .= $headerDetails['headerName'].': '.$headerDetails['headerValue']."\r\n";
            }
        }
        $socketRequest .= "Connection: close\r\n";
        $socketRequest .= "\r\n";

        if ($this->_console)
        {
            $this->_console->writeln("Request headers:");
            $this->_console->writeln("---------- ---------- ---------- ---------- ---------- ---------- ----------");
            $this->_console->writeln($socketRequest);
        }

        /**
         * Writing Data to the socket and reading socket response
         */
        fwrite($hSocket, $socketRequest);
        $socketResponse  = '';

        /**
         * Get HEAD response headers
         */
        while (!feof($hSocket))
        {
            $socketResponse .= fgets($hSocket);
        }

        $socketResponseCode = $this->_processResponse($socketResponse);

        if ($this->_console)
        {
            $this->_console->writeln("---------- ---------- ---------- ---------- ---------- ---------- ----------");
            $this->_console->writeln("Socket process time: ".(microtime(true) - $timeStart));
            $this->_console->writeln("");
        }

        /**
         * Processing Recursive redirects
         */
        if (!$socketResponseCode && $this->_followRedirects && (($this->_responseCode == 303) || ($this->_responseCode == 302) || ($this->_responseCode == 301)) && ($this->_redirectsCount < 5))
        {
            if (!$location = $this->getResponseHeader('Location')) return false;

            if ($location[0] == '/')
            {
                $location = $this->_sourceUrlInfo['scheme'].'://'.$this->_sourceUrlInfo['host'].$location;
            }

            $this->_redirectsCount++;

            return $this->head($location, $headers);
        }
        else
        {
            $this->_redirectsCount = 0;
        }

        return $socketResponseCode;
    }

    /**
     * Process PUT Request. Return code of the PUT request.
     *
     * @param   string $url
     * @param   string $filename
     * @param   array  $headers
     * @return  array
     * @access  protected
     */
    public function put($url = null, $filename = null, $headers = null)
    {
        $fileContent = file_get_contents($filename);

        return $this->putFileContent($url, $fileContent, $headers);
    }

    /**
     * Process PUT Request. Return code of the PUT request.
     *
     * @param   string $url
     * @param   string $fileContent
     * @param   array  $headers
     * @return  array
     * @access  protected
     */
    public function putFileContent($url = null, $fileContent = null, $headers = null)
    {
        if ($url) $this->setUrl($url);
        if (is_array($headers) && $headers) $this->setHeaders($headers);

        $timeStart = microtime(true);

        /**
         * Creating socket
         */
        $hSocket    = @fsockopen($this->_sourceUrlInfo['host'], $this->_sourceUrlInfo['port'], $connectError, $connectErrorMessage, $this->_connectionTimeout);

        /**
         * Maybe we need to generate an exception in this case
         */
        if ($connectError || (!is_resource($hSocket)))
        {
            if ($this->_console) $this->_console->write('Socket Connection Error: '.$connectError.'. '.$connectErrorMessage);

            return false;
        }

        /**
         * Creating HTTP request string
         */
        $socketRequest  = "PUT ".$this->_sourceUrlInfo['queryPath']." HTTP/1.1\r\n";
        $socketRequest .= "Host: {$this->_sourceUrlInfo['host']}\r\n";

        if (count($this->_requestHeaders))
        {
            foreach ($this->_requestHeaders as $headerName => $headerDetails)
            {
                $socketRequest .= $headerDetails['headerName'].': '.$headerDetails['headerValue']."\r\n";
            }
        }
        $socketRequest .= 'Content-length: '.strlen($fileContent)."\r\n";
        $socketRequest .= "Connection: close\r\n";
        $socketRequest .= "\r\n";
        $socketRequestHeaders = $socketRequest;
        $socketRequest .= $fileContent;

        if ($this->_console)
        {
            $this->_console->writeln("Request headers:");
            $this->_console->writeln("---------- ---------- ---------- ---------- ---------- ---------- ----------");
            $this->_console->writeln($socketRequestHeaders);
        }

        /**
         * Writing Data to the socket and reading socket response
         */
        fwrite($hSocket, $socketRequest);
        $socketResponse  = '';

        /**
         * Get HEAD response headers
         */
        while (!feof($hSocket))
        {
            $socketResponse .= fgets($hSocket);
        }

        $socketResponseCode = $this->_processResponse($socketResponse);

        if ($this->_console)
        {
            $this->_console->writeln("---------- ---------- ---------- ---------- ---------- ---------- ----------");
            $this->_console->writeln("Socket process time: ".(microtime(true) - $timeStart));
            $this->_console->writeln("");
        }

        return $socketResponseCode;
    }


    // --- Response Processing Methods --- //

    /**
     * Process response headers
     *
     * @param   string $responseString
     * @param   array  $responceParameters
     * @return  string
     */
    protected function _processResponceHeaders($responseString)
    {
        $headersEndPos    = 0;

        /**
         * Defining line delimiter for response
         */
        $stringDelimiter  = "\r\n";
        if (($headersEndPos = strpos($responseString, "\r\n\r\n")) === null)
        {
            if (($headersEndPos = strpos($responseString, "\n\n")) !== false)
            {
                $stringDelimiter       = "\n";
            }
            else
            {
                /**
                 * Writing Error about response Headers
                 */
                if ($this->_console)
                {
                    $this->_console->writeError('Invalid response headers!');
                    $this->_console->writeError(substr($responseString, 0, 1024));
                }

                return false;
            }
        }
        else
        {
            $stringDelimiter       = "\r\n";
        }

        /**
         * Processing Response Headers string
         */
        $this->_responseHeadersString   = substr($responseString, 0, $headersEndPos);
        $this->_responseHeaders         = array();
        $this->_responseHeadersStorage  = array();
        $responseHeadersList            = explode($stringDelimiter, $this->_responseHeadersString);
        foreach ($responseHeadersList as $headerInfo)
        {
            if (strpos($headerInfo, ':'))
            {
                $headerParts = explode(':', $headerInfo, 2);
                $this->_responseHeaders[trim($headerParts[0])] = trim($headerParts[1]);
                $this->_responseHeadersStorage[strtolower(trim($headerParts[0]))] = array('headerName' => trim($headerParts[0]), 'headerValue' => trim($headerParts[1]));
            }
        }

        /**
         * Matching response Code
         */
        preg_match("/HTTP\/1.([\d]{1})[\s]+([\d]+)/x", $this->_responseHeadersString, $matches);
        $this->_responseCode = isset($matches[2]) ? $matches[2] : false;

        if ($this->_console)
        {
            $this->_console->writeln("\nResponse code: ".$this->_responseCode);
            $this->_console->writeln("Response headers:");
            $this->_console->writeln("---------- ---------- ---------- ---------- ---------- ---------- ----------");
            $this->_console->writeln($this->_responseHeadersString);

            /**
             * In verbose mode outputing Response body to the console.
             */
            if ($this->_verbose)
            {
                if ($responseBody = trim(substr($responseString, $headersEndPos)))
                {
                    $this->_console->writeDelimiterLine();
                    $this->_console->writeln("Response Body:");
                    $this->_console->writeln($responseBody);
                }
            }
        }

        $result = true;
        switch ($this->_responseCode)
        {
            case 200:
            case 206:
                $result = true;
            break;

            default:
                $result = false;
        }

        $this->_responseParameters                     = array();
        $this->_responseParameters['stringDelimiter']  = $stringDelimiter;
        $this->_responseParameters['headersEndPos']    = $headersEndPos;
        $this->_responseParameters['result']           = $result;

        return $result;
    }

    /**
     * Process Socket response from the server
     *
     * @param   string $responseString
     * @return  string
     */
    protected function _processResponse($responseString)
    {
        if (!$this->_responseParameters) $this->_processResponceHeaders($responseString);

        $headersEndPos    = $this->_responseParameters['headersEndPos'];
        $stringDelimiter  = $this->_responseParameters['stringDelimiter'];

        /**
         * Processing response body.
         *
         * In some cases HTTP server sends response as blocks with size predefined for this server.
         * First occurence of block size is added to 2-nd line after HTTP headers.
         * If this line is empty than all symbols after second is the Body of the HTTP request
         */
        $bodyStartPos = $headersEndPos ? $headersEndPos + 2*strlen($stringDelimiter) : 0;
        $isUsedOutputBuffer = false;
        if (($bodyLengthStrEndPos = strpos($responseString, $stringDelimiter, $bodyStartPos)) !== false)
        {
            if (($bodyLengthStrEndPos > $bodyStartPos) && ($bodyLengthStrEndPos < $bodyStartPos + 12))
            {
                $isUsedOutputBuffer = true;
                $bodySize16    = substr($responseString, $bodyStartPos, $bodyLengthStrEndPos - $bodyStartPos);
                $bodySize      = intval(((string) $bodySize16), 16);
                $responseBody  = '';
                $startReadPos  = $bodyLengthStrEndPos + strlen($stringDelimiter);
                while ($bodySize)
                {
                    $responseBody .= substr($responseString, $startReadPos, $bodySize);

                    // echo substr($responseString, $startReadPos, $bodySize);
                    // echo "\n\n\n\n";
                    if (($bodyLengthStrEndPos = strpos($responseString, $stringDelimiter, $startReadPos + $bodySize + strlen($stringDelimiter))) !== false)
                    {
                        $bodySize16    = substr($responseString, $startReadPos + $bodySize + strlen($stringDelimiter), $bodyLengthStrEndPos - $startReadPos - $bodySize - strlen($stringDelimiter));
                        $bodySize      = intval(((string) $bodySize16), 16);
                        $startReadPos  = $bodyLengthStrEndPos + strlen($stringDelimiter);
                    }
                    else
                    {
                        $bodySize = false;
                    }
                }

                $this->_responseBody = $responseBody;
            }
        }

        /**
         * In some cases server use 2 empty Lines before response body.
         * Some servers use only 1 empty line as in the HTTP/1.1 standard. It is recommended to ignore all empty lines before response body.
         * We are ignoring only one empty line for now.
         */
        if (!$isUsedOutputBuffer)
        {
            if (strpos($responseString, $stringDelimiter, $bodyStartPos) == $bodyStartPos) $bodyStartPos + strlen($stringDelimiter);
            $this->_responseBody = substr($responseString, $bodyStartPos);
        }

        return $this->_responseParameters['result'];
    }


    // --- Response manipulating Part --- //

    /**
     * Returns Response Body
     *
     * @return  string
     * @access  public
     */
    public function getResponseBody()
    {
        return $this->_responseBody;
    }

    /**
     * Returns response headers as array
     *
     * @return  array
     * @access  public
     */
    public function getResponseHeaders()
    {
        return $this->_responseHeaders;
    }

    /**
     * Returns specified response header as string
     *
     * @param   string $headerName Name of the header
     * @return  string
     * @access  public
     */
    public function getResponseHeader($headerName)
    {
        $headerSearchName = strtolower($headerName);

        return isset($this->_responseHeadersStorage[$headerSearchName]) ? $this->_responseHeadersStorage[$headerSearchName]['headerValue'] : null;
    }

    /**
     * Returns response headers as string
     *
     * @return  string
     * @access  public
     */
    public function getResponseHeadersString()
    {
        return $this->_responseHeadersString;
    }

    /**
     * Returns Response Code
     *
     * @return  integer
     * @access  public
     */
    public function getResponseCode()
    {
        return $this->_responseCode;
    }

}
