
if (typeof(PHP2) == "undefined") PHP2 = new Object();

PHP2.ValidatorRegExpCollection = new Object();

PHP2.ValidatorRegExpCollection.TYPE_EMAIL     = /\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*/;
PHP2.ValidatorRegExpCollection.TYPE_USERNAME  = /[а-яА-Я\w\d\_\-]+/;
PHP2.ValidatorRegExpCollection.TYPE_LETTER    = /[a-zA-Z]+/;
PHP2.ValidatorRegExpCollection.TYPE_FLOAT     = /[-+]?[0-9]*\.?[0-9]*/;
PHP2.ValidatorRegExpCollection.TYPE_URL       = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?([\w\.\-]+)?(:[0-9]+)?(\/([\w\#\!\:\.\?\;\+\=\&\%@!\-\/]*))?/;

/**
 * Validator class
 *
 * @param    {String}  controlId
 * @param    {Integer} minLength
 * @param    {Integer} maxLength
 * @param    {String}  regExp
 * @inherits {Object}
 * @constructor
 */
PHP2.Validator = function(controlId, minLength, maxLength, regExp)
{
    this._controlId  = controlId;
    this._minLength  = minLength;
    this._maxLength  = maxLength;

    var regExpString = new String(regExp);
    var regExpIndex  = 'TYPE_' + regExpString.toUpperCase();
    this._regExp     = regExp;
    if (typeof (PHP2.ValidatorRegExpCollection[regExpIndex]) != "undefined")
    {
        this._regExp = PHP2.ValidatorRegExpCollection[regExpIndex];
    }

    this._errorsList = new Array();

    this._vldErrorClass  = 'validationError';
    this._vldResultPanel = null;
    this.showMessages    = true;
};

/**
 * Registers validation result panel
 *
 * @param   {String}  resultPanelId
 * @param   {Boolean}  showMessages
 * @return  {void}
 */
PHP2.Validator.prototype.setResultPanel = function (resultPanelId, showMessages)
{
    this._vldResultPanel = resultPanelId;
    this.showMessages    = (showMessages === false) ? false : true;
};

/**
 * Registers validation handlers
 *
 * @param   {String}  submitControlId
 * @param   {Boolean} validateOnBlur
 * @return  {void}
 */
PHP2.Validator.prototype.registerHandlers = function (submitControlId, validateOnBlur)
{
    var $this = this;

    /**
     * Validation function wrapper
     *
     * @return {boolean}
     */
    var fncValidate = function(){return $this.validate();};

    this._controlObject = jQuery('#' + this._controlId).eq(0);

    /**
     * Set form validation logic
     */
    if (submitControlId)
    {
        if ((btnSubmit = jQuery('#' + submitControlId)) && btnSubmit.length) btnSubmit.click(fncValidate);
    }
    else if (this._controlObject.length)
    {
        if (frmForValidate = this._controlObject.get(0).form) jQuery(frmForValidate).submit(fncValidate);
    }

    /**
     * Checking onBlur() validation
     */
    if (validateOnBlur)
    {
        jQuery('#' + this._controlId).unbind('blur').blur(fncValidate);
    }
};

/**
 * Validation method
 *
 * @return  {Boolean} validation result
 */
PHP2.Validator.prototype.validate = function ()
{
    var result        = true;
    this._errorsList  = new Array();

    /**
     * Validating Min length of the Value
     */
    if (this._minLength > 0)
    {
        if (this._controlObject.val().length < this._minLength)
        {
            if (this._minLength > 1)
            {
                this._errorsList[this._errorsList.length] = PHP2.MultiLang.getInstance().get("VALIDATOR_SMALL", {length: this._minLength});
            }
            else if (this._minLength == 1)
            {
                this._errorsList[this._errorsList.length] = PHP2.MultiLang.getInstance().get("VALIDATOR_EMPTY");
            }
            result = false;
        }
    }

    /**
     * Validating Max length of the Value
     */
    if (this._maxLength > 0)
    {
        if (this._controlObject.val().length > this._maxLength)
        {
            this._errorsList[this._errorsList.length] = PHP2.MultiLang.getInstance().get("VALIDATOR_TO_BIG");
            result = false;
        }
    }

    /**
     * Validating value with Regexp
     */
    if (result && (this._regExp != null))
    {
        var validatedValue   = new String(this._controlObject.val());
        var regExpObject     = new RegExp(this._regExp);
        var regExMatchResult = regExpObject.exec(validatedValue);
        var regexpResult     = false;

        if (regExMatchResult != null)
        {
            for (var i = 0; i < regExMatchResult.length; i++)
            {
                if (regExMatchResult[i] == validatedValue) regexpResult = true;
            }
        }

        if (!regexpResult)
        {
            this._errorsList[this._errorsList.length] = PHP2.MultiLang.getInstance().get("VALIDATOR_INVALID_FORMAT");
            result = false;
        }
    }

    /**
     * Addign/Removing error class to the validated control
     */
    if (!result)
    {
        if (!this._controlObject.hasClass(this._vldErrorClass)) this._controlObject.addClass(this._vldErrorClass);
        if (this._vldResultPanel)
        {
            if (this.showMessages) jQuery('#' + this._vldResultPanel).html('* ' + this._errorsList[0]);
            jQuery('#' + this._vldResultPanel).show();
        }
    }
    else
    {
        this._controlObject.removeClass(this._vldErrorClass);
        if (this._vldResultPanel) jQuery('#' + this._vldResultPanel).hide();
    }

    return result;
};

/**
 * Validation summary
 *
 * @param    {String}  controlId
 * @inherits {Object}
 * @constructor
 */
PHP2.ValidationSummary = function(controlId)
{
    this._controlId      = controlId;
    this._vldErrorClass  = 'validationError';
    this._errorsList     = new Array();
};

/**
 * Add validation error
 *
 * @return  {void}
 */
PHP2.ValidationSummary.prototype.add = function ()
{
    ;
};