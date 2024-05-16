<?php
/* Template Name: Register Form */

if (is_user_logged_in()) {
    wp_redirect(home_url('admin'));
}
// Include necessary WordPress functions
require_once(ABSPATH . 'wp-admin/includes/image.php');
require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');

// Initialize variables
$errors = array();
$user_name = $full_name = $user_email = $upass = $gender = '';
$attachment_id = null;

// Check if the form has been submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {

    // Sanitize and validate user input
    $user_name = isset($_POST['uname']) ? sanitize_text_field($_POST['uname']) : '';
    $full_name = isset($_POST['full_name']) ? sanitize_text_field($_POST['full_name']) : '';
    $user_email = isset($_POST['uemail']) ? sanitize_email($_POST['uemail']) : '';
    $upass = isset($_POST['upass']) ? sanitize_text_field($_POST['upass']) : '';
    $gender = isset($_POST['gender']) ? sanitize_text_field($_POST['gender']) : '';

    // Error handling
    if (empty($user_name)) {
        $errors['user_err'] = 'User name is a required field';
    }
    if (strpos($user_name, ' ') !== false) {
        $errors['user_err'] = 'White space not allowed in user name field';
    }
    if (username_exists($user_name)) {
        $errors['user_name_exist'] = 'User name already exists.';
    }
    
    if (empty($full_name)) {
        $errors['fullname_err'] = 'Full name is a required field';
    }
    if (empty($user_email)) {
        $errors['user_email_err'] = 'User email is a required field';
    }
    if (email_exists($user_email)) {
        $errors['user_email_exist'] = 'User email already exists. Please choose another.';
    }
    if (empty($upass)) {
        $errors['user_pass_err'] = 'User password is a required field';
    }
    if (empty($gender)) {
        $errors['user_gender_err'] = 'User gender is a required field';
    }
    // Upload profile picture
    if (!empty($_FILES['profile_pic']['name'])) {
        $attachment_id = media_handle_upload('profile_pic', 0);
    }
    if (empty($attachment_id)) {
        $errors['user_img_err'] = 'Failed to upload profile picture';
    }
    // if (file_exists($_FILES['profile_pic']['name'])) {
    //     $errors['user_image_exist'] = 'User image already exists.';
    // }
    // If there are no errors, proceed with other actions
    if (empty($errors)) {
        // Create user
        $user_id = wp_create_user($user_name, $upass, $user_email);
        if ($user_id) {
            // Redirect to thank you page
            wp_redirect(home_url('thank-you'));
            exit;
        } else {
            // Handle error if user creation fails
            $errors['user_creation_err'] = 'Failed to create user.';
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
    <form action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" method="post" name="register_form" enctype="multipart/form-data">
        <label for="uname">User Name</label>
        <input type="text" name="uname" value="<?php echo esc_attr($user_name); ?>"><br>
        <label for="full_name">Full Name</label>
        <input type="text" name="full_name" value="<?php echo esc_attr($full_name); ?>"><br><br>
        <label for="uemail">Email</label>
        <input type="email" name="uemail" value="<?php echo esc_attr($user_email); ?>"><br><br>
        <label for="gender">Gender</label>
        <input type="radio" name="gender" value="male" <?php checked($gender, 'male'); ?>> Male
        <input type="radio" name="gender" value="female" <?php checked($gender, 'female'); ?>> Female<br><br>
        <label for="profile_pic">Profile Image</label>
        <input type="file" name="profile_pic"><br>
        <!-- Display the uploaded image if available -->
        <?php if (!empty($attachment_id)) : ?>
            <img src="<?php echo wp_get_attachment_image_url($attachment_id); ?>" alt="Image appear here">
        <?php endif; ?><br><br>
        <label for="upass">Password</label><br>
        <input type="password" name="upass" value="<?php echo esc_attr($upass); ?>"><br><br>
        <input type="submit" name="submit" value="Register">
    </form>
    <a href="<?php echo home_url('login');?>">Already register login</a>
</div>

<?php
get_footer();
?>
