<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * File contains Text blocks component
 *
 * PHP version 5
 * @category   Library Classes
 * @author     Eugene A. Kalosha <ekalosha@gmail.com>
 * @copyright  (c) 2004-2010 by SolArt xIT
 * @license    http://opensource.org/licenses/gpl-3.0.html GNU General Public License version 3.0
 * @version    SVN: $Revision: 114 $
 * @link       http://www.solartxit.com/php2
 * @since      File available since Release 2.0
 */

/**
 * Defining Namespace
 *
 * For PHP versions 5.3 and Higher
 */
// namespace PHP2\UI\Components\Additional;

/**
 * Text Blocks component
 *
 * Dispatches the following events:
 *
 * <code>
 *     PHP2_UI_UIEvent::INIT_STATE
 *     PHP2_UI_UIEvent::CHANGE
 *     PHP2_UI_UIEvent::CREATION_COMPLETE
 * </code>
 *
 * Usage in the template:
 *
 * <code>
 *      <php:textblocks:$objectName
 *          [isolated="true|false"]
 *          [visible="true|false"]
 *      />
 *          <block:$blockName>
 *              Simple template
 *          </block:$blockName>
 *      </php:textblocks:$objectName>
 * </code>
 *
 * @author   Eugene A. Kalosha <ekalosha@gmail.com>
 * @version  $Id: textblocks.class.php 114 2010-05-21 15:32:29Z eugene $
 * @access   public
 * @package  PHP2\UI\Components\Additional
 */
class PHP2_UI_Components_Additional_TextBlocks extends PHP2_UI_Control implements PHP2_UI_Components_IViewStack
{
    /**
     * List of the Blocks definitions
     *
     * @var   array
     */
    protected $_blocks = array();

    /**
     * Blocks order data
     *
     * @var   array
     */
    protected $_blocksOrder = array();

    /**
     * Class constructor
     *
     * @param   PHP2_UI_ControlDefinition $controlDefinition control definition Object
     * @access  public
     */
    public function __construct($controlDefinition = null)
    {
        /**
         * Calling parent constructor
         */
        parent::__construct($controlDefinition);
    }

    /**
     * Initialize control
     *
     * @return  void
     * @access  protected
     */
    protected function _init()
    {
        parent::_init();

        /**
         * Set event handlers
         */
        $this->addEventListener(PHP2_UI_UIEvent::LOAD_SESSION, '_loadFromSession', $this);
        $this->addEventListener(PHP2_UI_UIEvent::SAVE_SESSION, '_saveToSession', $this);

        /**
         * Parsing blocks
         */
        $blocksDefinitions = PHP2_UI_TemplateParser::parseBlockTemplate($this->_controlDefinition->content, 'block');
        if (isset($blocksDefinitions['blocks']))
        {
            foreach ($blocksDefinitions['blocks'] as $blockIndex => &$blockDetails)
            {
                $this->addBlock($blockDetails['blockName'], $blockDetails['content']);
            }
        }
    }

    /**
     * Adds text block to the control
     *
     * @param   string $blockName
     * @param   string $blockTemplate
     * @param   boolean $visible
     * @return  void
     */
    public function addBlock($blockName, $blockTemplate, $visible = true)
    {
        $blockItemPos = count($this->_blocksOrder);
        $this->_blocksOrder[$blockItemPos] = $blockName;
        $this->_blocks[$blockName] = array('name' => $blockName, 'text' => $blockTemplate, 'position' => $blockItemPos, 'visible' => true, );
    }

    /**
     * Removes block by its ID
     *
     * @param   string $blockName
     */
    public function removeBlock($blockName)
    {
        if (isset($this->_blocks[$blockName])) unset($this->_blocks[$blockName]);
    }

    /**
     * Unsets block order to default
     */
    public function resetBlocksOrder()
    {
        $this->_blocksOrder = array();

        foreach ($this->_blocks as $blockName => $blockDetails)
        {
            $this->_blocksOrder[] = $blockName;
        }
    }

    /**
     * Set order of the blocks
     *
     * @todo Implement blocks order
     * @param   array $blocksOrder
     */
    public function setBlocksOrder($blocksOrder = null)
    {
        ;
    }

    /**
     * Set block as visible
     *
     * @param   array $blockName
     */
    public function show($blockName = null)
    {
        if (isset($this->_blocks[$blockName]))
        {
            $this->_blocks[$blockName]['visible'] = true;
        }
    }

    /**
     * Set blocks as visible
     *
     * @param   array $blocksList
     */
    public function showBlocks($blocksList = null)
    {
        if (!$blocksList) return;

        foreach ($blocksList as $blockName)
        {
            $this->show($blockName);
        }
    }

    /**
     * Set block as hidden
     *
     * @param   string $blockName
     */
    public function hide($blockName = null)
    {
        if (isset($this->_blocks[$blockName]))
        {
            $this->_blocks[$blockName]['visible'] = false;
        }
    }

    /**
     * Hide blocks
     *
     * @param   array $blocksList
     */
    public function hideBlocks($blocksList = null)
    {
        if (!$blocksList) return;

        foreach ($blocksList as $blockName)
        {
            $this->hide($blockName);
        }
    }

    /**
     * Returns rendered content as string
     *
     * @return  string
     * @access  protected
     */
    protected function _getRenderedContent()
    {
        $result = '';

        $blocksCount = count($this->_blocksOrder);
        for ($i = 0; $i < $blocksCount; $i++)
        {
            $blockName = $this->_blocksOrder[$i];
            if (isset($this->_blocks[$blockName]) && $this->_blocks[$blockName]['visible'])
            {
                $result .= $this->_blocks[$blockName]['text'];
            }
        }

        $attributes = array();
        if ($this->owner) $attributes += $this->owner->templateVariables->getTemplateVariables();
        if ($this->container && ($this->container != $this->owner)) $attributes += $this->container->templateVariables->getTemplateVariables();
        $attributes += $this->_attributes;

        return preg_replace('/{{(\w+)}}/e', '(isset($attributes["\1"]) ? $attributes["\1"] : \'\')', $result);;
    }

    /**
     * Handler of the load session event
     *
     * @return  string
     * @access  protected
     */
    protected function _loadFromSession()
    {
    }

    /**
     * Handler of the save to session event
     *
     * @return  string
     * @access  protected
     */
    protected function _saveToSession()
    {
    }
}
