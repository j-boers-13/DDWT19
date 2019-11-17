<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';

/* Connect to DB */

$db = connect_db('localhost', 'ddwt19_week1', 'ddwt19','ddwt19');

/* Landing page */
if (new_route('/DDWT19/week1/', 'get')) {
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Home' => na('/DDWT19/week1/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', True),
        'Overview' => na('/DDWT19/week1/overview/', False),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT19/week1/overview/', 'get')) {
    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $series = get_series($db);
    $left_content = get_series_table($series);

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT19/week1/serie/', 'get')) {
    $serie_id = htmlspecialchars($_GET["serie_id"]);
    /* Get series from db */
    $serie_info = get_series_info($db, $serie_id);
    $serie_name = $serie_info['name'];
    $serie_abstract = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];

    /* Page info */
    $page_title = $serie_name;
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview/', False),
        $serie_name => na('/DDWT19/week1/serie/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_name);
    $page_content = $serie_abstract;

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT19/week1/add/', 'get')) {
    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Add Series' => na('/DDWT19/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', True)
    ]);

    /* Page content */

    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT19/week1/add/';

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT19/week1/add/', 'post')) {
    /* Page info */
    $serie_info = $_POST;
    $message = add_series($db, $serie_info);
    if ($message['type'] === ['danger']){
        $error_msg = $message['message'];
    }
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Add Series' => na('/DDWT19/week1/add/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', True)
    ]);

    /* Page content */
    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT19/week1/add/';

    include use_template('new');
}

/* Edit serie GET */
elseif (new_route('/DDWT19/week1/edit/', 'get')) {
    /* Get serie info from db */
    $serie_id = htmlspecialchars($_GET["serie_id"]);
    $series_info = get_series_info($db, $serie_id);

    $serie_name = 'House of Cards';
    $serie_abstract = 'A Congressman works with his equally conniving wife to exact revenge on the people who betrayed him.';
    $nbr_seasons = '6';
    $creators = 'Beau Willimon';

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        sprintf("Edit Series %s", $serie_name) => na('/DDWT19/week1/new/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $submit_btn = "Edit Series";
    $form_action = '/DDWT19/week1/edit/';
    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Edit %s", $serie_name);
    $page_content = 'Edit the series below.';

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT19/week1/edit/', 'post')) {
    /* Get serie info from db */
    $serie_id = htmlspecialchars($_POST["serie_id"]);
    $series_info = get_series_info($db, $serie_id);
    $serie_name = 'House of Cards';
    $serie_abstract = 'A Congressman works with his equally conniving wife to exact revenge on the people who betrayed him.';
    $nbr_seasons = '6';
    $creators = 'Beau Willimon';

    /* Page info */
    $page_title = $series_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview/', False),
        $serie_name => na('/DDWT19/week1/serie/', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', False),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $submit_btn = "Edit Series";
    $form_action = '/DDWT19/week1/edit/?serie=' . $serie_id;
    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = sprintf("Information about %s", $serie_name);
    $page_content = $series_info['abstract'];

    /* Choose Template */
    include use_template('serie');
}

/* Remove serie */
elseif (new_route('/DDWT19/week1/remove/', 'post')) {
    /* Remove serie in database */
    $serie_id = $_POST['serie_id'];
/*    $feedback = remove_serie($db, $serie_id); */
/*    $error_msg = get_error($feedback);*/

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 1' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True)
    ]);
    $navigation = get_navigation([
        'Home' => na('/DDWT19/week1/', False),
        'Overview' => na('/DDWT19/week1/overview', True),
        'Add Series' => na('/DDWT19/week1/add/', False)
    ]);

    /* Page content */
    $series_count = count_series($db);
    $right_column = use_template('cards');
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $series = get_series($db);
    $left_content = get_series_table($series);

    /* Choose Template */
    include use_template('main');
}

else {
    http_response_code(404);
}
