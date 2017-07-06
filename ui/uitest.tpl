<php:panel:pnlHeader template="header.panel.tpl" />

<form id="mainForm" method="post" action="{{selfUrl}}">

    <div class="pageContent">
      <php:messagebox:msbMessageBox />

      <php:panel:pnlLeft template="left.panel.tpl" />

      <p class="pageContentTitle"><span>Login</span></p>

    <div style="float: left; width: 700px;">
      <php:Repeater:rptBlockTest>
          <div style="border: 1px solid #f00; margin: 2px; width: 200px; float: left;">
              Before block {{var1}}<br />
              <block:blcBlock1>Block 1 {{var1}}<br /></block:blcBlock1>
              <block:blcBlock2>Block 2 {{var1}}<br /></block:blcBlock2>
              After block {{var2}}
          </div>
      </php:Repeater:rptBlockTest>

      <div style="clear: both;"></div>

      <php:checkbox:chkbCheckbox1 checked="true" />
      <php:checkbox:chkbCheckbox2 checked="false" />
      <php:TextArea:txtTextArea style="width: 300px; height: 100px;">Some initial content</php:TextArea:txtTextArea>

      <php:radiobutton:rbtRadio1 group="list" checked="true" />
      <php:radiobutton:rbtRadio2 group="list" checked="false" />
      <php:radiobutton:rbtRadio3 group="list" checked="false" />

      <php:dropdownlist:ddlTestList autoSubmit="true">
          <option value="opt1">Option 1
          <option value="opt2" selected="false">Option 2</option>
          <option vAlue="opt3" Selected="True">Option 3
          <option Value="opt4">Option 4
      </php:dropdownlist:ddlTestList>

      <php:codecolorer:codeColorerExample language="php2" type="xml">
          <php:panel:pnlColorerExample language="php2" type="xml">
              <php:radiobutton:rbtRadio1 group="list" checked="true" />
              <php:radiobutton:rbtRadio2 group="list" checked="false" />
              <php:radiobutton:rbtRadio3 group="list" checked="false" />

              <php:dropdownlist:ddlTestList autoSubmit="true">
                  <option value="opt1">Option 1
                  <option value="opt2" selected="false">Option 2</option>
                  <option vAlue="opt3" Selected="True">Option 3
                  <option Value="opt4">Option 4
              </php:dropdownlist:ddlTestList>
          </php:panel:pnlColorerExample>
      </php:codecolorer:codeColorerExample>

      <div style="margin-top: 20px;"></div>

      <php:codecolorer:codeColorerExample2 language="php" type="default" file="{BASE_PATH}application/bslayer/usersecurity.class.php" />

     </div>

     <php:panel:pnlFormContainer isolated="false">

     <div style="clear: both;"></div>


     <php:viewStack:vsSomeState>
         <state:stState1 attr1="ss" attr2="dd">
             <php:edit:txtEmail_1 class="textField" />
         </state:stState1>
         <state:stState2>
            content 2
             <php:edit:txtEmail_2 class="textField" />

             <php:validator:vldEmail2 control="txtEmail_2" minLength="1" maxLength="50" />
         </state:stState2>
         <state:stState3>
             content 3
             <php:edit:txtEmail_3 class="textField" />
         </state:stState3>
     </php:viewStack:vsSomeState>

     <div style="padding: 10px;">

     <!-- Begin of the Datagrid -->
     <php:datagrid:odgTestDataGrid class="dataGrid" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
        <row:header>
          <col>&nbsp;</col>
          <col>&nbsp;</col>
          <col sortfield='ContinentID'>Continent ID</col>
          <col sortfield='CountryName'>Country Name</col>
          <col>Other cell</col>
          <col sortfield='CountryCode'>Country Code</col>
          <col sortfield='PhoneCode'>PhoneCode</col>
        </row>
        <row:body>
          <col class="dgEdit"><a href="{{editUrl}}">Edit</a></col>
          <col class="dgDel"><a href="javascript: if(confirm('Are you sure you want to delete this record?')){{{deleteUrl}}}">Delete</a></col>
          <col>{{ContinentID}}</col>
          <col>{{CountryName}}</col>
          <col>Empty cell</col>
          <col>{{CountryCode}}</col>
          <col>&nbsp;{{PhoneCode}}</col>
        </row>
     </php:datagrid:odgTestDataGrid>
     <!-- End of the Datagrid -->


     </div>

      <div class="pageContentBlock">
          <div class="formBlockBox">
              <div class="centerBlock">
                  <ul class="formLine">
                      <li class="fieldNameCell">Login:</li>
                      <li class="fieldDataCell">
                          <php:edit:txtLogin class="textField" />
                          <php:validator:vldLogin onBlur="false" showMessage="false" control="txtLogin" minLength="1" maxLength="10" regExp="username" button="btnLogin2" />
                      </li>
                  </ul>

                  <ul class="formLine">
                      <li class="fieldNameCell">Email:</li>
                      <li class="fieldDataCell">
                          <php:edit:txtEmail class="textField" />
                          <php:validator:vldEmail control="txtEmail" minLength="1" maxLength="50" regExp="email" button="btnLogin" />
                      </li>
                  </ul>

                  <ul class="formLine">
                      <li class="fieldNameCell">Url:</li>
                      <li class="fieldDataCell">
                          <php:edit:txtUrl class="textField" />
                          <php:validator:vldUrl control="txtUrl" minLength="1" maxLength="255" regExp="url" button="btnLogin" />
                      </li>
                  </ul>

                  <ul class="formLine">
                      <li class="fieldNameCell">Password:</li>
                      <li class="fieldDataCell">
                          <php:password:txtPassword class="textField" />
                      </li>
                  </ul>
                  <div class="floatClear"></div>

                  <div class="formLine">
                      <div class="actionsLine">
                          <php:submit:btnLogin class="button" value="Url and Email Validate" />
                          <php:submit:btnLogin2 class="button" value="Login validate" />
                      </div>
                  </div>
              </div>
              <div class="floatClear"></div>
          </div>
      </div>

      </php:panel:pnlFormContainer>
    </div>

</form>

<script type="text/javascript">
    /*jQuery.post(PHP2.Url.getInstance().getUrl('/webservice/wstest.php', {"__callHandler": 'getRegisteredHandlers'}), {}, function(data){
        var a = 111;
    }, "json");*/
</script>

<php:panel:pnlFooter template="footer.panel.tpl" />