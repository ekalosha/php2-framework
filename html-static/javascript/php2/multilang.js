
if (typeof(PHP2) == "undefined") PHP2 = new Object();

/**
 * Registering default system translations
 */
PHP2.MultiLangWords = new Object();
PHP2.MultiLangWords.VALIDATOR_EMPTY  = 'The element could not be empty';
PHP2.MultiLangWords.VALIDATOR_SMALL  = 'The length should be more than {{length}} symbols';
PHP2.MultiLangWords.VALIDATOR_TO_BIG = 'The maximum length exceeded';
PHP2.MultiLangWords.VALIDATOR_INVALID_FORMAT = 'Invalid format';

/**
 * Multilanguage support class
 *
 * @inherits {Object}
 * @constructor
 */
PHP2.MultiLang = function()
{
	if (typeof(PHP2.MultiLang._instance) != "undefined") alert("Error: PHP2.MultiLang singleton reinitialized.");
};

/**
 * Returns singleton instance of the PHP2.MultiLang
 *
 * @return  {PHP2.MultiLang} the instance of PHP2.MultiLang class
 * @static
 */
PHP2.MultiLang.getInstance = function ()
{
	if (typeof(PHP2.MultiLang._instance) != "undefined") return PHP2.MultiLang._instance;

	PHP2.MultiLang._instance = new PHP2.MultiLang();

	return PHP2.MultiLang._instance;
};

/**
 * Returns translation of the sentence by its code
 *
 * @param   {String} wordCode
 * @param   {Object} replace
 * @return  {String}
 */
PHP2.MultiLang.prototype.get = function (wordCode, replace)
{
	var result = wordCode;

	if (typeof(PHP2.MultiLangWords[wordCode]) != "undefined") result = new String(PHP2.MultiLangWords[wordCode]);

	if (typeof(replace) == "object")
	{
		for (var fieldName in replace)
		{
			result = result.replace(new RegExp('{{' + fieldName + '}}', 'g'), replace[fieldName]);
		}
	}

	return result;
};
