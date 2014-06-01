	<!-- Begin Footer -->

		<?php
		$localVars = localVars::getInstance();
		if ($localVars->get("excludeToolbar") != "TRUE") {
			?>

		<footer id="toolBar">
			<p>
				<!-- Lockerz Share BEGIN -->
				<a class="a2a_dd" href="http://www.addtoany.com/share_save"><img src="http://static.addtoany.com/buttons/share_save_120_16.gif" width="120" height="16" border="0" alt="Share"/></a>
				<script type="text/javascript">
				var a2a_config = a2a_config || {};
				a2a_config.onclick = 1;
				a2a_config.prioritize = ["facebook", "google_plus", "twitter", "email", "blogger_post", "digg", "linkedin", "reddit", "delicious", "myspace"];
				</script>
				<script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>
				<!-- Lockerz Share END -->
			</p>
			<p>last updated: {date format="F j, Y" time="<?php print filemtime($_SERVER['SCRIPT_FILENAME']); ?>"}</p>
			<p>
				<a href="mailto:Jessica.Tapia@mail.wvu.edu">Report an error on this page <img src="{local var="imgURL"}/reportError.png" alt="Report Error" /></a>
			</p>
		</footer>

		<?php } ?>

	</div>

	<footer>
		<section id="siteSearch">
			<span class="header">
				<label for="siteSearchInput">Search Library Pages</label>
			</span>

			<form method='get' action='http://search.wvu.edu/search'>
				<input type='hidden' name='as_sitesearch' value='www.libraries.wvu.edu' />
				<input type='hidden' name='client' value='default_frontend' />
				<input type='hidden' name='output' value='xml_no_dtd' />
				<input type='hidden' name='proxystylesheet' value='default_frontend' />
				<input type='search' name='q' id="siteSearchInput" value="" />
				<input type="submit" value="Search" />
			</form>

		</section>

		<div id="footerLinks">
			<section id="popularResources">
				<!-- These should really be in header blocks, but not allowed in footer -->
				<h2>POPULAR RESOURCES</h2>
				<ul>
					<li>
						<a href="http://mountainlynx.lib.wvu.edu" title="Library Catalog">Library Catalog</a>
					</li>
					<li>
						<a href="http://illiad.lib.wvu.edu" title="Interlibrary Loan">Interlibrary Loan</a>
					</li>
					<li>
						<a href="http://www.libraries.wvu.edu/ejournals/" title="eJournals">eJournals</a>
					</li>
					<li>
						<a href="http://reserves.lib.wvu.edu" title="Reserves &amp; eReserves">Reserves</a>
					</li>
					<li>
						<!-- <a href="http://www.libraries.wvu.edu/databases/cgi-bin/databases.pl?1185309093=invs" title="Refworks">Refworks</a> -->
						<a href="http://systems.lib.wvu.edu/availableComputers" title="Available Computers">Available Computers</a>
					</li>
					<li>
						<a href="http://www.libraries.wvu.edu/databases/" title="Databases">Databases</a>
					</li>
					<li>
						<a href="http://ad4tq3gq5x.search.serialssolutions.com/?SS_Page=refiner&amp;SS_RefinerEditable=yes" title="Have a Citation? Find it @WVU">Find it @WVU</a>
					</li>
					<li>
						<a href="http://libguides.wvu.edu/" title="Research Guides">Research Guides</a>
					</li>
				</ul>
			</section>

			<section id="social">
					<h2>WE'RE SOCIAL</h2>

				<div id="footerFacebook">
					<div class="fb-like" data-send="true" data-width="370" data-show-faces="false"></div>
				</div>
				<div id="footerTwitter">
				</div>

			</section>

			<div id="footerFloatRight">
				<section id="donate">
						<h2>DONATE TO THE LIBRARIES</h2>
					<ul>
						<li>
							<a href="http://www.libraries.wvu.edu/about/friends/">Become a Friend of the Libraries</a>
						</li>
						<li>
							<a href="/about/friends/donations/">Ways to Give</a>
						</li>
						<li>
							<a href="https://www.mountaineerconnection.com/sslpage.aspx?pid=501&section=WVU%20Libraries">Give Online</a>
						</li>
					</ul>
				</section>
				<section>
					<a href="http://www.facebook.com/pages/Morgantown-WV/WVU-Libraries/108610159200233"><img src="{local var="imgURL"}/footer/socialIcons/icon_facebook.gif" alt="Facebook" /></a><a href="/about/friends/donations/"><img src="{local var="imgURL"}/footer/socialIcons/icon_give.gif" alt="Give" /></a><a href="https://mix.wvu.edu/ "><img src="{local var="imgURL"}/footer/socialIcons/icon_mix.gif" alt="Mix" /></a><a href="http://twitter.com/wvuLibraries"><img src="{local var="imgURL"}/footer/socialIcons/icon_twitter.gif" alt="Twitter" /></a><a href="/rss/"><img src="{local var="imgURL"}/footer/socialIcons/newrss.jpg" alt="RSS" /></a><a href="http://www.youtube.com/user/WVULibraries"><img src="{local var="imgURL"}/footer/socialIcons/icon_youtube.gif" alt="YouTube" /></a>
					<a href="/services/ask/"><img src="{local var="imgURL"}/askALibrarianButton.png" alt="Ask a Librarian" /></a>
				</section>
			</div>

		</div>

		<address>
			<p><a href="/about/copyright/" title="WVU Libraries Copyright">&copy; WVU Libraries</a> &nbsp;&nbsp; P.O. Box 6069 WVU &nbsp;&nbsp; 1549 University Ave. &nbsp;&nbsp; Morgantown, WV 26506-6069 &nbsp;&nbsp; Phone: (304) 293-4040 &nbsp;&nbsp; Fax: (304) 293-6638
		</address>
	</footer>

</body>

</html>
