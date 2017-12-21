<?php

function responsive_bartik_preprocess_html(&$variables)
{
  // Add variables for path to theme.
  $variables['base_path'] = base_path();
  $variables['path_to_resbartik'] = drupal_get_path('theme', 'responsive_bartik');

  // Add local.css stylesheet
  if (file_exists(drupal_get_path('theme', 'responsive_bartik') . '/css/local.css')) {
    drupal_add_css(drupal_get_path('theme', 'responsive_bartik') . '/css/local.css',
      array('group' => CSS_THEME, 'every_page' => TRUE));
  }

  // Add body classes if certain regions have content.
  if (!empty($variables['page']['featured'])) {
    $variables['classes_array'][] = 'featured';
  }

  if (!empty($variables['page']['triptych_first'])
    || !empty($variables['page']['triptych_middle'])
    || !empty($variables['page']['triptych_last'])
  ) {
    $variables['classes_array'][] = 'triptych';
  }

  if (!empty($variables['page']['footer_firstcolumn'])
    || !empty($variables['page']['footer_secondcolumn'])
    || !empty($variables['page']['footer_thirdcolumn'])
    || !empty($variables['page']['footer_fourthcolumn'])
  ) {
    $variables['classes_array'][] = 'footer-columns';
  }

drupal_add_js('renderbackground();', array(
  'type' => 'inline',
  'scope' => 'footer',
  'group' => JS_THEME,
  'weight' => 1,
));
  
drupal_add_js('bejegyzesekcollapsible();', array(
  'type' => 'inline',
  'scope' => 'footer',
  'group' => JS_THEME,
  'weight' => 2,
));

	global $user;
	global $base_url;
	$host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$srvr = $_SERVER['SERVER_NAME'];	
	$uid = $user->uid;
	$account = user_load($uid);
	
	//$master_race = field_get_items('user', $account, 'field_master_race'); 
	//$ultramobil = field_get_items('user', $account, 'field_ultramobil');
	//$alairaselrejtes = field_get_items('user', $account, 'field_alairaselrejtes'); 
	//$maxfontmeret = field_get_items('user', $account, 'field_maxfontmeret');
	$counts[$user->uid] = _privatemsg_assemble_query('unread_count', $user)->execute()->fetchField();
	$CookieVariables = CookieVariables();
	$rewired_cookie_values = $CookieVariables['rewired_cookie_values'];
	$gfx = $rewired_cookie_values['gfx'];
	$maxfontmeret = $rewired_cookie_values['mfm'];
	$alairaselrejtes = $rewired_cookie_values['aie'];
	$profilkepelrejtes = $rewired_cookie_values['pke'];
	$ultramobil = $rewired_cookie_values['ult'];
	$hdmode = $rewired_cookie_values['hdm'];
	
	//forum grafika szint
	switch ($gfx) {
		case 1: $variables['classes_array'][] = 'graph_low';
		break;
		//case 2 = default
		case 3: $variables['classes_array'][] = 'graph_high';
		break;
	}
	
	// max fontmeret override
	switch ($maxfontmeret) {
		case 1: $variables['classes_array'][] = 'font_small';
		break;
		case 2: $variables['classes_array'][] = 'font_medium';
		break;
		//case 3 = default
	}
	
	// alairas eljertes
	if ($alairaselrejtes == 1) $variables['classes_array'][] = 'hideas';
	
	// profilkep eljertes
	if ($profilkepelrejtes == 1) $variables['classes_array'][] = 'hidepk';
	
	// ultramobil 
	if ($ultramobil == 1) $variables['classes_array'][] = 'ultramobil';
	
	// HD mode
	if ($hdmode == 1) $variables['classes_array'][] = 'hdmode';
	
	// uzenetek kijelzes 
	if ($counts[$user->uid] != 0) $variables['classes_array'][] = 'pmuzenet';
	
	//hide breadcrumb on these pages
	//$crumbhiderstring = $srvr . '.forum';
	//$crumbhiderstring = "#\b(" . $crumbhiderstring . ")\b#";
	//if (preg_match($crumbhiderstring, $host ) ) $variables['classes_array'][] = 'hidecrumb';
	
	
	$lolipoptheme = array(
        '#tag' => 'meta',
        '#attributes' => array(
		"name" => "theme-color",
		"content" => "#413b55",
		),
	);
	
	drupal_add_html_head($lolipoptheme,'theme-color'); //lolipop theme color
	
}

