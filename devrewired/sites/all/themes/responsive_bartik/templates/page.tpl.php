<?php

/**
 * @file
 * Bartik's theme implementation to display a single Drupal page.
 *
 * The doctype, html, head, and body tags are not in this template. Instead
 * they can be found in the html.tpl.php template normally located in the
 * core/modules/system directory.
 *
 * Available variables:
 *
 * General utility variables:
 * - $base_path: The base URL path of the Drupal installation. At the very
 *   least, this will always default to /.
 * - $directory: The directory the template is located in, e.g. modules/system
 *   or themes/bartik.
 * - $is_front: TRUE if the current page is the front page.
 * - $logged_in: TRUE if the user is registered and signed in.
 * - $is_admin: TRUE if the user has permission to access administration pages.
 *
 * Site identity:
 * - $front_page: The URL of the front page. Use this instead of $base_path,
 *   when linking to the front page. This includes the language domain or
 *   prefix.
 * - $logo: The path to the logo image, as defined in theme configuration.
 * - $site_name: The name of the site, empty when display has been disabled
 *   in theme settings.
 * - $site_slogan: The slogan of the site, empty when display has been disabled
 *   in theme settings.
 * - $hide_site_name: TRUE if the site name has been toggled off on the theme
 *   settings page. If hidden, the "element-invisible" class is added to make
 *   the site name visually hidden, but still accessible.
 * - $hide_site_slogan: TRUE if the site slogan has been toggled off on the
 *   theme settings page. If hidden, the "element-invisible" class is added to
 *   make the site slogan visually hidden, but still accessible.
 *
 * Navigation:
 * - $main_menu (array): An array containing the Main menu links for the
 *   site, if they have been configured.
 * - $secondary_menu (array): An array containing the Secondary menu links for
 *   the site, if they have been configured.
 * - $breadcrumb: The breadcrumb trail for the current page.
 *
 * Page content (in order of occurrence in the default page.tpl.php):
 * - $title_prefix (array): An array containing additional output populated by
 *   modules, intended to be displayed in front of the main title tag that
 *   appears in the template.
 * - $title: The page title, for use in the actual HTML content.
 * - $title_suffix (array): An array containing additional output populated by
 *   modules, intended to be displayed after the main title tag that appears in
 *   the template.
 * - $messages: HTML for status and error messages. Should be displayed
 *   prominently.
 * - $tabs (array): Tabs linking to any sub-pages beneath the current page
 *   (e.g., the view and edit tabs when displaying a node).
 * - $action_links (array): Actions local to the page, such as 'Add menu' on
 *   the menu administration interface.
 * - $feed_icons: A string of all feed icons for the current page.
 * - $node: The node entity, if there is an automatically-loaded node
 *   associated with the page, and the node ID is the second argument
 *   in the page's path (e.g. node/12345 and node/12345/revisions, but not
 *   comment/reply/12345).
 *
 * Regions:
 * - $page['header']: Items for the header region.
 * - $page['featured']: Items for the featured region.
 * - $page['highlighted']: Items for the highlighted content region.
 * - $page['help']: Dynamic help text, mostly for admin pages.
 * - $page['content']: The main content of the current page.
 * - $page['sidebar_first']: Items for the first sidebar.
 * - $page['triptych_first']: Items for the first triptych.
 * - $page['triptych_middle']: Items for the middle triptych.
 * - $page['triptych_last']: Items for the last triptych.
 * - $page['footer_firstcolumn']: Items for the first footer column.
 * - $page['footer_secondcolumn']: Items for the second footer column.
 * - $page['footer_thirdcolumn']: Items for the third footer column.
 * - $page['footer_fourthcolumn']: Items for the fourth footer column.
 * - $page['footer']: Items for the footer region.
 *
 * @see template_preprocess()
 * @see template_preprocess_page()
 * @see template_process()
 * @see bartik_process_page()
 * @see html.tpl.php
 *
 * @ingroup themeable
 */
?>

