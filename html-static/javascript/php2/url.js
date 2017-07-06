
if (typeof(PHP2) == "undefined") PHP2 = new Object();

/**
 * Main URL management class
 *
 * @param    {String} rootUrl
 * @param    {String} staticRootUrl
 * @inherits {Object}
 * @constructor
 */
PHP2.Url = function(rootUrl, staticRootUrl)
{
    if (typeof(PHP2.Url._instance) != "undefined") alert("Error: PHP2.Url singleton reinitialized.");

    this.init(((rootUrl) ? rootUrl : "/"), ((staticRootUrl) ? staticRootUrl : "/"));
};

/**
 * Returns singleton instance of the PHP2.Url
 *
 * @return  {PHP2.Url} the instance of PHP2.Url class
 * @static
 */
PHP2.Url.getInstance = function ()
{
    if (typeof(PHP2.Url._instance) != "undefined") return PHP2.Url._instance;

    PHP2.Url._instance = new PHP2.Url();

    return PHP2.Url._instance;
};


/**
 * Returns valid query string for POST data
 *
 * @param   {Object} data
 * @param   {String} objectPrefix
 * @return  {String} valid POST string
 * @type    {String}
 * @static
 */
PHP2.Url.getPostString = function (data, objectPrefix)
{
    var result = "";

    /**
     * Ignoring function variables
     */
    if (typeof(data) == "function") return "";

    if (typeof(data) == "object")
    {
        for (var index in data)
        {
            if (typeof(data[index]) == "function") continue;

            currParamName = objectPrefix ? objectPrefix + "[" + index + "]" : index;
            if (typeof(data[index]) == "object")
            {
                result += PHP2.Url.getPostString(data[index], currParamName);
            }
            else
            {
                result += "&" + currParamName + "=" + data[index];
            }
        }
    }
    else
    {
        result = new String(data);
    }

    return result;
};

/**
 * Init/Reinit main url parameters
 *
 * @param   {String} rootUrl
 * @param   {String} staticRootUrl
 * @param   {String} sslRootUrl
 * @param   {String} sslStaticRootUrl
 */
PHP2.Url.prototype.init = function (rootUrl, staticRootUrl, sslRootUrl, sslStaticRootUrl)
{
    this._rootUrl           = (rootUrl) ? rootUrl + ((rootUrl[rootUrl.length - 1] != '/') ? "/" : "") : "";
    this._staticRootUrl     = (staticRootUrl) ? staticRootUrl + ((staticRootUrl[staticRootUrl.length - 1] != '/') ? "/" : "") : "";
    this._sslRootUrl        = sslRootUrl ? sslRootUrl : this._rootUrl.replace("http://", "https://");
    this._sslStaticRootUrl  = sslStaticRootUrl ? sslStaticRootUrl : this._staticRootUrl.replace("http://", "https://");
};

/**
 * Checks is current request is made under SSL
 *
 * @return {Boolean} is SSL request flag
 */
PHP2.Url.prototype.isSSL = function ()
{
    var currentUrl = new String(document.location.href);

    return (currentUrl.indexOf("https://") != -1);
};

/**
 * Return root URL of the project
 *
 * @return {String} root Url
 */
PHP2.Url.prototype.getRootUrl = function ()
{
    return this._rootUrl;
};

/**
 * Return root URL for Static content of the project
 *
 * @return {String} static root Url
 */
PHP2.Url.prototype.getStaticUrl = function ()
{
    return this._staticRootUrl;
};

/**
 * Returns valid URL based
 *
 * @param   {String} url
 * @param   {Object} params
 * @param   {Boolean} isSSL
 * @return  {String} valid system url
 */
PHP2.Url.prototype.getUrl = function (url, params, isSSL)
{
    // --- Creating root Url --- //
    var result = "";
    if (typeof(isSSL) != "undefined")
    {
        result = ((!isSSL) ? this._rootUrl : this._sslRootUrl) + url;
    }
    else
    {
        result = ((!this.isSSL()) ? this._rootUrl : this._sslRootUrl) + url;
    }

    if (typeof(params) == "undefined") params = new Object();

    var i = 0;
    for (var indexName in params)
    {
        if (typeof(params[indexName]) != "object") result += ((i++) ? '&' : '?') + indexName + "=" + params[indexName];
    }

    return result;
};