function CookieVariables() {
	$rewired_cookie_name = "Rewired_Cookie";
	
	//reading-validating or defining rewired cookies
	if(isset($_COOKIE[$rewired_cookie_name])) {
		$rewired_cookie_values = json_decode($_COOKIE[$rewired_cookie_name],true);

		$cookiesNeedsUpdating = 0;
		
		if (!isset($rewired_cookie_values['ver']) || $rewired_cookie_values['ver'] <2 || $rewired_cookie_values['ver'] >2) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['ver'] = 2;
		}
		if (!isset($rewired_cookie_values['gfx']) || $rewired_cookie_values['gfx'] <1 || $rewired_cookie_values['gfx'] >3) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['gfx'] = 2;
		}
		if (!isset($rewired_cookie_values['mfm']) || $rewired_cookie_values['mfm'] <1 || $rewired_cookie_values['mfm'] >3) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['mfm'] = 3;
		}
		if (!isset($rewired_cookie_values['aie']) || $rewired_cookie_values['aie'] <0 || $rewired_cookie_values['aie'] >1) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['aie'] = 0;
		}
		if (!isset($rewired_cookie_values['ape']) || $rewired_cookie_values['ape'] <0 || $rewired_cookie_values['ape'] >1) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['ape'] = 1;
		}
		if (!isset($rewired_cookie_values['pke']) || $rewired_cookie_values['pke'] <0 || $rewired_cookie_values['pke'] >1) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['pke'] = 0;
		}
		if (!isset($rewired_cookie_values['ult']) || $rewired_cookie_values['ult'] <0 || $rewired_cookie_values['ult'] >1) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['ult'] = 0;
		}
		if (!isset($rewired_cookie_values['sze']) || $rewired_cookie_values['sze'] <600 || $rewired_cookie_values['sze'] >1000) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['sze'] = 800;
		}
		if (!isset($rewired_cookie_values['hdm']) || $rewired_cookie_values['hdm'] <0 || $rewired_cookie_values['hdm'] >1) {
			$cookiesNeedsUpdating = 1;
			$rewired_cookie_values['hdm'] = 0;
		}
		
		$output = array(
			'rewired_cookie_values' => $rewired_cookie_values,
			'rewired_cookie_name' => $rewired_cookie_name
			);
			
		if ($cookiesNeedsUpdating) {
			$rewired_cookie_values = json_encode($rewired_cookie_values);
			setcookie($rewired_cookie_name, $rewired_cookie_values, time() + (60*60*24*365), "/");
		}
	}
	else {
		$rewired_cookie_values['ver'] = 2;
		
		$rewired_cookie_values['gfx'] = 2;
		$rewired_cookie_values['mfm'] = 3;
		$rewired_cookie_values['aie'] = 0;
		$rewired_cookie_values['ape'] = 1;
		$rewired_cookie_values['pke'] = 0;
		$rewired_cookie_values['ult'] = 0;
		$rewired_cookie_values['sze'] = 800;
		$rewired_cookie_values['hdm'] = 0;
		$output = array(
			'rewired_cookie_values' => $rewired_cookie_values,
			'rewired_cookie_name' => $rewired_cookie_name
			);
		$rewired_cookie_values = json_encode($rewired_cookie_values);
		setcookie($rewired_cookie_name, $rewired_cookie_values, time() + (60*60*24*365), "/");
	}
	
	return $output;
}

/**
 * Override or insert variables into the page template for HTML output.
 */
function responsive_bartik_process_html(&$variables)
{
  // Hook into color.module.
  if (module_exists('color')) {
    _color_html_alter($variables);
  }
}

