<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains base class for XML Value Objects
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 118 $
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
 * Base Class for XML Value Objects
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: xml.class.php 118 2011-08-23 15:15:39Z eugene $
 * @access   public
 * @package  PHP2\WebService\VO
 */
class PHP2_WebService_VO_XML implements ArrayAccess, Countable
{
	/**
	 * Constants described XML instances for Value Object
	 */
	const ATTRIBUTES = 'attributes';

	/**
	 * Attributes Array
	 *
	 * @var     array
	 * @access  public
	 */
	public $attributes = array();

	/**
	 * XML Node Content
	 *
	 * @var     string
	 * @access  public
	 */
	public $text;

	/**
	 * Is use CData for the XML Node Content
	 *
	 * @var     boolean
	 * @access  public
	 */
	public $isCData;

	/**
	 * XML Nodes List
	 *
	 * @var     array
	 * @access  public
	 */
	protected $_xmlNodes = array();

	/**
	 * XML Collections List
	 *
	 * @var     array
	 * @access  public
	 */
	protected $_xmlCollections = array();

	/**
	 * XML VO constructor
	 *
	 * @param   string $xmlObject
	 * @param   mixed  $attributes
	 * @access  pulic
	 */
	public function __construct($xmlObject = null, $attributes = array())
	{
		/**
		 * Assigning Default Value of Object
		 */
		if ($xmlObject)
		{
			$this->assign($xmlObject, $attributes);
		}
		else
		{
			$this->_assignAttributes($attributes);
		}
	}

	/**
	 * Unset specified Node
	 *
	 * @param   string $nodeName
	 * @return  boolean
	 * @access  public
	 */
	protected function _unsetNode($nodeName)
	{
		if (isset($this->_xmlNodes[$nodeName]))
		{
			unset($this->_xmlNodes[$nodeName]);

			return true;
		}
		elseif (isset($this->_xmlCollections[$nodeName]))
		{
			unset($this->_xmlCollections[$nodeName]);

			return true;
		}

		return false;
	}

	/**
	 * Assign some object to the current Object as main value
	 *
	 * @param   mixed $assignedObject
	 * @param   array $attributes
	 * @return  boolean
	 */
	public function assign($assignedObject, $attributes = false)
	{
		/**
		 * Checking is this Object VO Convertable
		 */
		if (is_object($assignedObject) && ($assignedObject instanceof PHP2_WebService_VO_IConvertable)) $assignedObject = $assignedObject->getValueObject();

		/**
		 * Assigning Value of this object from different sources
		 */
		if (is_array($assignedObject) || is_object($assignedObject))
		{
			return $this->_assignFromObject($assignedObject, $attributes);
		}
		else
		{
			$this->_assignAttributes($attributes);
			$this->text = $assignedObject;
		}

		return false;
	}

	/**
	 * Assign current XML Object value from Array/Object
	 *
	 * @param   array $assignedObject
	 * @param   array $attributes
	 * @return  boolean
	 */
	protected function _assignFromObject($assignedObject, $attributes = false)
	{
		/**
		 * Assigning Attributes
		 */
		$baseAttributes = array();
		if (is_array($assignedObject))
		{
			$baseAttributes = (isset($assignedObject[self::ATTRIBUTES]) ? $assignedObject[self::ATTRIBUTES] : array());
		}
		else
		{
			$baseAttributes = (isset($assignedObject->{self::ATTRIBUTES}) ? $assignedObject->{self::ATTRIBUTES} : array());
		}
		$this->_assignAttributes($baseAttributes, $attributes);

		/**
		 * Assigning parameters from Object
		 */
		foreach ($assignedObject as $parameterName => $parameterValue)
		{
			if ($parameterName != self::ATTRIBUTES)
			{
				$this->{$parameterName} = $parameterValue;
			}
		}

		return false;
	}

	/**
	 * Assign attributes to the Current Object
	 *
	 * @param   mixed $attributes
	 * @param   mixed $additionalAttributes
	 * @return  boolean
	 */
	protected function _assignAttributes($attributes, $additionalAttributes = false)
	{
		$this->attributes = array();

		/**
		 * Assigning attributes from Objects
		 */
		if (is_array($attributes) || is_object($attributes))
		{
			foreach ($attributes as $attributeName => $attributeValue) $this->attributes[$attributeName] = (string) $attributeValue;
		}

		/**
		 * Assigning additional attributes from Objects
		 */
		if (is_array($additionalAttributes) || is_object($additionalAttributes))
		{
			foreach ($additionalAttributes as $attributeName => $attributeValue) $this->attributes[$attributeName] = (string) $attributeValue;
		}

		return true;
	}

	/**
	 * Adds attributes to the Current Object
	 *
	 * @param   mixed $attributes
	 * @return  boolean
	 */
	public function addAttributes($attributes)
	{
		if (is_array($attributes) || is_object($attributes))
		{
			foreach ($attributes as $attributeName => $attributeValue)
			{
				$this->attributes[$attributeName] = (string) $attributeValue;
			}
		}
	}

