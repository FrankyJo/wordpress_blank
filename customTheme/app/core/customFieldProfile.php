<?php
add_action('show_user_profile', 'profile_extra_fields');
add_action('edit_user_profile', 'profile_extra_fields');

function profile_extra_fields($user) {
    ?>
    <h3>Extra profile information</h3>
    <table class="form-table">
        <tr>
            <th><label for="position">Position</label></th>
            <td>
                <input type="text" name="extra[position]" id="position" class="regular-text"
                       value="<?php echo esc_attr(get_the_author_meta('position', $user->ID)); ?>"/><br/>
                <span class="description">Please enter your position.</span>
            </td>
        </tr>
        <tr>
            <th><label for="shortBiographicalInfo">Short Biographical Info</label></th>
            <td>
                <textarea type="text" name="extra[shortBiographicalInfo]" id="shortBiographicalInfo" class="regular-text"><?php echo esc_attr(get_the_author_meta('shortBiographicalInfo', $user->ID)); ?></textarea><br/>
                <span class="description">Please enter your short biographical info.</span>
            </td>
        </tr>
        <tr>
            <th><label for="position">LinkedIn Profile Link</label></th>
            <td>
                <input type="text" name="extra[linkedinLink]" id="linkedinLink" class="regular-text"
                       value="<?php echo esc_attr(get_the_author_meta('linkedinLink', $user->ID)); ?>"/><br/>
                <span class="description">Please enter your LinkedIn profile link.</span>
            </td>
        </tr>
        <tr>
            <th><label for="position">Forbes Councils Link</label></th>
            <td>
                <input type="text" name="extra[forbesLink]" id="forbesLink" class="regular-text"
                       value="<?php echo esc_attr(get_the_author_meta('forbesLink', $user->ID)); ?>"/><br/>
                <span class="description">Please enter your Forbes profile link.</span>
            </td>
        </tr>
    </table>
    <h3>Viewable settings</h3>
    <table class="form-table">
        <tr>
            <th><label for="viewableInRC">Is Viewable</label></th>
            <td>
                <input type="checkbox" name="extra[viewableInRC]" id="viewableInRC" class="regular-text"
                       value="1" <?php echo esc_attr(get_the_author_meta('viewableInRC', $user->ID)) == 1 ? 'checked="checked"' : ''; ?>/>
            </td>
        </tr>
        <tr>
            <th><label for="orderInRC">Order in List</label></th>
            <td>
                <input type="number" name="extra[orderInRC]" id="orderInRC" class="regular-text"
                       value="<?php echo esc_attr(get_the_author_meta('orderInRC', $user->ID)); ?>"/>
            </td>
        </tr>
    </table>
    <?php
}

add_action('personal_options_update', 'save_profile_extra_fields');
add_action('edit_user_profile_update', 'save_profile_extra_fields');
function save_profile_extra_fields($user_id) {
    if (empty($_POST['extra']) || empty($_POST['_wpnonce']) || !wp_verify_nonce($_POST['_wpnonce'], 'update-user_' . $user_id)) {
        return false;
    }
    if (!current_user_can('edit_user', $user_id)) {
        return false;
    }

    if (!$_POST['extra']['viewableInRC']) $_POST['extra']['viewableInRC'] = '0';

    foreach ($_POST['extra'] as $key => $value) {
        if (empty($value)) {
            delete_user_meta($user_id, $key);
            continue;
        }

        update_user_meta($user_id, $key, $value);
    }

    return true;
}
