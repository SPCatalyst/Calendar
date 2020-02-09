<?php

// Meta
global $wp;
$short_url = home_url( $wp->request );
$full_url  = remove_query_arg( array( 'pagenum', 'pagename' ), spcc_current_page_url() );

// Set View
$view = 'list';
if ( isset( $_GET['view'] ) && in_array( $_GET['view'], array( 'grid', 'list', 'map' ) ) ) {
	$view = sanitize_text_field( $_GET['view'] );
}
$allowed_date_filters = array(
	array(
		'key'  => 'today',
		'name' => 'Today',
		'url'  => add_query_arg( 'date', 'today', $short_url )
	),
	array(
		'key'  => 'week',
		'name' => 'This Week',
		'url'  => add_query_arg( 'date', 'week', $short_url )
	),
	array(
		'key'  => 'month',
		'name' => 'This Month',
		'url'  => add_query_arg( 'date', 'month', $short_url )
	),
);
$allowed_views        = array(
	array(
		'key'  => 'grid',
		'name' => 'GRID',
		'icon' => 'fa fa-th',
		'url'  => add_query_arg( 'view', 'grid', $full_url )
	),
	array(
		'key'  => 'list',
		'name' => 'LIST',
		'icon' => 'fa fa-list',
		'url'  => add_query_arg( 'view', 'list', $full_url )
	),
	array(
		'key'  => 'map',
		'name' => 'MAP',
		'icon' => 'fa fa-map-pin',
		'url'  => add_query_arg( 'view', 'map', $full_url )
	),
);

// Static config
$config = array(
	'per_page' => 10,
	'parent'   => get_option( 'spcc_website_id' ),
	'fields'   => 'all',
);

// API Params
$params = spcc_array_only( $_GET, array(
	'date',
	'datefrom',
	'dateto',
	'search',
	'category',
	'filter',
	'sort_by',
	'page',
) );
$params = array_merge( $params, $config );

