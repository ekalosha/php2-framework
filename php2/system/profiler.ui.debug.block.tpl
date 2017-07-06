
<script type="text/javascript">
  window.onload = function() {
      var styleObject   = document.createElement('link');
      styleObject.rel   = 'stylesheet';
      styleObject.id    = 'debugStyles';
      styleObject.media = 'screen';
      styleObject.type  = 'text/css';
      // styleObject.href  = 'data:text/css,' + escape("@import url('{{staticUrl}}/styles/debug.styles.css'");
      styleObject.href  = '{{staticUrl}}/styles/debug.styles.css';
      document.getElementsByTagName('head')[0].appendChild(styleObject);
  }
</script>

<div style="width: 100%;" id="debugArea">

    <br/>
    <br/>

    <div class="debugArea">

      <div style="width: 100%; float: left;">
        <div class="debugGroupContainer" style="width: 47%; float: right;">
          <div class="headerText">General info:</div>
          <table cellspacing="0" cellpadding="0" border="1" class="debugTable">
            <block:blcGeneralInfo><tr><td>{{variableName}}</td><td>{{variableValue}}</td></tr></block:blcGeneralInfo>
          </table>
        </div>

        <div class="debugGroupContainer" style="width: 47%; float: left;">
          <div class="headerText">Template system debug info:</div>
          <table cellspacing="0" cellpadding="0" border="1" class="debugTable">
              <tr><th style="width: 1px">N</th><th>Caption</th><th style="width: 60px">Time</th></tr>
              <block:blcGroup>
                  <tr><td colspan="4">&nbsp;</td></tr>
                  <tr><td colspan="4"><strong>Group: {{groupName}}</strong></td></tr>
                  <block:blcItemInfo>
                      <tr>
                          <td class="lineNum">{{index}}</td>
                          <td class="queryText">{{caption}}</td>
                          <td class="execTime">{{execTime}}</td>
                      </tr>
                  </block:blcItemInfo>
                  <tr><td class="total" colspan="2">Total items count: {{totalCount}}</td><td class="total">{{totalTime}}</td></tr>
              </block:blcGroup>
          </table>
        </div>
      </div>
      <div style="clear: both;"></div>

        <block:blcTracedVariables>
        <div class="debugGroupContainer">
          <div class="headerText">Traced variables:</div>
          <table cellspacing="0" cellpadding="0" border="1" class="debugTable">
              <tr><th style="width: 1px">N</th><th style="width: 200px">Type</th><th>Variable Value</th></tr>
              <block:blcItemInfo>
                  <tr>
                      <td class="lineNum">{{index}}</td>
                      <td class="variableType">{{type}}</td>
                      <td>
                          <div class="variableName"><b>Variable name:</b> {{name}}<hr size="-1" color="black"></div>
                          <div class="traceResult"><pre>{{value}}</pre></div>
                      </td>
                  </tr>
              </block:blcItemInfo>
          </table>
          <br/>
        </div>
        </block:blcTracedVariables>

        <block:blcDatabase>
        <div class="debugGroupContainer">
          <div class="headerText">Database Layer debug info:</div>
          <div align="right">
              <table class="queryTypesInfo">
                  <tr class="successQuery"><td>Success query</td></tr>
                  <tr class="needsOptimization"><td>Success query, but needs optimization</td></tr>
                  <tr class="invalidQuery"><td>Invalid query</td></tr>
              </table>
          </div>
          <table cellspacing="0" cellpadding="0" border="1" class="debugTable">
              <tr><th style="width: 1px">N</th><th>Query</th><th style="width: 60px">Execution Time</th></tr>
              <block:blcDBGroup>
                  <tr><td class="queryText" colspan="4">&nbsp;</td></tr>
                  <tr><td class="queryText" colspan="4"><strong>Group: {{dbGroupName}}</strong></td></tr>
                  <block:blcQueryInfo>
                      <tr class="{{queryResultClass}}">
                          <td class="lineNum">{{queryNum}}</td>
                          <td class="queryText">{{queryText}}</td>
                          <td class="execTime">{{execTime}}</td>
                      </tr>
                  </block:blcQueryInfo>
                  <tr><td class="total" colspan="2">Total DB queries count: {{totalCount}}</td><td class="total">{{totalTime}}</td></tr>
              </block:blcDBGroup>
              <tr><td colspan="4" class="queryText"></td></tr><tr>
              <td class="total" colspan="2">Total DB queries count: {{totalQueriesCount}}</td><td class="total">{{totalDBTime}}</td></tr>
          </table>
        </div>
        </block:blcDatabase>

        <div class="debugGroupContainer">
          <div class="headerText">Autoload data:</div>
          <table cellspacing="0" cellpadding="0" border="1" class="debugTable">
              <tr><th style="width: 1px">N</th><th>Class Name:</th><th style="width: 60px">Load Time</th></tr>
              <block:blcProfilingAutoload><tr><td>{{index}}.</td><td>{{className}}</td><td align="right">{{loadTime}}</td></tr></block:blcProfilingAutoload>
              <tr><td class="total" colspan="2">Total loaded classes: {{classesCount}}</td><td class="total">{{totalAutoloadTime}}</td></tr>
          </table>
        </div>

    </div>
</div>