/**
 * Override or insert variables into the page template.
 */
 function responsive_bartik_views_mini_pager($vars) {
  global $pager_page_array, $pager_total;

  $tags = $vars['tags'];
  $element = $vars['element'];
  $parameters = $vars['parameters'];

  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  if ($pager_total[$element] > 1) {

    $li_previous = theme('pager_previous', array(
      'text' => (isset($tags[1]) ? $tags[1] : t('‹‹')),
      'element' => $element,
      'interval' => 1,
      'parameters' => $parameters,
    ));
    if (empty($li_previous)) {
      $li_previous = "&nbsp;";
    }

    $li_next = theme('pager_next', array(
      'text' => (isset($tags[3]) ? $tags[3] : t('››')),
      'element' => $element,
      'interval' => 1,
      'parameters' => $parameters,
    ));

    if (empty($li_next)) {
      $li_next = "&nbsp;";
    }

    $items[] = array(
      'class' => array('pager-previous'),
      'data' => $li_previous,
    );

    $items[] = array(
      'class' => array('pager-current'),
      'data' => t('@current / @max', array('@current' => $pager_current, '@max' => $pager_max)),
    );

    $items[] = array(
      'class' => array('pager-next'),
      'data' => $li_next,
    );
    return theme('item_list', array(
      'items' => $items,
      'title' => NULL,
      'type' => 'ul',
      'attributes' => array('class' => array('pager')),
    ));
  }
}
 
 
function responsive_bartik_process_page(&$variables)
{
  // Hook into color.module.
  if (module_exists('color')) {
    _color_page_alter($variables);
  }
  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name'] = theme_get_setting('toggle_name') ? FALSE : TRUE;
  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(variable_get('site_name', 'Drupal'));
  }
  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }
  // Since the title and the shortcut link are both block level elements,
  // positioning them next to each other is much simpler with a wrapper div.
  if (!empty($variables['title_suffix']['add_or_remove_shortcut']) && $variables['title']) {
    // Add a wrapper div using the title_prefix and title_suffix render elements.
    $variables['title_prefix']['shortcut_wrapper'] = array(
      '#markup' => '<div class="shortcut-wrapper clearfix">',
      '#weight' => 100,
    );
    $variables['title_suffix']['shortcut_wrapper'] = array(
      '#markup' => '</div>',
      '#weight' => -99,
    );
    // Make sure the shortcut link is the first item in title_suffix.
    $variables['title_suffix']['add_or_remove_shortcut']['#weight'] = -100;
  }
 
}

/**
 * Implements hook_preprocess_maintenance_page().
 */
function responsive_bartik_preprocess_maintenance_page(&$variables)
{
  // By default, site_name is set to Drupal if no db connection is available
  // or during site installation. Setting site_name to an empty string makes
  // the site and update pages look cleaner.
  // @see template_preprocess_maintenance_page
  if (!$variables['db_is_active']) {
    $variables['site_name'] = '';
  }
  drupal_add_css(drupal_get_path('theme', 'responsive_bartik') . '/css/maintenance-page.css');
}

/**
 * Override or insert variables into the maintenance page template.
 */
function responsive_bartik_process_maintenance_page(&$variables)
{
  // Always print the site name and slogan, but if they are toggled off, we'll
  // just hide them visually.
  $variables['hide_site_name'] = theme_get_setting('toggle_name') ? FALSE : TRUE;
  $variables['hide_site_slogan'] = theme_get_setting('toggle_slogan') ? FALSE : TRUE;
  if ($variables['hide_site_name']) {
    // If toggle_name is FALSE, the site_name will be empty, so we rebuild it.
    $variables['site_name'] = filter_xss_admin(variable_get('site_name', 'Drupal'));
  }
  if ($variables['hide_site_slogan']) {
    // If toggle_site_slogan is FALSE, the site_slogan will be empty, so we rebuild it.
    $variables['site_slogan'] = filter_xss_admin(variable_get('site_slogan', ''));
  }
}

