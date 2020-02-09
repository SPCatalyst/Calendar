<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://stpetecatalyst.com
 * @since      1.0.0
 *
 * @package    SPC_Community_Calendar
 * @subpackage SPC_Community_Calendar/admin/partials
 */
?>

<div class="wrap">
    <h2><?php _e( 'Events', 'spcc' ); ?> <a class="add-new-h2" href="#createEvent" rel="modal:open"><?php _e( 'Add New', 'spcc' ); ?></a></h2>
    <form method="post">
        <input type="hidden" name="page" value="ttest_list_table">
		<?php
		$list_table = new SPC_Community_Calendar_Events_List_Table();
		$list_table->prepare_items();
		$list_table->search_box( 'search', 'search_id' );
		$list_table->views();
		$list_table->display();
		?>
    </form>
</div>


<div id="createEvent" class="modal event-form">
    <h3 class="event-form-title"><?php _e('Create event'); ?></h3>
    <?php include dirname(__FILE__) . '/form-create.php'; ?>
</div>