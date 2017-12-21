<?php
/**
 * @file
 * Theme implementation: Template for each forum forum statistics section.
 *
 * Available variables:
 * - $current_total: Total number of users currently online.
 * - $current_users: Number of logged in users.
 * - $current_guests: Number of anonymous users.
 * - $online_users: List of logged in users.
 * - $topics: Total number of nodes (threads / topics).
 * - $posts: Total number of nodes + comments.
 * - $users: Total number of registered active users.
 * - $latest_users: Linked user names of latest active users.
 */
?>

<div id="forum-statistics">
  <?php /*<div id="forum-statistics-header"><?php print t("What's Going On?"); ?></div>*/?>

  <div id="forum-statistics-active-header" class="forum-statistics-sub-header">
    <?php print t('Aktív felhasználók: !current_total', array('!current_total' => $current_total)); ?>
  </div>
  <?php if (!empty($online_users)) : ?>
    <div id="forum-statistics-active-body" class="forum-statistics-sub-body">
      <?php print $online_users; ?>
    </div>
  <?php endif; ?>

  <div id="forum-statistics-statistics-header" class="forum-statistics-sub-header">
    <?php print t('Infópanel'); ?>
    

  </div>

  <div id="forum-statistics-statistics-body" class="forum-statistics-sub-body">
    <div id="forum-statistics-topics">
      <?php print t('Téma: !topics, Hozzászólás: !posts, Populáció: !users', array(
          '!topics' => $topics,
          '!posts' => $posts,
          '!users' => $users,
        )
      ); ?>
      <div id="forum-statistics-all-users">
		<a href="<?php global $base_url; print $base_url;?>/felhasznalok">(felhasználó adatbázis)</a>
	</div>
	<div id="forum-statistics-latest-users">
		<?php print t('A közösség friss emberei: !users', array('!users' => $latest_users)); ?>
	 </div>
    </div>
  </div>

		
	
</div>
