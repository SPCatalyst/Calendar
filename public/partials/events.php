<?php

// Settings
$settings         = new SPC_Community_Calendar_Settings();
$repo             = new SPC_Community_Calendar_Data_Repository();
$account_response = $repo->get_account();
$account          = null;
if ( ! $account_response->is_error() ) {
	$account = $account_response->get_item();
}

// Config 1
$preferred_categories = $settings->get( 'preferred_categories', array() );
$preferred_filters    = $settings->get( 'preferred_filters', array() );

// Config 2
$show_internal_setting = $settings->get( 'type' ) === 'internal';
$show_internal = $show_internal_setting;

// Logo
$settings = new SPC_Community_Calendar_Settings();
$logo     = $settings->get( 'logo' );
if ( ! empty( $logo ) ) {
	$logo = wp_get_attachment_image_url( $logo, 'medium' );
}

// Parse Type
$type = isset( $_GET['type'] ) ? $_GET['type'] : '';
if ( $type ) {
	if ( $type === 'internal' ) {
		$show_internal = true;
	} else if ( $type === 'both' ) {
		$show_internal = false;
	}
}

// Meta
global $wp;
$short_url = home_url( $wp->request );
$full_url  = remove_query_arg( array( 'pagenum', 'pagename' ), spcc_current_page_url() );

// Find the logo url
$logo_url = remove_query_arg( 'type', $full_url );
if ( $type === 'both' ) {
	$logo_url_type = 'internal';
} else if ( $type === 'internal' ) {
	$logo_url_type = 'both';
} else {
	$logo_url_type = $show_internal_setting ? 'both' : 'internal';
}
$logo_url = add_query_arg( 'type', $logo_url_type, $logo_url );

// Find the logo image
if($logo_url_type === 'both') {
	$_remote_logo = isset($account['logo_mixed']) && !empty($account['logo_mixed']) ? $account['logo_mixed'] : '';
	if(!empty($_remote_logo)) {
		$logo = $_remote_logo;
	}
} elseif($logo_url_type === 'internal') {
	$_remote_logo = isset($account['logo_local']) && !empty($account['logo_local']) ? $account['logo_local'] : '';
	if(!empty($_remote_logo)) {
		$logo = $_remote_logo;
	}
}

// Set View
$view = $settings->get( 'preferred_view', 'list' );
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
$allowed_type_filters = array(
	array(
		'key'  => 'internal',
		'name' => 'Internal',
		'url'  => add_query_arg( 'type', 'internal', $short_url )
	),
	array(
		'key'  => 'community',
		'name' => 'Community',
		'url'  => add_query_arg( 'type', 'community', $short_url )
	),
	array(
		'key'  => 'both',
		'name' => 'Both',
		'url'  => add_query_arg( 'type', 'both', $short_url )
	)
);

$allowed_views = array(
	array(
		'key'  => 'grid',
		'name' => 'GRID',
		'icon' => 'spcc-icon spcc-icon-th',
		'url'  => add_query_arg( 'view', 'grid', $full_url )
	),
	array(
		'key'  => 'list',
		'name' => 'LIST',
		'icon' => 'spcc-icon spcc-icon-list',
		'url'  => add_query_arg( 'view', 'list', $full_url )
	),
	array(
		'key'  => 'map',
		'name' => 'MAP',
		'icon' => 'spcc-icon spcc-icon-pin',
		'url'  => add_query_arg( 'view', 'map', $full_url )
	),
);

// Static config
$website_id = get_option( 'spcc_website_id' );
$config = array(
	'per_page' => apply_filters( 'ccc_events_per_page', 8 ),
	'fields'   => 'all',
    'current_site' => $website_id,
);

// Internal?
if ( $show_internal ) {
	$config['parent'] = $website_id;
}


// API Params
$params = spcc_array_only( $_GET, array(
	'date',
	'datefrom',
	'dateto',
	'search',
) );

// Page
$params['page'] = isset( $_GET['pagenum'] ) && is_numeric( $_GET['pagenum'] ) ? intval( $_GET['pagenum'] ) : 1;
$params         = array_merge( $params, $config );

// Search
$search = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
if(!empty($search)) {
    $params['search'] = $search;
}

// Sort
$sort_by           = isset( $_GET['sort_by'] ) ? $_GET['sort_by'] : 'date';
$params['orderby'] = $sort_by;

// Category
$category = isset( $_GET['category'] ) ? (int) $_GET['category'] : null;
if ( ! is_null( $category ) ) {
	$params['category'] = $category;
} else if ( ! empty( $preferred_categories ) ) {
	$params['category'] = implode( ',', $preferred_categories );
}

