
if (typeof(PHP2) == "undefined") PHP2 = new Object();

/**
 * Core PHP2 features
 *
 * @inherits {Object}
 * @constructor
 */
PHP2.Core = function()
{
};

/**
 * Dispatches server event
 *
 * @param   {String} objectName
 * @param   {String} eventDetails
 * @param   {String} formId
 * @return  {Boolean}
 * @static
 */
PHP2.Core.dispatchServerEvent = function (objectName, eventDetails, formId)
{
    if (!formId) formId = 0;

    var formObject = null;
    formObjectFieldId = objectName + '_dispatchEvent';

    if (typeof(document.forms[formId]) != "undefined")
    {
        formObject = jQuery(document.forms[formId]);
    }
    else
    {
        formObject = jQuery(formId);
    }

    if (!jQuery('#' + formObjectFieldId).length) formObject.append('<input type="hidden" id="' + formObjectFieldId + '" name="' + formObjectFieldId + '" />');

    jQuery('#' + formObjectFieldId).val(eventDetails);

    formObject.submit();

    return true;
};
