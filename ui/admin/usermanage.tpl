<php:panel:pnlHeader template="admin/header.panel.tpl" />

<form id="mainForm" method="post" action="{{selfUrl}}">
	<div id="content">
		<div class="contentPanel">
			<div class="topPanel">
				<div class="rightTopPanel">
					<div class="centerTopPanel">&nbsp;</div>
				</div>
			</div>
			<div class="middlePanel">
				<div class="rightMiddlePanel">
					<div class="centerMiddleTopPanel whitePanel">
						<div class="wpTop"><div>&nbsp;</div></div>
						<div class="wpMiddle">
							<div class="wpMiddleInner">
								<h2><span>&nbsp;</span>Manage Users</h2>
								<div class="pageMessages"><php:messagebox:pageMessages /></div>

								<php:viewStack:vsManageRecord default="stViewList">

									<state:stViewList>
										<div style="padding: 4px; width:95%; margin:0 auto;">

											<dl class="clearAfter" style="margin-top: 15px; margin-bottom: 15px; position: relative;">
												<dt style="float: left;">
													<input class="btnAddNew" type="image" name="image" src="{{systemStaticUrl}}/images/blank.gif" value="Add" onclick="$('#btnAddNew').click();"/>
													<php:submit:btnAddNew value="Add" type="hidden" />
												</dt>
												<dd style="float: left; padding-top: 2px;"><label style="cursor: pointer; padding-left: 5px" for="btnAddNew">Add new user</label></dd>
											</dl>

											<!-- Begin of the DataGrid -->
											<php:datagrid:odgDataGrid class="dataGrid" width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
											<row:header>
												<col>&nbsp;</col>
												<col>&nbsp;</col>
												<col sortfield='GroupName'>Group</col>
												<col sortfield='Login'>User Name</col>
												<col sortfield='EMail'>E-Mail</col>
												<col sortfield='CountryName'>Country</col>
												<col sortfield='RegistrationDate'>Registration Date</col>
												<col>Last Login</col>
												<col>Logins Count</col>
												<col sortfield='Enabled'>Enabled</col>
												<col sortfield='Confirmed'>Confirmed</col>
											</row>
											<row:body>
												<col class="dgEdit"><a class="dgEditLink" href="javascript: {{editUrl}}">&nbsp;</a></col>
												<col class="dgDel"><a class="dgDelLink" href="javascript: if(confirm('Are you sure you want to delete this record?')){{{deleteUrl}}}">&nbsp;</a></col>
												<col>{{GroupName}}</col>
												<col>{{Login}}</col>
												<col>{{EMail}}</col>
												<col>{{CountryName}}</col>
												<col>{{RegistrationDate}}</col>
												<col>{{LastLogin}}</col>
												<col>{{LoginsCount}}</col>
												<col>{{Enabled}}</col>
												<col>{{Confirmed}}</col>
											</row>
											</php:datagrid:odgDataGrid>
											<!-- End of the DataGrid -->
										</div>
									</state:stViewList>

									<state:stEditRecord>

										<dl class="dlLogin">
											<dt><label for="txtLogin">Login:</label></dt>
											<dd>
												<php:edit:txtLogin class="textField" />
												<php:validator:vldLogin onBlur="true" showMessage="false" control="txtLogin" minLength="1" maxLength="20" regExp="username" button="btnApplyEdit" />
											</dd>

											<dt><label for="ddlGroup">Group:</label></dt>
											<dd><php:dropdownlist:ddlGroup /></dd>

											<dt><label for="txtEmail">Email:</label></dt>
											<dd>
												<php:edit:txtEmail class="textField" />
												<php:validator:vldEmail control="txtEmail" minLength="1" maxLength="256" regExp="email" button="btnApplyEdit" />
											</dd>

											<dt><label for="txtPassword">Password:</label></dt>
											<dd><php:password:txtPassword class="textField" /></dd>

											<dt><label for="txtPasswordRetyped">Retype Password:</label></dt>
											<dd><php:password:txtPasswordRetyped class="textField" /></dd>

											<dt><label for="txtFirstName">First Name:</label></dt>
											<dd><php:edit:txtFirstName class="textField" /></dd>

											<dt><label for="txtLastName">Last Name:</label></dt>
											<dd><php:edit:txtLastName class="textField" /></dd>

											<dt><label for="ckbEnabled">Enabled:</label></dt>
											<dd><php:checkbox:ckbEnabled /></dd>

											<dt><label for="ckbConfirmed">Confirmed:</label></dt>
											<dd><php:checkbox:ckbConfirmed /></dd>

											<dt><label>&nbsp;</label></dt>
											<dd>
												<input class="btnApply" type="image" name="image" src="{{systemStaticUrl}}/images/blank.gif" value="Apply" onclick="$('#btnApply').click();"/>
												<php:submit:btnApplyEdit id="btnApply" value="Apply" style="display: none;" />
												<input class="btnCancel" type="image" name="image" src="{{systemStaticUrl}}/images/blank.gif" value="Cancel" onclick="$('#btnCancel').click();"/>
												<php:submit:btnCancelEdit id="btnCancel" value="Cancel" style="display: none;" />
											</dd>
										</dl>

										<php:PostBack:pbdsDataStorage useSession="true" />

									</state:stEditRecord>
								</php:viewStack:vsManageRecord>

							</div>
						</div>
						<div class="wpBottom"><div>&nbsp;</div></div>
					</div>
				</div>
			</div>
			<div class="bottomPanel">
				<div class="rightBottomPanel">
					<div class="centerBottomPanel">&nbsp;</div>
				</div>
			</div>
		</div>
	</div>
</form>

<php:panel:pnlFooter template="admin/footer.panel.tpl" />