<div id="page-wrapper"><div id="page">
	
	<div id="main-wrapper" class="clearfix">

		<?php // -------- Background  elements -------------------------------------------------------------------------------------------------------------------------------- ?>
		<div id="main-wrapper-top-left" class="antialias-shadow"></div> 
		<div id="main-wrapper-top-right" class="antialias-shadow"></div> 
		<div id="main-wrapper-top-left-drot" class="drot-shadow"></div>
		<div id="main-wrapper-top-right-drot" class="drot-shadow"></div>  
		<div id="main-wrapper-header-container" class="clearfix">
			<div id="main-wrapper-header-left" class="antialias-shadow"></div>
			<div id="main-wrapper-header-right" class="antialias-shadow"></div>
			<div id="main-wrapper-header-head" class="clearfix"></div>
		</div>
		<div id="main-wrapper-bottom-left-container" class="clearfix"><div id="main-wrapper-bottom-left" class="antialias-shadow"></div></div>
		<div id="main-wrapper-bottom-right-container" class="clearfix"><div id="main-wrapper-bottom-right" class="antialias-shadow"></div></div>  
		<div id="main-wrapper-footer-container" class="clearfix">
			<div id="main-wrapper-footer-left" class="antialias-shadow"></div>
			<div id="main-wrapper-footer-right" class="antialias-shadow"></div>
		</div>  
		<div id="main-wrapper-background" class="clearfix"></div> 
		<?php // -------- Background elements END ------------------------------------------------------------------------------------------------------------------------------- ?>
  
	<div id="main" role="main" class="clearfix">
	
		<?php // -------- Floating Navigation Bar  -------------------------------------------------------------------------------------------------------------------------------- ?>
		<div class="rewired-main-divider">
			<div class="rewired-breadcrumb-container">
			
				<div class="rewired-logo">
					<a href="<?php print $base_url;?>" title="Rewired" id="rewired-logo-link"></a>
				</div>

				<div class="rewired-menu-and-pager"> 

					<div class="rewired-menu"> 
					<?php if ($main_menu): ?>
						<nav id="main-menu" role="navigation" class="navigation">
							<?php print theme('links__system_main_menu', array(
								'links' => $main_menu,
								'attributes' => array(
								'id' => 'main-menu-links',
								'class' => array('links', 'clearfix'),
								),
								'heading' => array(
								'text' => t('Main menu'),
								'level' => 'h2',
								'class' => array('element-invisible'),
								),
							)); 
							?>
						</nav> 
					<?php endif; ?>
					</div>
				</div>
				
				<div class="rewired-breadcrumb"><?php if (isset($breadcrumb)) print $breadcrumb; ?></div>	
				<div class="rewired-breadcrumb-pager"><?php if(null !==theme('pager') && $showpager) echo theme('pager'); 	?></div>
			</div>

		</div>
		<?php // -------- Floating Navigation Bar END -------------------------------------------------------------------------------------------------------------------------------- ?>

			<div class="rewired_mark_all 
				<?php if ($forumroot || 'http://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'] == $base_url . "/?page=0"): ?>
					<?php print 'displayed'; ?>
				<?php endif; ?>
				">
				
				<?php if ($user->uid!=0 ): ?>
					<a href='/forum/markasread' title='Az összes topik megjelölése a Rewireden olvasottként.'>Láttam</a>
				<?php endif; ?>
			</div>
			
			<div id='content' class="<?php echo $pageswithshadow ? ' content_black_background' : ' '?>">
			  <div class="section <?php echo $pageswithshadow ? ' black_shadow_hack' : ' '?>">
				<div id='content-gap' class='clearfix'></div>

				<?php if ($page['highlighted']): ?><div id="highlighted"><?php print render($page['highlighted']); ?></div><?php endif; ?>
				<a id="main-content"></a> 
	  
				<?php print render($title_prefix); ?>
				<?php if ($title): ?>
					<h1 class="title" id="page-title">
					<?php print $title; ?>
					</h1>
				<?php endif; ?>
				<?php print render($title_suffix); ?>

				<?php if ($tabs): ?>
					<div class="tabs">
					<?php print render($tabs); ?>
					</div>
				<?php endif; ?>
				
				
				<?php if ($action_links): ?>
					<ul class="action-links">
						<?php print render($action_links); ?>
					</ul>
				<?php endif; ?>

			<?php // content ---------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------  ?>
			<?php print render($page['content']); ?>
	  
			<?php if ($messages): ?>
				<div id="messages"><div class="section clearfix">
				<?php print $messages; ?>
				</div></div> <!-- /.section, /#messages -->
			<?php endif; ?>
			
		</div></div> <!-- /.section, /#content -->
	</div></div> <!-- /#main, /#main-wrapper -->