	/**
	 * Set text content of the current node
	 *
	 * @param   string  $text
	 * @param   boolean $isCData
	 * @return  boolean
	 */
	public function setText($text = null, $isCData = false)
	{
		$this->text = $text;

		$this->setCData($isCData);
	}

	/**
	 * Set text CData status for text node
	 *
	 * @param   boolean $isCData
	 * @return  boolean
	 */
	public function setCData($isCData = false)
	{
		$this->isCData = (boolean) $isCData;
	}

	/**
	 * Adds Object to XML Collection of the Current Object
	 *
	 * @param   string  $collectionName
	 * @param   mixed   $xmlNodeObject
	 * @return  boolean
	 */
	public function addToCollection($collectionName, $xmlNodeObject)
	{
		$this->_xmlCollections[$collectionName][] = new PHP2_WebService_VO_XML($xmlNodeObject);
	}

	/**
	 * Checks is current Array valid for XML convertion
	 *
	 * @param   array $arrayObject
	 * @return  boolean
	 */
	protected function _checkInvalidXMLNodeKeysInArray($arrayObject)
	{
		$xmlNodesList = array_keys($arrayObject);
		foreach ($xmlNodesList as $nodeName)
		{
			if (!$this->_checkIsXMLNodeNameValid($nodeName)) return false;
		}

		return true;
	}

	/**
	 * Check is specified Name is valid Name for XML Node
	 *
	 * @param   string $xmlNodeName
	 * @return  boolean
	 */
	protected function _checkIsXMLNodeNameValid($xmlNodeName)
	{
		preg_match('/[a-zA-Z_]+[\_\w\d]*/', $xmlNodeName, $matches);

		if (isset($matches[0]) && ($matches[0] == $xmlNodeName)) return true;

		return false;
	}

	/**
	 * Overriding Access to Object fields
	 *
	 * @param   string $objectName
	 * @return  PHP2_WebService_VO_XML
	 * @access  public
	 */
	public function __get($objectName)
	{
		if (isset($this->_xmlCollections[$objectName])) return $this->_xmlCollections[$objectName];

		if (!isset($this->_xmlNodes[$objectName])) $this->_xmlNodes[$objectName] = new PHP2_WebService_VO_XML();

		return $this->_xmlNodes[$objectName];
	}

	/**
	 * Overriding Access to set XML Nodes fields
	 *
	 * @param   string $objectName
	 * @param   mixed  $objectValue
	 * @access  public
	 */
	public function __set($objectName, $objectValue)
	{
		if (is_array($objectValue) && !$this->_checkInvalidXMLNodeKeysInArray($objectValue))
		{
			$this->_xmlCollections[$objectName] = array();
			foreach ($objectValue as $tmpObjectKey => &$tmpObjectValue)
			{
				$this->_xmlCollections[$objectName][$tmpObjectKey] = new PHP2_WebService_VO_XML($tmpObjectValue);
			}
		}
		elseif (is_object($objectValue) && ((get_class($objectValue) == 'PHP2_WebService_VO_XML') || is_subclass_of($objectValue, 'PHP2_WebService_VO_XML')))
		{
			$this->_xmlNodes[$objectName] = &$objectValue;
		}
		else
		{
			$this->_xmlNodes[$objectName] = new PHP2_WebService_VO_XML($objectValue);
		}
	}

	/**
	 * Overriding 'unset' for Object Node
	 *
	 * @param   string $objectName
	 * @return  boolean
	 * @access  public
	 */
	public function __unset($objectName)
	{
		return $this->_unsetNode($objectName);
	}

	/**
	 * Overriding 'isset' method for Object Node
	 *
	 * @param   string $objectName
	 * @return  boolean
	 * @access  public
	 */
	public function __isset($objectName)
	{
		return (isset($this->_xmlNodes[$objectName]) || isset($this->_xmlCollections[$objectName]));
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
		$result = '<'.$nodeName.$this->getAttributesString().'>';
		if ($this->text !== null)
		{
			if ($this->isCData)
			{
				$result .= '<![CDATA['.$this->text.']]>';
			}
			else
			{
				$result .= str_replace('&', '&amp;', $this->text);
				/*
				if (strpos($this->text, '<![CDATA[') !== false)
				{
					$result .= $this->text;
				}
				else
				{
					$result .= PHP2_Utils_String::validateXMLText($this->text);
				}
				 */
				// $result .= PHP2_Utils_String::validateXMLText($this->text);
			}
		}
		else
		{
			/**
			 * Processing XML nodes
			 */
			foreach ($this->_xmlNodes as $xmlNodeName => &$xmlNodeObject)
			{
				$result .= $xmlNodeObject->__toXML($xmlNodeName);
			}

			/**
			 * Processing XML Collections
			 */
			foreach ($this->_xmlCollections as $xmlCollectionName => &$xmlCollectionObject)
			{
				foreach ($xmlCollectionObject as &$collectionItem)
				{
					$result .= $collectionItem->__toXML($xmlCollectionName);
				}
			}
		}
		$result .= '</'.$nodeName.'>';

		return $result;
	}

