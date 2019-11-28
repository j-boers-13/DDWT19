<?php
/**
 * Controller
 * User: reinardvandalen
 * Date: 05-11-18
 * Time: 15:25
 */

include 'model.php';

/* Connect to DB */
$db = connect_db('localhost', 'ddwt19_week2', 'ddwt19','ddwt19');

/* Redundant code */
/* Get Number of Series */
$nbr_series = count_series($db);
$nbr_users = count_users($db);
$right_column = use_template('cards');
$template = Array(
    1 => Array(
        'name' => 'Home',
        'url' => '/DDWT19/week2/'
    ),
    2 => Array(
        'name' => 'Overview',
        'url' => '/DDWT19/week2/overview/'
    ),
    3 => Array(
        'name' => 'My Account',
        'url' => '/DDWT19/week2/myaccount/'
    ),
    4 => Array(
        'name' => 'Register',
        'url' => '/DDWT19/week2/register/'
    ));

/* Landing page */
if (new_route('/DDWT19/week2/', 'get')) {

    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
    /* Page info */
    $page_title = 'Home';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Home' => na('/DDWT19/week2/', True)
    ]);
    $navigation = get_navigation($template, 1);

    /* Page content */
    $page_subtitle = 'The online platform to list your favorite series';
    $page_content = 'On Series Overview you can list your favorite series. You can see the favorite series of all Series Overview users. By sharing your favorite series, you can get inspired by others and explore new series.';

    /* Choose Template */
    include use_template('main');
}

/* Overview page */
elseif (new_route('/DDWT19/week2/overview/', 'get')) {

    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Overview';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Overview' => na('/DDWT19/week2/overview', True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = 'The overview of all series';
    $page_content = 'Here you find all series listed on Series Overview.';
    $left_content = get_serie_table($db, get_series($db));

    /* Choose Template */
    include use_template('main');
}

/* Single Serie */
elseif (new_route('/DDWT19/week2/serie/', 'get')) {
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }
    else {
        $user_id = $_SESSION['user_id'];
    }
    /* Get Number of Series */
    /* Get series from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);
    $display_buttons = ($user_id == $serie_info['user_id']) ? true : false;

    /* Page info */
    $added_by = get_username($db, $user_id);
    $page_title = $serie_info['name'];
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Overview' => na('/DDWT19/week2/overview/', False),
        $serie_info['name'] => na('/DDWT19/week2/serie/?serie_id='.$serie_id, True)
    ]);
    $navigation = get_navigation($template, 2);

    /* Page content */
    $page_subtitle = sprintf("Information about %s", $serie_info['name']);
    $page_content = $serie_info['abstract'];
    $nbr_seasons = $serie_info['seasons'];
    $creators = $serie_info['creator'];

    /* Choose Template */
    include use_template('serie');
}

/* Add serie GET */
elseif (new_route('/DDWT19/week2/add/', 'get')) {
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }
    else {
        $user_id = $_SESSION['user_id'];
    }
    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Page info */
    $page_title = 'Add Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Add Series' => na('/DDWT19/week2/new/', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* Page content */
    $page_subtitle = 'Add your favorite series';
    $page_content = 'Fill in the details of you favorite series.';
    $submit_btn = "Add Series";
    $form_action = '/DDWT19/week2/add/';

    /* Choose Template */
    include use_template('new');
}

/* Add serie POST */
elseif (new_route('/DDWT19/week2/add/', 'post')) {
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }
    else {
        $user_id = $_SESSION['user_id'];
    }
    /* Add serie to database */
    $feedback = add_serie($db, $_POST, $user_id);
    /* Redirect to serie GET route */
    redirect(sprintf('/DDWT19/week2/add/?error_msg=%s',
        json_encode($feedback)));
}