// Filters
$filter = isset( $_GET['filter'] ) ? (int) $_GET['filter'] : null;
if ( ! is_null( $filter ) ) {
	$params['filter'] = $filter;
} else if ( ! empty( $preferred_filters ) ) {
	$params['filter'] = implode( ',', $preferred_filters );
}

// Query
$params['date_format'] = 'm-d-Y';
$events      = $repo->get_events( $params );
$events_list = $events->get_items();

// Query the categories
$categories      = $repo->get_categories();
$categories_list = $categories->get_items();

// Query the filters
$filters      = $repo->get_filters();
$filters_list = $filters->get_items();

$total       = (int) $events->get_header( 'X-WP-Total' );
$total_pages = (int) $events->get_header( 'X-WP-TotalPages' );

/// View urls
$url_sort_by_name = add_query_arg( 'sort_by', 'name', $full_url );
$url_sort_by_date = add_query_arg( 'sort_by', 'date', $full_url );

// Pagination
$prev_page = $params['page'] > 1 ? $params['page'] - 1 : 1;
$next_page = $params['page'] + 1 > $total_pages ? $total_pages : $params['page'] + 1;
$url_next  = add_query_arg( 'pagenum', $next_page, $full_url );
$url_prev  = add_query_arg( 'pagenum', $prev_page, $full_url );

// Featured
$show_featured = (int) $settings->get('show_featured', 0);


