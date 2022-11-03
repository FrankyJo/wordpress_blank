<?php
// change from post_type to name your post type
add_action('add_meta_boxes', 'post_type_extra_fields', 1);
function events_extra_fields()
{
    add_meta_box('extra_fields', 'Additional Fields', 'post_type_extra_fields_box_func', 'post_type', 'normal', 'high');
}

function post_type_extra_fields_box_func($post)
{
    ?>
    <p>Event Url<label><input type="text" name="extra[url]" value="<?php echo get_post_meta($post->ID, 'url', 1); ?>" style="width:100%"/></label></p>
    <p>Event Date<label><input type="text" name="extra[date]" value="<?php echo get_post_meta($post->ID, 'date', 1); ?>" style="width:100%"/></label></p>
    <p>Event Key<label><input type="text" name="extra[key]" value="<?php echo get_post_meta($post->ID, 'key', 1); ?>" style="width:100%"/></label></p>
    <input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>"/>
    <?php
}

add_action('save_post', 'post_type_extra_fields_update', 0);
function post_type_extra_fields_update($post_id)
{
    if (empty($_POST['extra'])
        || !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__)
        || wp_is_post_autosave($post_id)
        || wp_is_post_revision($post_id)) {
        return false;
    }

    $_POST['extra'] = array_map('sanitize_text_field', $_POST['extra']);
    foreach ($_POST['extra'] as $key => $value) {
        if (empty($value)) {
            delete_post_meta($post_id, $key);
            continue;
        }

        update_post_meta($post_id, $key, $value);
    }

    return $post_id;
}
