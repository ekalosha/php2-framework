<php:panel:pnlHeader template="header.panel.tpl" />
		
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
										<form id="mainForm" method="post" action="{{selfUrl}}">			
											<h2><span>&nbsp;</span>Login</h2>
	
	                                        <div class="pageMessages"><php:messagebox:pageMessages /></div>
	
	                                        <dl class="dlLogin">
	                                            <dt><label for="txtLogin">Login:</label></dt>
	                                            <dd><div class="leftCornerInput"><php:edit:txtLogin class="textField" /></div></dd>
	
	                                            <dt><label for="txtPassword">Password:</label></dt>
	                                            <dd><div class="leftCornerInput"><php:password:txtPassword class="textField" /></div></dd>
	
	                                            <dt><label for="ckbRememberMe">Remember me:</label></dt>
	                                            <dd><php:checkbox:ckbRememberMe class="checkbox" /></dd>
	
	                                            <dt><label>&nbsp;</label></dt>
	                                            <dd>
	                                            	<input class="btnLogin" type="image" name="image" src="{{systemStaticUrl}}/images/blank.gif" value="Login" onclick="$('#btnLogin').click();"/>																
	                                            	<php:submit:btnLogin id="btnLogin" class="button" value="Login" style="display: none;" />
												</dd>
	                                        </dl>
									
										</form>
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

<script type="text/javascript">
    /*jQuery.post(PHP2.Url.getInstance().getUrl('/webservice/wstest.php', {"__callHandler": 'getRegisteredHandlers'}), {}, function(data){
        var a = 111;
    }, "json");*/
</script>

<php:panel:pnlFooter template="footer.panel.tpl" />