	/**
	 * Generate valid XML document for current XML object
	 *
	 * @param   string $rootNode Name of the root node. By default 'Response'.
	 * @param   string $version  XML document Version. By default '1.0'.
	 * @param   string $encoding XML document Encoding. By default 'UTF-8'.
	 * @return  string
	 */
	public function getXML($rootNode = false, $version = null, $encoding = null)
	{
		$result  = '<?xml version="'.($version ? $version : '1.0').'" encoding="'.($encoding ? $encoding : 'UTF-8').'"?>'."\n";
		$result .= $this->__toXML($rootNode ? $rootNode : 'Response');

		return $result;
	}

	/**
	 * Return Attributes list for Current Object
	 *
	 * This function automatically translate some entities to XML valid form.
	 * A list of translated entities is below:
	 *
	 * # &amp; refers to an ampersand (&) - yes
	 * # &lt; refers to a less-than symbol (<) - yes
	 * # &gt; refers to a greater-than symbol (>) - yes
	 * # &quot; refers to a double-quote mark (") - yes
	 * # &apos; refers to an apostrophe (') - yes
	 *
	 * @return  string
	 */
	public function getAttributesString()
	{
		$result = '';

		if ($this->attributes && (is_array($this->attributes) || is_object($this->attributes)))
		{
			foreach ($this->attributes as $attributeName => $attributeValue)
			{
				$result .= ' '.$attributeName.'="'.PHP2_Utils_String::validateXMLText($attributeValue).'"';
			}
		}

		return $result;
	}

	/**
	 * Checks is offset exists in the object attributes.
	 * Implementation of the ArrayAccess interface from the SPL.
	 *
	 * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
	 * After implementation of offsetExists method you can use isset() method to check is attribute exists in the object.
	 * For example:
	 * <code>
	 *    $xmlObject = new PHP2_WebService_VO_XML();
	 *    ...
	 *    if (isset($xmlObject['attributeName'])) ...
	 * </code>
	 *
	 * @param   string $offset
	 * @return  boolean
	 */
	public function    offsetExists($offset)
	{
		return (isset($this->attributes[$offset]));
	}

	/**
	 * Returns object attribute.
	 * Implementation of the ArrayAccess interface from the SPL.
	 *
	 * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
	 * After implementation of offsetGet method you can use [] modifier to get attribute.
	 * For example:
	 * <code>
	 *    $xmlObject = new PHP2_WebService_VO_XML();
	 *    ...
	 *    $attributeValue = $xmlObject['attributeName'];
	 * </code>
	 *
	 * @param   string $offset
	 * @return  string
	 */
	public function    offsetGet($offset)
	{
		return (isset($this->attributes[$offset]) ? $this->attributes[$offset] : null);
	}

	/**
	 * Set object attribute.
	 * Implementation of the ArrayAccess interface from the SPL.
	 *
	 * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
	 * After implementation of offsetSet method you can use [] modifier to set attribute.
	 * For example:
	 * <code>
	 *    $xmlObject = new PHP2_WebService_VO_XML();
	 *    ...
	 *    $xmlObject['attributeName'] = $attributeValue;
	 * </code>
	 *
	 * @param   string $offset
	 * @param   string $value
	 * @return  string
	 */
	public function    offsetSet($offset, $value)
	{
		$this->attributes[$offset] = $value;
	}

	/**
	 * Unsets offset in the object attributes.
	 * Implementation of the ArrayAccess interface from the SPL.
	 *
	 * As the result of implementation of ArrayAccess interface from SPL we can use objects of current class as arrays.
	 * After implementation of offsetUnset method you can use unset() method to unset attribute of the object.
	 * For example:
	 * <code>
	 *    $xmlObject = new PHP2_WebService_VO_XML();
	 *    ...
	 *    unset($xmlObject['attributeName']);
	 * </code>
	 *
	 * @param   string $offset
	 * @return  boolean
	 */
	public function    offsetUnset($offset)
	{
		if (isset($this->attributes[$offset])) unset($this->attributes[$offset]);
	}

	/**
	 * Returns nodes count in the object.
	 * Implementation of the Countable interface from the SPL.
	 *
	 * As the result of implementation of Countable interface from SPL we can properly use count() method for objects of this class.
	 * For example:
	 * <code>
	 *    $xmlObject = new PHP2_WebService_VO_XML();
	 *    ...
	 *    $nodesCount = count($xmlObject);
	 * </code>
	 *
	 * @return  integer
	 */
	public function    count()
	{
		$result = count($this->_xmlNodes);
		foreach ($this->_xmlCollections as &$collection)
		{
			$result += count($collection);
		}
		return $result;
	}

}