/**
 * Override or insert variables into the node template.
 */
function responsive_bartik_preprocess_node(&$variables)
{

  if ($variables['view_mode'] == 'full' && node_is_page($variables['node'])) {
    $variables['classes_array'][] = 'node-full';
  }
  
	/*facebook open graph */
  	global $base_url; //our url
	
	if (isset($variables['node'])) { //if page = node type
		$node = node_load($variables['node']->nid); // load node from ID
		
		$ogDescription = field_get_items('node', $node, 'field_og_description');
		$ogDescription = $ogDescription[0]['value'];
		
		$ogImage = field_get_items('node', $node, 'field_og_image');
		$ogImage = $ogImage[0]['value'];

	}
	
	if (!isset($ogImage)) { $ogImage = $base_url."/sites/all/files/rewired_og.jpg"; }
	if (!isset($ogDescription)) { 
		$ogDescription = "REWiRED - Kutyus felfedo szétszéledés - 2014-2057 © Minden Jog Fenntartva!
Virtuális valóság és Kecskeklónozó központ - Oculus MegaRift - PS21 - Mozi - 4D - Bajuszpödrés
Médiaajánlat/Borsós Brassói Árak
Rohadt Impresszum!";
	}
	
	//dsm($ogDescription);
	//dsm($ogImage);

	$element = array(
        '#tag' => 'meta',
        '#attributes' => array(
		"property" => "shareaholic:image",
		"content" => $ogImage,
		),
	);
	drupal_add_html_head($element,'shareaholic_share_image');

    $element = array(
        '#tag' => 'meta',
        '#attributes' => array(
		"property" => "og:image",
		"content" => $ogImage,
		),
	);
	drupal_add_html_head($element,'facebook_share_image');
	
	$element = array(
        '#tag' => 'meta',
        '#attributes' => array(
		"property" => "twitter:image",
		"content" => $ogImage,
		),
	);
	drupal_add_html_head($element,'twitter_share_image');
	
	//$element = array(
        //'#tag' => 'meta',
        //'#attributes' => array(
		//"property" => "referrer",
		//"content" => "no-referrer",
		//),
	//);
	
	$element = array(
        '#tag' => 'meta',
        '#attributes' => array(
		"property" => "og:type",
		"content" => "article",
		),
	);
	drupal_add_html_head($element,'facebook_object_type');
	
	$element = array(
        '#tag' => 'meta',
        '#attributes' => array(
		"property" => "og:description",
		"content" => $ogDescription,
		),
	);
	drupal_add_html_head($element,'facebook_description');
}

/**
 * Override or insert variables into the block template.
 */
function responsive_bartik_preprocess_block(&$variables)
{
  // In the header region visually hide block titles.
  if ($variables['block']->region == 'header') {
    $variables['title_attributes_array']['class'][] = 'element-invisible';
  }
}

/**
 * Implements theme_menu_tree().
 */
function responsive_bartik_menu_tree($variables)
{
  return '<ul class="menu clearfix">' . $variables['tree'] . '</ul>';
}

/**
 * Implements theme_field__field_type().
 */
function responsive_bartik_field__taxonomy_term_reference($variables)
{
  $output = '';

  // Render the label, if it's not hidden.
  if (!$variables['label_hidden']) {
    $output .= '<h3 class="field-label">' . $variables['label'] . ': </h3>';
  }

  // Render the items.
  $output .= ($variables['element']['#label_display'] == 'inline') ? '<ul class="links inline">' : '<ul class="links">';
  foreach ($variables['items'] as $delta => $item) {
    $output .= '<li class="taxonomy-term-reference-' . $delta . '"' . $variables['item_attributes'][$delta] . '>' . drupal_render($item) . '</li>';
  }
  $output .= '</ul>';

  // Render the top-level DIV.
  $output = '<div class="' . $variables['classes'] . (!in_array('clearfix', $variables['classes_array']) ? ' clearfix' : '') . '"' . $variables['attributes'] . '>' . $output . '</div>';

  return $output;
}

