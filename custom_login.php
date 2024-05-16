<?php
/* Template Name: Login Form */

// Initialize variables
if (is_user_logged_in()) {
    wp_redirect(home_url('admin'));
}

$errors = array();

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // Sanitize and validate user input
    $user_name_or_email = isset($_POST['user_name_or_email']) ? sanitize_text_field($_POST['user_name_or_email']) : '';
    $upass = isset($_POST['upass']) ? sanitize_text_field($_POST['upass']) : '';

    // Error handling
    if (empty($user_name_or_email)) {
        $errors['user_err'] = 'Username or email is a required field';
    } elseif (!filter_var($user_name_or_email, FILTER_VALIDATE_EMAIL) && !username_exists($user_name_or_email)) {
        $errors['user_err'] = 'Invalid username or email';
    }

    if (empty($upass)) {
        $errors['user_pass_err'] = 'User password is a required field';
    }

    // If there are no errors, attempt to log in the user
    if (empty($errors)) {
        // Determine if the provided input is a username or email
        if (filter_var($user_name_or_email, FILTER_VALIDATE_EMAIL)) {
            $user_data = get_user_by('email', $user_name_or_email);
            $user_login = $user_data ? $user_data->user_login : '';
        } else {
            $user_login = $user_name_or_email;
        }

        // Attempt to log in the user
        $login_data = array(
            'user_login'    => $user_login,
            'user_password' => $upass,
            'remember'      => true
        );

        $user = wp_signon($login_data, false);

        if (is_wp_error($user)) {
            // If login fails, add an error message
            $errors['login_err'] = 'Invalid username or password';
        } else {
            // If login succeeds, redirect the user
            $get_user_data = get_user_meta($user->ID, 'extra_user_info', true);
            if (!isset($get_user_data['redirect_link']) && empty($get_user_data['redirect_link'])) {
                // If no redirection link is set, redirect to a default page
                echo "your redirect not set";
                wp_redirect(home_url('admin')); // Change to the default page URL you prefer
                exit;
            } else {
                // Redirect the user to the specified link
                    if ($get_user_data['role_add']==='dada') {
                        wp_redirect('https://google.com');
                        exit;
                    }
                    if ($get_user_data['role_add']==='Security') {
                        wp_redirect('https://yahoo.com');
                        exit;
                    }
                    if ($get_user_data['role_add']==='Bhai') {
                        wp_redirect('https://x.com');
                        exit;
                    }if ($get_user_data['role_add']==='sister') {
                        wp_redirect('https://walmart.com');
                        exit;
                    }

                // wp_redirect($get_user_data['redirect_link']);
                // exit;
            }
        }
    }
}

// Output HTML markup
get_header();
?>

<div class="container" style="width: 50%; margin-bottom:40px;">
    <?php if (isset($errors) && !empty($errors)) : ?>
        <div style="color: red;">
            <?php foreach ($errors as $error) : ?>
                <ul>
                    <li><?php echo $error ?></li>
                </ul>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" name="login_form">
        <label for="user_name_or_email">Username or Email</label>
        <input type="text" name="user_name_or_email" value=""><br><br>
        <label for="upass">Password</label><br>
        <input type="password" name="upass" value=""><br><br>
        <input type="submit" name="submit" value="Login">
    </form>
    <br>
    <a href="<?php echo home_url('register');?>">New Here Register</a>
</div>

<?php
get_footer();
?>