</div></div> <!-- /#page, /#page-wrapper -->

<div id="rewired_settings_wrapper">
	<div id="rewired_settings" class="rs_element">
		<div id="rewired_settings_content" class="rs_element">
	
		<div class="rewired_settings_close"></div>

		<div class="rs_section rs_element">
			Profil: <input type="button" name="fpb" class="rs_element button" value="<?php print isset($user->name) ? $user->name : 'Belépés'?>" onclick="updateOutput(event)">
			
			<?php if (isset($user->name)): ?>
				<input type="button" name="fpk" class="rs_element button" value="Kilépés" onclick="updateOutput(event)">
			<?php endif; ?>
		</div>
		<div class="rs_section rs_element">
			Fórumgrafika szint:<br />
			<ul class="rs_element">
			<input type="radio" name="gfx" class="rs_element" value="1" onchange="updateOutput(event)">Minimális<br />
			<input type="radio" name="gfx" class="rs_element" value="2" onchange="updateOutput(event)">Átlagos<br />
			<input type="radio" name="gfx" class="rs_element" value="3" onchange="updateOutput(event)">Maximum<br />
			</ul>
		</div>
		<div class="rs_section rs_element">
			Max betűméret:<br />
			<ul class="rs_element">
			<input type="radio" name="mfm" class="rs_element" value="1" onchange="updateOutput(event)">Kicsi<br />
			<input type="radio" name="mfm" class="rs_element" value="2" onchange="updateOutput(event)">Közepes<br />
			<input type="radio" name="mfm" class="rs_element" value="3" onchange="updateOutput(event)">Nagy<br />
			</ul>
		</div>
		<div class="rs_section rs_element">
			Aláírás elrejtés:<input type="checkbox" name="aie" class="rs_element" onchange="updateOutput(event)">
		</div>
		<div class="rs_section rs_element">
			Profilkép elrejtés:<input type="checkbox" name="pke" class="rs_element" onchange="updateOutput(event)">
		</div>
		<div class="rs_section rs_element">
			Felhasználói paneladatok elrejtése:<input type="checkbox" name="ape" class="rs_element" onchange="updateOutput(event)">
		</div>
		<div class="rs_section rs_element">
			Ultramobil:<input type="checkbox" name="ult" class="rs_element" onchange="updateOutput(event)">
		</div>
		<div class="rs_section rs_element">
			HD kiosztás mód:<input type="checkbox" name="hdm" class="rs_element" onchange="updateOutput(event)">
		</div>
		<div class="rs_section rs_element">
			Max fórumszélesség (<span id="sze_output" class="rs_element"></span>px):<br />
			<input type="range" id="sze" name="sze" class="rs_element" min="600" max="1000" step="25"  oninput="updateOutput(event)" onchange="updateOutput(event)"><br />
		</div>
		
		</div>
	</div>
</div>

<div id="picture_viewer_wrapper" class= "pv_element">
		<div id="picture_viewer" class= "pv_element">
			<img src="//:0" class= "pv_element">
			<div id="picture_viewer_loading" class= "pv_element"></div>
			<div id="picture_viewer_prev" class= "pv_element"></div>
			<div id="picture_viewer_next" class= "pv_element"></div>

			<div id="picture_viewer_counterwrapper" class= "pv_element">
				<span id="picture_viewer_text" class= "pv_element"></span>
			</div>

			<div class="rewired_settings_close"></div>
		</div>
</div>