/* Edit serie GET */
elseif (new_route('/DDWT19/week2/edit/', 'get')) {
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }
    else {
        $user_id = $_SESSION['user_id'];
    }
    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) {
        $error_msg = get_error($_GET['error_msg']);
    }

    /* Get serie info from db */
    $serie_id = $_GET['serie_id'];
    $serie_info = get_serieinfo($db, $serie_id);

    /* Page info */
    $page_title = 'Edit Series';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        sprintf("Edit Series %s", $serie_info['name']) => na('/DDWT19/week2/new/', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* Page content */
    $page_subtitle = sprintf("Edit %s", $serie_info['name']);
    $page_content = 'Edit the series below.';
    $submit_btn = "Edit Series";
    $form_action = '/DDWT19/week2/edit/';

    /* Choose Template */
    include use_template('new');
}

/* Edit serie POST */
elseif (new_route('/DDWT19/week2/edit/', 'post')) {
    $serie_id = $_POST['serie_id'];
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }
    else {
        $user_id = $_SESSION['user_id'];
    }
    /* Update serie in database */
    $feedback = update_serie($db, $_POST, $user_id);
    redirect(sprintf('/DDWT19/week2/serie/?error_msg=%s&serie_id=%s',
        json_encode($feedback),$serie_id));
}

/* Remove serie */
elseif (new_route('/DDWT19/week2/remove/', 'post')) {
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }
    else {
        $user_id = $_SESSION['user_id'];
    }
    /* Remove serie in database */
    $serie_id = $_POST['serie_id'];
    $feedback = remove_serie($db, $serie_id, $user_id);
    redirect(sprintf('/DDWT19/week2/overview/?error_msg=%s',
        json_encode($feedback)));
}

/* My Account GET */
elseif (new_route('/DDWT19/week2/myaccount/', 'get')) {

    /* Check if logged in */
    if ( !check_login() ) {
        redirect('/DDWT19/week2/login/');
    }
    else {
        $user = get_username($db, $_SESSION['user_id']);
    }

    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }

    /* Page info */
    $page_title = 'My Account';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        "My Account" => na('/DDWT19/week2/myaccount/', True)
    ]);
    $navigation = get_navigation($template, 3);

    /* Page content */
    $page_subtitle = "My account";
    $page_content = 'Your account info.';

    /* Choose Template */
    include use_template('account');
}

/* Register GET */
elseif (new_route('/DDWT19/week2/register/', 'get')){
    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
    /* Page info */
    $page_title = 'Register';
    $breadcrumbs = get_breadcrumbs([
        'DDWT19' => na('/DDWT19/', False),
        'Week 2' => na('/DDWT19/week2/', False),
        'Register' => na('/DDWT19/week2/register/', True)
    ]);
    $navigation = get_navigation($template, 4);
    /* Page content */
    $page_subtitle = 'Register on Series Overview!';
    /* Get error msg from POST route */
    if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
    /* Choose Template */
    include use_template('register');
}

/* Register POST */
elseif (new_route('/DDWT19/week2/register/', 'post')){
    /* Register user */
    $feedback = register_user($db, $_POST);
    /* Redirect to homepage */
    redirect(sprintf('/DDWT19/week2/register/?error_msg=%s',
        json_encode($feedback)));
}

/* Login GET */
elseif (new_route('/DDWT19/week2/login/', 'get')){
     /* Page info */
     $page_title = 'Login';
     $breadcrumbs = get_breadcrumbs([
     'DDWT19' => na('/DDWT19/', False),
     'Week 2' => na('/DDWT19/week2/', False),
     'Login' => na('/DDWT19/week2/login/', True)
     ]);
     $navigation = get_navigation($template, 0);
     /* Page content */
     $page_subtitle = 'Use your username and password to login';
     /* Get error msg from POST route */
     if ( isset($_GET['error_msg']) ) { $error_msg = get_error($_GET['error_msg']); }
     /* Choose Template */
     include use_template('login');
}

/* Login POST */
elseif (new_route('/DDWT19/week2/login/', 'post')){
        /* Login user */
        $feedback = login_user($db, $_POST);
        /* Redirect to homepage */
        redirect(sprintf('/DDWT19/week2/login/?error_msg=%s',
            json_encode($feedback)));
}

/* Logout GET */
elseif (new_route('/DDWT19/week2/logout/', 'get')){
    /* Logout user */
    $feedback = logout_user();
    redirect(sprintf('/DDWT19/week2/?error_msg=%s',
        json_encode($feedback)));
}

else {
    http_response_code(404);
}
