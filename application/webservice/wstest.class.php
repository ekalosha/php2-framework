<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Test Web service Class
 *
 * PHP version 5
 * @category   Web Services
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Class for Test Web service
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: wstest.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  Application\WebService
 */
class Application_WebService_WSTest extends PHP2_WebService_XMLRequestHandler
{
    /**
     * Application_WebService_WSTest constructor
     *
     * @access  public
     */
    public function __construct()
    {
        /**
         * Processing parent constructor
         */
        parent::__construct();
    }

    /**
     * On Web service init event handler
     *
     * @access  protected
     */
    protected function on_Init()
    {
        $this->registerHandler('testXMLResponse', array(array('name' => 'loadedParameter', 'type' => 'string')));
        $this->registerHandler('testPHP2SystemProfiler', array(array('name' => 'loadedParameter', 'type' => 'string')));
        $this->registerHandler('testSimpleDatabaseOperations');
        $this->registerHandler('testPHP2Socket_POST');
        $this->registerHandler('getPOSTData');
    }

    /**
     * Default Method to test Architecture Features
     *
     * @param    string $loadedParameter
     * @return   string
     * @access   protected
     */
    public function testXMLResponse($loadedParameter)
    {
        $this->registerNamespace('someNamespace', 'http://php2-v3.ekalosha.dev.solartxit.com/Namespaces');

        $result = new PHP2_WebService_VO_XML();
        $result->DefaultParameter = $loadedParameter;
        $result->DefaultParameter['Attribute1'] = 'Value of Attribute 1';
        $result->DefaultParameter['Attribute2'] = $result->DefaultParameter['Attribute1'].' & Attribute2';

        $nodeWithNamespace = new PHP2_WebService_VO_XMLNamespace();
        $nodeWithNamespace->setNamespaceName('someNamespace');
        $nodeWithNamespace->text = 'Value1';

        $result->NodeWithNamespace = $nodeWithNamespace;
        $result->NodeWithNamespace2 = new PHP2_WebService_VO_XMLNamespace('someNamespace', 'inner text');


        $result->ArrayCollectionNode->ACNodeName = array(1, 2, 'some text', 'other text');

        return $result;
    }

    /**
     * Default Method to test Architecture Features
     *
     * @param    string $loadedParameter
     * @return   string
     * @access   protected
     */
    public function testPHP2SystemProfiler($loadedParameter)
    {
        $cityDetails = new Application_DBLayer_SysUser(DB_SLAVE_CONNECTION, 1);

        $result->CityDetails  = $cityDetails;

        /**
         * Writing profiling Info
         */
        PHP2_System_Profiler::getInstance()->trace($loadedParameter, '$loadedParameter');
        PHP2_System_Profiler::getInstance()->trace($cityDetails, 'City Details');

        return $result;
    }

    /**
     * Method to test simple DB architecture Features
     *
     * @return   string
     * @access   protected
     */
    public function testSimpleDatabaseOperations()
    {
        $result = new PHP2_WebService_VO_XML();

        $countryDetails = new Application_DBLayer_Country(DB_MASTER_CONNECTION);
        $recordset = $countryDetails->getList('CountryName LIKE \'u%\'')->getRecordset();
        while ($recordset->next())
        {
            $countryResult   = new PHP2_WebService_VO_XML();
            $cityesListQuery = new PHP2_Database_SQLQuery('SELECT * FROM City WHERE CountryID='.$recordset->ID.' LIMIT 10', DB_MASTER_CONNECTION);
            $cityesListQuery->execute();
            $countryResult->Cities->City = $cityesListQuery->getMatrix();
            $countryResult->CountryID    = $recordset->ID;
            $countryResult->CountryName  = $recordset->CountryName;

            $result->Countries->addToCollection('Country', $countryResult);
        }

        return $result;
    }

    /**
     * Tests post request via HTTP sockets
     *
     * @return   string
     * @access   protected
     */
    public function testPHP2Socket_POST()
    {
        // wstest.php?__callHandler=getPOSTData
        $urlToPost = PHP2_System_Response::getInstance()->getUrl('wstest.php', array('__callHandler' => 'getPOSTData2'));
        $postData  = array('xmlData' => 'Some XML');

        $httpSocket = new PHP2_Net_HTTPSocket();
        $httpSocket->setConsole(new PHP2_System_Console_Console(), true);
        // $httpSocket->setHeader('Content-type', 'text/html');
        $httpSocket->setHeader('accept', 'text/html');
        $httpSocket->setHeader('accEpt', 'text/html');
        $httpSocket->post($urlToPost, $postData);
        $httpSocket->head($urlToPost);

        $result = new PHP2_WebService_VO_XML();
        $result->PostURL  = $urlToPost;
        $result->PostData = print_r($postData, true);
        $result->PostResponse->setText($httpSocket->getResponseBody(), true);

        /*$contextInfo = array(
                                'ssl' => array(
                                                'verify_peer' => false,
                                                'allow_self_signed'    => true,
                                                'cafile' => '/opt/php/testscripts/newkey.pem',
                                                'capath' => '/opt/php/testscripts/',
                                                'local_cert' => '/opt/php/testscripts/newkey.pem',
                                                'passphrase' => '****',
                                                'CN_match' => 'ai000.de'
                                              )
                            );
       $sslContext = stream_context_create($contextInfo);*/


        return $result;
    }

    /**
     * Prints post data
     *
     * @return   string
     * @access   protected
     */
    public function getPOSTData2()
    {
        PHP2_System_Response::getInstance()->urlRedirect('wstest.php', array('__callHandler' => 'getPOSTData'));
    }

    /**
     * Prints post data
     *
     * @return   string
     * @access   protected
     */
    public function getPOSTData()
    {
        echo '$_POST data is: ';
        print_r($_POST);
        exit();
    }

}
