<?php
/* Template Name: Dashboard Form */
$is_admin = current_user_can('administrator');
if ($is_admin) {
    echo "you are admin"; ?>
    <a href="<?php echo wp_logout_url(home_url('login')) ?>" title='Logout'>Logout</a><br><br><br>
<?php
exit;
}
$user_id = get_current_user_id();

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
    $role_add = isset($_POST['role_add']) ? sanitize_text_field($_POST['role_add']) : '';
    $redirect_link = isset($_POST['redirect_link']) ? esc_url_raw($_POST['redirect_link']) : '';

    // Update user meta
    $user_extra_info = array(
        'role_add' => $role_add,
        'redirect_link' => $redirect_link
    );
    update_user_meta($user_id, 'extra_user_info', $user_extra_info);
}

// Output HTML markup for the dashboard
get_header();
?>

<br>

<div class="container">
    <div style="color: red;">
        <?php
        $errors = array();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (empty($redirect_link)) {
                $errors['redirect_link_err'] = "Please enter a redirection URL or page";
            }
            if (empty($role_add)) {
                $errors['role_add_err'] = "Please add a role";
            }
        }

        if (isset($errors) && !empty($errors)) {
            foreach ($errors as $key => $err) {
                echo "<ul><li>$err</li></ul>";
            }
        }
        ?>
    </div>
    <?php
    // Retrieve user's extra information
    $get_user_data = get_user_meta($user_id, 'extra_user_info', true);
    ?>
    <a href="<?php echo wp_logout_url(home_url('login')) ?>" title='Logout'>Logout</a><br><br><br>
    <form action="" method="post">
        <label for="">Add Custom Role</label>
        <input type="text" name="role_add" value="<?php echo isset($get_user_data['role_add']) ? $get_user_data['role_add'] : ''; ?>"> <br><br>
        <label for="">Enter Redirection URL</label>
        <input type="text" name="redirect_link" value="<?php echo isset($get_user_data['redirect_link']) ? $get_user_data['redirect_link'] : ''; ?>"> <br><br>
        <input type="submit" name="submit" value="Save Changes">
    </form>
</div><br><br>

<?php
get_footer();


// Check if the user has a role and redirection link set
if (!empty($get_user_data['role_add']) && !empty($get_user_data['redirect_link'])) {
    wp_redirect($get_user_data['redirect_link']);
    exit;
}
?>