function responsive_bartik_preprocess_page(&$variables, $hook)
{	
	// variables
	global $user;
	global $base_url;
	global $authorPanelElrejtes;
	
	$host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$srvr = $_SERVER['SERVER_NAME'];	
	$uid = $user->uid;
	$account = user_load($uid);
	//$szelesseg = field_get_items('user', $account, 'field_maxforumszelesseg');
	$CookieVariables = CookieVariables();
	$rewired_cookie_values = $CookieVariables['rewired_cookie_values'];
	$rewired_cookie_name = $CookieVariables['rewired_cookie_name'];
	$szelesseg = $rewired_cookie_values['sze'];
	$hdmode = $rewired_cookie_values['hdm'];
	$authorPanelElrejtes = $rewired_cookie_values['ape'];

	global $theme_path;
	
	//foreach ($variables['main_menu'] as $key => $value) {
    //$variables['main_menu'][$key]['html'] = TRUE;
    //$variables['main_menu'][$key]['title'] = '<span>'. $variables['main_menu'][$key]['title'] .'</span>';
	//}
	//drupal_set_message(t(print_r($variables)), 'status');
	
	// search block 
	$block = module_invoke('search','block_view','search');
    $rendered_block = render($block);
    $variables['mysb'] = $rendered_block;
  
	$form = drupal_get_form('search_form');
	$cajabusqueda = drupal_render($form);
	$variables['cajabusqueda'] = $cajabusqueda;
	
	// szelesseg atmeretezesek az oldalakra 
	$szelesseg = $szelesseg + 20; //add left and right margin

	$messagespage = $srvr . '.messages.view.*';
	$messagespage = "#\b(" . $messagespage . ")\b#";
	$commentpage = $srvr . '.node.*|' . $srvr . '.content.*|' . $srvr . '.comment.*';
	$commentpage = "#\b(" . $commentpage . ")\b#";
	$topikokpage = $srvr . '.*.hozzaszolasok.*|' . $srvr . '.*.topikok.*|' . $srvr . '.tags.*|' . $srvr . '.taxonomy.*';
	$topikokpage = "#\b(" . $topikokpage . ")\b#";
	$frontpage = $srvr . '.node.page=*|' . $srvr . '.node.|' . $srvr . '.node';
	$frontpage = "#\b(" . $frontpage . ")\b#";
	
	if ($srvr . '/' == $host || preg_match($frontpage, $host )  )	{  //frontpage
		//$szelesseg_css = "#content { max-width: " . ($szelesseg + 2) . "px; } ";
	}
	else if ($srvr . '.forum' == $host)	{ //forum root
	}
	else if(preg_match($messagespage, $host ) ) { //messages
		$szelesseg_css =  "
				#content {
				max-width: " . ($szelesseg + 14 + 40 - 20 + 10) . "px;
				}
		
				.form-textarea-wrapper.resizable.textarea-processed.resizable-textarea {
				max-width: " . ($szelesseg - 20) . "px;
				}
				";			
	}
	else if(preg_match($commentpage, $host ) ) { //comments page
		$szelesseg_css = "
				.content {
				max-width: " . ($szelesseg + 150 + 3) . "px;
				}
		
				.form-textarea-wrapper.resizable.textarea-processed.resizable-textarea {
				max-width: " . ($szelesseg - 20) . "px;
				}
				";
	}
	else if(preg_match($topikokpage, $host ) ) { // user hozzaszolasok/topikok view/tags view
		$szelesseg_css =  "
				#content {
				max-width: " . ($szelesseg + 4) . "px !important;
				}
				";
	}

	if (isset($szelesseg_css))  {	drupal_add_css($szelesseg_css, 'inline');	}

	//hide pager on these pages
	
	$pagerhiderstring = $srvr . '.user.*.messages|' . $srvr . '.user.*.edit|' . $srvr . '.user.*.bookmarks|' . $srvr . '.user.*.notifications|' . $srvr . '.user.*.badges|' . $srvr . '.user.*.content.lock|' . $srvr . '.comment.*.edit|' . $srvr . '.comment.reply.*|' 	. $srvr . '.users.*|' . $srvr . '.forum';
					
	$pagerhiderstring = "#\b(" . $pagerhiderstring . ")\b#";

	if (preg_match($pagerhiderstring, $host ) || $srvr === $host || ($srvr . '/' === $host ) ) $showpager = 0;
	else $showpager = 1;
	
	// pages with darkening shadow class
	$shadowstring = $srvr . '.node.add.*|' . $srvr . '.node.*.edit|' . $srvr . '.user.*.edit|' . $srvr . '.user.login|' . $srvr . '.user.password|' . $srvr . '.user.register|' . $srvr . '.user|' . $srvr . '.messages.*|' . $srvr . '.node.*.track*|' . $srvr . '.node.*.votes*|' . $srvr . '.comment.*.edit|' . $srvr . '.comment.*.delete|' . $srvr . '.users.*|'  . $srvr . '.cimkek|'  . $srvr . '.taxonomy.term.*.edit|' . $srvr . '.tags.*|' . $srvr . '.flag.*|' . $srvr . '.felhasznalok|' . $srvr . '.myuserpoints|' . $srvr . '.search.*';
		
	$shadowstring = "#\b(" . $shadowstring . ")\b#";

	if(preg_match($shadowstring, $host ) || $srvr  == $host ) $pageswithshadow = 1;
	else	$pageswithshadow = 0;
	
	 //display "Láttam" button on forum root view: "Bejegyzesek"
	if ($srvr . '/' == $host) $forumroot = 1;
	else $forumroot = 0;

	//preprocessed variables
	$variables['host'] = $host;
	$variables['srvr'] = $srvr;
	$variables['szelesseg'] = $szelesseg;
	$variables['showpager'] = $showpager;
	$variables['pageswithshadow'] = $pageswithshadow;
	$variables['forumroot'] = $forumroot;
	$variables['base_url'] = $base_url;
		
	drupal_add_js(array('szelesseg' => $szelesseg), 'setting');
	drupal_add_js(array('hdm' => $hdmode), 'setting');
	drupal_add_js(array('rewiredcookiename' => $rewired_cookie_name), 'setting');
	drupal_add_js(array('loggeduserid' => $uid), 'setting');
}

