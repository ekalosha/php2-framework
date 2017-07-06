<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class which implements XML Value Objects with Namespaces
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 97 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 3.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\WebService\VO;

/**
 * Class implements XML with Namespaces Value Objects
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: xmlnamespace.class.php 97 2009-08-19 08:50:57Z eugene $
 * @access   public
 * @package  PHP2\WebService\VO
 */
class PHP2_WebService_VO_XMLNamespace extends PHP2_WebService_VO_XML
{
    /**
     * Namespace Name
     *
     * @var     string
     * @access  protected
     */
    protected $_namespaceName;

    /**
     * XML Namespace VO constructor.
     *
     * @param   string $namespaceName Name of current namespace
     * @param   string $xmlObject
     * @param   mixed  $attributes
     * @access  pulic
     */
    public function __construct($namespaceName = null, $xmlObject = null, $attributes = array())
    {
        if ($namespaceName) $this->setNamespaceName($namespaceName);

        /**
         * Initializing Parent Object
         */
        parent::__construct($xmlObject, $attributes);
    }

    /**
     * Set Namespace Name for Current Object
     */
    public function setNamespaceName($namespaceName)
    {
        $this->_namespaceName = $namespaceName;
    }

    /**
     * Return XML representation of the Object as Specified Node
     *
     * @param   string $nodeName
     * @return  string
     * @access  public
     */
    public function __toXML($nodeName)
    {
        return parent::__toXML(($this->_namespaceName ? $this->_namespaceName.':' : '').$nodeName);
    }

}
