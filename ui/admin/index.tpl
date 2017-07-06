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
										<form id="mainForm" method="post" action="{{selfUrl}}">			
											<h2><span>&nbsp;</span>Welcome</h2>
	                                        <div class="pageMessages">
												Hi {{userName}}, <a href="~/login.php?action=logout" title="Logout">Logout</a><br />
												Last login time: {{lastLogin}}, <br />
												Logins count: {{loginsCount}}
											</div>
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

<php:panel:pnlFooter template="admin/footer.panel.tpl" />