?>
<div class="spcc-events-container">

    <?php if($show_featured): ?>
    <div class="spcc-events-row">
        <?php echo spcc_featured_events(array('type' => 'featured', 'per_page' => 4)); ?>
    </div>
    <?php endif; ?>

    <div class="spcc-events-row">
        <div class="spcc-events-filters">
			<?php if ( ! empty( $logo ) ): ?>
                <div class="spcc-banner">
                    <a href="<?php echo $logo_url; ?>"><img alt="ad" src="<?php echo $logo; ?>"></a>
                </div>
			<?php endif; ?>
            <div class="spcc-filters">
                <form class="spcc-events-filters-form" id="spcc-events-filters-form" action="" method="GET">
                    <div class="spcc-form-row">
                        <input type="text" class="spcc-form-control" placeholder="Search events..." id="search" name="search" value="<?php echo $search; ?>">
                    </div>
                    <div class="spcc-form-row">
                        <label>Show events for:</label>
                        <ul class="spcc-inline-list">
							<?php foreach ( $allowed_date_filters as $allowed_date_filter ): ?>
                                <li>
                                    <a target="_self" href="<?php echo $allowed_date_filter['url']; ?>"
                                       class="<?php echo spcc_get_var( 'date' ) === $allowed_date_filter['key'] ? 'active' : ''; ?>">
										<?php echo $allowed_date_filter['name']; ?>
                                    </a>
                                </li>
							<?php endforeach; ?>
                        </ul>
                    </div>
                    <div class="spcc-form-row f-14">
                        <label class="spcc-label-fw" for="datefrom">Show events between:</label>
                        <input class="spcc-form-control" type="text" autocomplete="off" name="datefrom" id="datefrom"
                               value="<?php echo spcc_get_var( 'datefrom' ); ?>"><span>&</span><input
                                class="spcc-form-control" autocomplete="off"
                                type="text" name="dateto"
                                id="dateto"
                                value="<?php echo spcc_get_var( 'dateto' ); ?>">
                    </div>
                    <div class="spcc-form-row">
                        <div id="calendar"></div>
                    </div>
                    <div class="spcc-form-row">
                        <label for="filter">Filters</label>
                        <select id="filter" name="filter" class="spcc-form-control spcc-w-100">
                            <option value="0" <?php selected( $filter, null ); ?>>All Filters</option>
							<?php foreach ( $filters_list as $term ): ?>
                                <option value="<?php echo $term['id']; ?>" <?php selected( $filter, $term['id'] ); ?>><?php echo $term['name']; ?></option>
							<?php endforeach; ?>
                        </select>
                    </div>
                    <div class="spcc-form-row">
                        <label for="category">Category</label>
                        <select id="category" name="category" class="spcc-form-control spcc-w-100">
                            <option value="0" <?php selected( $category, null ); ?>>All Categories</option>
							<?php foreach ( $categories_list as $term ): ?>
                                <option value="<?php echo $term['id']; ?>" <?php selected( $category, $term['id'] ); ?>><?php echo $term['name']; ?></option>
							<?php endforeach; ?>
                        </select>

                    </div>
                    <div class="spcc-form-row">
                        <input type="hidden" name="view" value="<?php echo $view; ?>">
                        <input type="hidden" name="sort_by" value="<?php echo spcc_get_var( 'sort_by' ); ?>">
                        <button type="submit" class="spcc-btn spcc-btn-primary">Go!</button>
                        <a href="<?php echo $short_url; ?>" class="spcc-btn spcc-btn-link spcc-reset">Reset</a>
                    </div>
                </form>
            </div>
            <div class="spcc-other">
                <form id="spcc-subscribe" class="js-cm-form subscribe-form spc-newsletter-cc" method="post">
                    <div class="cc-s-header">
                        <img src="<?php echo plugin_dir_url( dirname( __FILE__ ) ); ?>img/poweredby.png"
                             alt="subscribe">
                    </div>
                    <div class="cc-s-body">
                        <div class="cc-form-row">
                            <h3 class="title-sub">Get your free subscription</h3>
                        </div>
                        <div class="cc-form-row">
                            <input id="fieldName" class="spcc-form-control" placeholder="Name" name="name" type="text"/>
                        </div>
                        <div class="cc-form-row">
                            <input id="fieldEmail" class="spcc-form-control js-cm-email-input" name="email" placeholder="Email"
                                   type="email" required/>
                        </div>
                        <div class="cc-form-row">
                            <button class="spcc-btn spcc-btn-primary" type="submit">Subscribe</button>
                        </div>
                        <div class="cc-form-row">
                            <h3 class="title-listed">Want your event listed?</h3>
                            <p>
                                <a class="spcc-btn spcc-btn-primary" href="https://stpetecatalyst.com/contribute/event"
                                   type="submit">Submit your event</a>
                            </p>
                        </div>
                    </div>
                </form>
				<?php do_action( 'spcc_sidebar' ); ?>
            </div>
        </div>
        <div class="spcc-events-main">
            <div class="spcc-events-main--filters">
                <ul>
                    <li class="spcc-view-links">
                        <span>VIEW BY:</span>
						<?php foreach ( $allowed_views as $value ): ?>
                            <a target="_self"
                               class="<?php echo $view === $value['key'] ? 'current' : ''; ?>"
                               href="<?php echo $value['url']; ?>"><i
                                        class="<?php echo $value['icon']; ?>"></i> <?php echo $value['name']; ?></a>
						<?php endforeach; ?>
                    </li>
                    <li class="spcc-sort-links">
                        <span>SORT BY:</span>
                        <a target="_self"
                           class="<?php echo empty( $sort_by ) || $sort_by === 'date' ? 'current' : ''; ?>"
                           href="<?php echo $url_sort_by_date; ?>">DATE</a>&nbsp;|&nbsp;
                        <a target="_self" class="<?php echo $sort_by === 'name' ? 'current' : ''; ?>"
                           href="<?php echo $url_sort_by_name; ?>">NAME</a>
                    </li>
					<?php if ( $total_pages > 1 ): ?>
                        <li class="spcc-nav-links">
                            <a target="_self" href="<?php echo $url_prev; ?>" class="spcc-backward"><i class="spcc-icon spcc-icon-fast-bw"></i></a>
                            <a target="_self" href="#"><?php echo $params['page']; ?> OF <?php echo $total_pages; ?></a>
                            <a target="_self" href="<?php echo $url_next; ?>" class="spcc-forward"><i class="spcc-icon spcc-icon-fast-fw"></i></a>
                        </li>
					<?php endif; ?>
                </ul>
            </div>
            <div class="spcc-events-main--list">
				<?php
				if ( $total_pages > 0 ) {
					if ( $view === 'grid' ) {
						$view = 'events-grid';
					} else if ( $view === 'map' ) {
						$view = 'events-map';
					} else {
						$view = 'events-list';
					}
					echo spcc_get_view( $view, array( 'events_list' => $events_list ) );
				} else {
				    if($show_internal) {
                        echo '<p>There are no events here, please click to display community events using the graphic on the left.</p>';
                    } else {
                        echo '<p>No events found for your query</p>';
                    }
				}
				?>
            </div>

            <div class="spcc-events-footer">
                <div class="spcc-events-main--filters">
                    <ul>
                        <?php if ( $total_pages > 1 ): ?>
                            <li class="spcc-nav-links">
                                <a target="_self" href="<?php echo $url_prev; ?>" class="spcc-backward"><i class="spcc-icon spcc-icon-fast-bw"></i></a>
                                <a target="_self" href="#"><?php echo $params['page']; ?> OF <?php echo $total_pages; ?></a>
                                <a target="_self" href="<?php echo $url_next; ?>" class="spcc-forward"><i class="spcc-icon spcc-icon-fast-fw"></i></a>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

        </div>
    </div>
</div>

