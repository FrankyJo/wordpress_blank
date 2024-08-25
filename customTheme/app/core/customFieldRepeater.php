<?php

function my_custom_fields_add_meta_box()
{
    add_meta_box(
        'my_custom_fields_meta_box',
        'Webinar info',
        'my_custom_fields_meta_box_callback',
        'webinars'
    );
}

add_action('add_meta_boxes', 'my_custom_fields_add_meta_box');

function my_custom_fields_meta_box_callback($post)
{
    wp_nonce_field('my_custom_fields_nonce', 'my_custom_fields_nonce');

    $saved_names = get_post_meta($post->ID, 'custom_text_name', true);
    $saved_positions = get_post_meta($post->ID, 'custom_text_position', true);
    $saved_image_ids = get_post_meta($post->ID, 'custom_image_id', true);
    $saved_webinar_link = get_post_meta($post->ID, 'custom_text_webinar_link', true);

    $saved_names = is_array($saved_names) ? $saved_names : [$saved_names];
    $saved_positions = is_array($saved_positions) ? $saved_positions : [$saved_positions];
    $saved_image_ids = is_array($saved_image_ids) ? $saved_image_ids : [$saved_image_ids];

    ?>
    <div id="custom_field_repeater">
        <ul id="custom_field_list">
            <h3>Webinar Link</h3>
            <input type="text" class="custom_text_name" name="custom_text_webinar_link" value="<?php echo esc_attr($saved_webinar_link); ?>" />
            <h3>Webinar Speakers</h3>
            <?php foreach ($saved_names as $index => $value): ?>
                <li>
                    <input type="text" class="custom_text_name" name="custom_text_name[]" value="<?php echo esc_attr($saved_names[$index]); ?>" />
                    <input type="text" class="custom_text_position" name="custom_text_position[]" value="<?php echo esc_attr($saved_positions[$index] ?? ''); ?>" />
                    <?php if (!empty($saved_image_ids[$index])): ?>
                        <?php $image_url = wp_get_attachment_url($saved_image_ids[$index]); ?>
                        <img class="preview-image" src="<?php echo esc_url($image_url); ?>" width="150" height="auto" />
                        <button class="button button-primary change-image" type="button">Change image</button>
                    <?php else: ?>
                        <button class="button button-primary upload-image" type="button">Upload Image</button>
                    <?php endif; ?>
                    <input type="hidden" id="custom_image_id" name="custom_image_id[]" value="<?php echo esc_attr($saved_image_ids[$index] ?? ''); ?>" />
                    <button type="button" class="button button-primary remove_field ">Remove</button>
                </li>
            <?php endforeach; ?>
        </ul>
        <button type="button" id="add_field" class="button button-primary" >Add row</button>
    </div>
    <style>
        #custom_field_repeater{

        }
        #custom_field_list li{
            display: flex;
            flex-direction: row;
            align-items: flex-end;
            gap: 20px;
        }

        .custom_text_name{
            order: 1;
        }
        .custom_text_position{
            order: 2;
        }
        .preview-image{
            order: 3;
        }
        .change-image,
        .upload-image{
            order: 4;
        }
        .remove_field{
            order: 5;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Adding a new row
            document.getElementById('add_field').addEventListener('click', function(e) {
                e.preventDefault();
                let list = document.getElementById('custom_field_list');
                const newField = document.createElement('li');

                newField.innerHTML = `
                    <input type="text" class="custom_text_name" name="custom_text_name[]" />
                    <input type="text" class="custom_text_position" name="custom_text_position[]" />
                    <button type="button" class="button button-primary upload-image">Upload image</button>
                    <input type="hidden" name="custom_image_id[]" />
                    <button type="button" class="button button-primary remove_field">Remove</button>
                `;

                list.appendChild(newField);
            });

            // Dynamically add event listeners for image upload
            document.addEventListener('click', function(e) {
                if(e.target && (e.target.className.includes('upload-image') || e.target.className.includes('change-image'))) {
                    const button = e.target;
                    const parent = button.closest('li');
                    const imageIdInput = parent.querySelector('input[type=hidden]');

                    const frame = wp.media({
                        title: 'Upload image',
                        button: {
                            text: 'Use this image'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        const attachment = frame.state().get('selection').first().toJSON();
                        imageIdInput.value = attachment.id;
                        button.textContent = 'Change image';
                        const imagePreview = document.createElement('img');
                        imagePreview.src = attachment.url;
                        imagePreview.width = 150; // Use style or CSS if necessary
                        imagePreview.className = 'preview-image';

                        const existingPreview = parent.querySelector('.preview-image');
                        if(existingPreview) {
                            parent.removeChild(existingPreview);
                        }
                        parent.appendChild(imagePreview);
                    });

                    frame.open();
                }
            });

            // Event listener for removing a row
            document.addEventListener('click', function(e) {
                if(e.target && e.target.className.includes('remove_field')) {
                    const listItem = e.target.closest('li');
                    listItem.remove();
                }
            });
        });
    </script>
    <?php
}


function my_custom_fields_save_meta_box_data($post_id)
{
    if ('webinars' != $_POST['post_type']) {
        return;
    }

    if (!isset($_POST['my_custom_fields_nonce']) || !wp_verify_nonce($_POST['my_custom_fields_nonce'], 'my_custom_fields_nonce')) {
        return;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    delete_post_meta($post_id, 'custom_text_name');
    delete_post_meta($post_id, 'custom_text_position');
    delete_post_meta($post_id, 'custom_image_id');

    if (isset($_POST['custom_text_webinar_link'])) {
        update_post_meta($post_id, 'custom_text_webinar_link', $_POST['custom_text_webinar_link']);
    }

    if (isset($_POST['custom_text_name'])) {
        update_post_meta($post_id, 'custom_text_name', $_POST['custom_text_name']);
    }

    if (isset($_POST['custom_text_position'])) {
        update_post_meta($post_id, 'custom_text_position', $_POST['custom_text_position']);
    }

    if (isset($_POST['custom_image_id'])) {
        update_post_meta($post_id, 'custom_image_id', $_POST['custom_image_id']);
    }
}

add_action('save_post', 'my_custom_fields_save_meta_box_data');