// Vars
$page = isset( $_GET['pagenum'] ) && is_numeric( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;

// Query
$repo        = new SPC_Community_Calendar_Data_Repository();
$events      = $repo->get_events( $params );
$events_list = $events->get_items();

$total       = (int) $events->get_header( 'X-WP-Total' );
$total_pages = (int) $events->get_header( 'X-WP-TotalPages' );

/// View urls
$url_sort_by_name = add_query_arg( 'sort_by', 'name', $full_url );
$url_sort_by_date = add_query_arg( 'sort_by', 'date', $full_url );

// Pagination
$prev_page = $page > 1 ? $page - 1 : 1;
$next_page = $page + 1 > $total_pages ? $total_pages : $page + 1;
$url_next  = add_query_arg( 'pagenum', $next_page, $full_url );
$url_prev  = add_query_arg( 'pagenum', $prev_page, $full_url );
$url_first = add_query_arg( 'pagenum', 1, $full_url );
$url_last  = add_query_arg( 'pagenum', $total_pages, $full_url );


?>
<div class="cc-events-container">
    <div class="cc-events-row">
        <div class="cc-events-filters">
            <div class="cc-banner">
                <a href="#"><img alt="ad" src="/wp-content/uploads/2020/01/catalyst.jpg"></a>
            </div>
            <div class="cc-filters">

                <form class="cc-events-filters-form" id="cc-events-filters-form" action="" method="GET">
                    <div class="form-row">
                        <label>Show events for</label>
                        <ul class="cc-inline-list">
							<?php foreach ( $allowed_date_filters as $allowed_date_filter ): ?>
                                <li><a target="_self" href="<?php echo $allowed_date_filter['url']; ?>"
                                       class="<?php echo spcc_get_var( 'date' ) === $allowed_date_filter['key'] ? 'active' : ''; ?>"><?php echo $allowed_date_filter['name']; ?></a>
                                </li>
							<?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="form-row f-14">
                        <label class="cc-label-fw" for="datefrom">Show events between</label>
                        <input type="text" autocomplete="off" name="datefrom" id="datefrom"
                               value="<?php echo spcc_get_var( 'datefrom' ); ?>"> And <input autocomplete="off"
                                                                                             type="text" name="dateto"
                                                                                             id="dateto"
                                                                                             value="<?php echo spcc_get_var( 'dateto' ); ?>">
                    </div>
                    <div class="form-row">
                        <label for="filter">Filters</label>
                        <select id="filter" name="filter" class="form-control">
                            <option value="0">All Filters</option>
							<?php foreach ( $filters as $term ): ?>
                                <option value="<?php echo $term->term_id; ?>" <?php selected( spcc_get_var( 'filter' ), $term->term_id ); ?>><?php echo $term->name; ?></option>
							<?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="form-control">
                            <option value="0">All Categories</option>
							<?php foreach ( $categories as $term ): ?>
                                <option value="<?php echo $term->term_id; ?>" <?php selected( spcc_get_var( 'category' ), $term->term_id ); ?>><?php echo $term->name; ?></option>
							<?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-row">
                        <input type="hidden" name="view" value="<?php echo $view; ?>">
                        <input type="hidden" name="sort_by" value="<?php echo spcc_get_var( 'sort_by' ); ?>">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>


            </div>
            <div class="cc-other">
				<?php do_action( 'spcc_sidebar' ); ?>
            </div>
        </div>
        <div class="cc-events-main">
            <div class="cc-events-main--filters">
                <ul>
                    <li class="cc-view-links">
                        <span>VIEW BY:</span>
						<?php foreach ( $allowed_views as $value ): ?>
                            <a target="_self"
                               class="<?php echo $view === $value['key'] ? 'current' : ''; ?>"
                               href="<?php echo $value['url']; ?>"><i
                                        class="<?php echo $value['icon']; ?>"></i> <?php echo $value['name']; ?></a>
						<?php endforeach; ?>
                    </li>
                    <li class="cc-sort-links">
                        <span>SORT BY:</span>
                        <a target="_self"
                           class="<?php echo empty( $sort_by ) || $sort_by === 'date' ? 'current' : ''; ?>"
                           href="<?php echo $url_sort_by_date; ?>">DATE</a>&nbsp;|&nbsp;
                        <a target="_self" class="<?php echo $sort_by === 'name' ? 'current' : ''; ?>"
                           href="<?php echo $url_sort_by_name; ?>">NAME</a>
                    </li>
					<?php if ( $total_pages > 1 ): ?>
                        <li class="cc-nav-links">
                            <a target="_self" href="<?php echo $url_first; ?>" class="cc-left"><i
                                        class="fa fa-caret-left"></i></a>
                            <a target="_self" href="<?php echo $url_prev; ?>" class="cc-backward"><i
                                        class="fa fa-backward"></i></a>
                            <a target="_self" href="#"><?php echo implode( '-', array(
									$page,
									$config['per_page']
								) ); ?>
                                OF <?php echo $total_pages; ?></a>
                            <a target="_self" href="<?php echo $url_next; ?>" class="cc-forward"><i
                                        class="fa fa-forward"></i></a>
                            <a target="_self" href="<?php echo $url_last; ?>" class="cc-right"><i
                                        class="fa fa-caret-right"></i></a>
                        </li>
					<?php endif; ?>
                </ul>
            </div>
            <div class="cc-events-main--list">
				<?php
				if ( $total > 0 ) {
					if ( $view === 'grid' ) {
						$view = 'events-grid';
					} else if ( $view === 'map' ) {
						$view = 'events-map';
					} else {
						$view = 'events-list';
					}
					echo spcc_get_view( $view, array( 'events_list' => $events_list ) );
				} else {
					echo '<p>No events found for your query</p>';
				}

				?>
            </div>
        </div>
    </div>
</div>