function responsive_bartik_form_alter(&$form, &$form_state, $form_id) {
	
		// Add some cool text to the search block form
	  if ($form_id == 'search_block_form') {
    // HTML5 placeholder attribute
    $form['search_block_form']['#attributes']['placeholder'] = t('Keresés');
  }
  
if (in_array( $form_id, array( 'search_form')))
    {
        // Adding placeholders to fields
$form['basic']['keys']['#attributes']['placeholder']= t( 'Keresés mező' );
    }
}

function responsive_bartik_get_user_comments_count($uid) {
  $query = db_select('comment', 'c');
  $query->condition('uid', $uid, '=');
  $query->condition('status', '1', '=');
  $query->addExpression('COUNT(1)', 'count');
  $result = $query->execute();

  if ($record = $result->fetchAssoc())
    return $record['count'];
  
  return 0;
}

function responsive_bartik_pager($variables) {
	//hide pager on these pages
	$host = $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
	$srvr = $_SERVER['SERVER_NAME'];	

	$pagerhiderstring = $srvr . '.user.*.messages|' . $srvr . '.user.*.edit|' . $srvr . '.user.*.bookmarks|' . $srvr . '.user.*.notifications|' . $srvr . '.user.*.badges|' . $srvr . '.user.*.content.lock|' . $srvr . '.comment.*.edit|' . $srvr . '.comment.reply.*|' 	. $srvr . '.users.*|' . $srvr . '.forum';
					
	$pagerhiderstring = "#\b(" . $pagerhiderstring . ")\b#";
		
	if (preg_match($pagerhiderstring, $host ) || $srvr === $host || ($srvr . '/' === $host ) ) $showpager = 0;
	else $showpager = 1;
	
 if ($showpager == 1) 
 {
  //$tags = $variables['tags'];
  //$element = $variables['element'];
  //$parameters = $variables['parameters'];
  //$quantity = $variables['quantity'];
    
  $tags = $variables['tags']; //$variables['tags'];
  $element = $variables['element'];
  $parameters = $variables['parameters'];
  $quantity = 5; //$variables['quantity'];
  
    
  global $pager_page_array, $pager_total;

  // Calculate various markers within this pager piece:
  // Middle is used to "center" pages around the current page.
  $pager_middle = ceil($quantity / 2);
  // current is the page we are currently paged to
  $pager_current = $pager_page_array[$element] + 1;
  // first is the first page listed by this pager piece (re quantity)
  $pager_first = $pager_current - $pager_middle + 1;
  // last is the last page listed by this pager piece (re quantity)
  $pager_last = $pager_current + $quantity - $pager_middle;
  // max is the maximum page number
  $pager_max = $pager_total[$element];
  // End of marker calculations.

  // Prepare for generation loop.
  $i = $pager_first;
  if ($pager_last > $pager_max) {
    // Adjust "center" if at end of query.
    $i = $i + ($pager_max - $pager_last);
    $pager_last = $pager_max;
  }
  if ($i <= 0) {
    // Adjust "center" if at start of query.
    $pager_last = $pager_last + (1 - $i);
    $i = 1;
  }
  // End of generation loop preparation.

  $li_first =  theme('pager_first', array('text' => (isset($tags[0]) ? $tags[0] : t(1)), 'element' => $element, 'parameters' => $parameters));
  $li_previous = theme('pager_previous', array('text' => (isset($tags[1]) ? $tags[1] : t('előző')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_next =theme('pager_next', array('text' => (isset($tags[3]) ? $tags[3] : t('kovetkező')), 'element' => $element, 'interval' => 1, 'parameters' => $parameters));
  $li_last = theme('pager_last', array('text' => (isset($tags[4]) ? $tags[4] : t($pager_max)), 'element' => $element, 'parameters' => $parameters));

  if ($pager_total[$element] > 1) {

    // When there is more than one page, create the pager list.
    if ( $i != $pager_max) {
		
	if ($pager_current >= $quantity -1 && $pager_max > $quantity) {
		$items[] = array(
			'class' => array('pager-first'),
			'data' => $li_first,
		);
    }		
		
      if ($i > 2 && $pager_max > $quantity ) {
        $items[] = array(
          'class' => array('pager-ellipsis'),
          'data' => '…' //…
        );
      }
	  
      // Now generate the actual pager piece.
      for (; $i <= $pager_last && $i <= $pager_max; $i++) {
        if ($i < $pager_current ) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' =>  theme('pager_previous', array('text' => $i, 'element' => $element, 'interval' => ($pager_current - $i), 'parameters' => $parameters)),
          );
        }
        if ($i == $pager_current) {
          $items[] = array(
            'class' => array('pager-current'),
            'data' => $i,
          );
        }
        if ($i > $pager_current) {
          $items[] = array(
            'class' => array('pager-item'),
            'data' =>  theme('pager_next', array('text' => $i, 'element' => $element, 'interval' => ($i - $pager_current), 'parameters' => $parameters)),
          );
        }
      }
      if ($i < $pager_max) {
        $items[] = array(
          'class' => array('pager-ellipsis'),
          'data' => '…', //…
        );
      }
	  
	 if ( $pager_current <= $pager_max - $quantity +2 && $pager_max - $quantity > 0){
      $items[] = array(
        'class' => array('pager-last'),
        'data' => $li_last,  
      );
    }
	  
    }
    // End generation.

	return theme('item_list', array(
      'items' => $items,
      'attributes' => array('class' => array('pager')),
    ));
  }
  return theme('item_list');
 }
}	
		
		
		
		
		
		