<?php 
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: maps.php
 * Description: List of maps page
 */

  
?>

<div class="container">
    <h3 class="doc_title"><?php echo $vars['page_title']; ?></h3>
    <!-- Main component for a primary marketing message or call to action -->
    <div class="jumbotron">
<?php
    if(isset($vars['user']) && $vars['user']->isAdmin()) {
        $users = $vars['siteusers']; 
        if (isset($vars['user_delete']) && $vars['user_delete'])	{
            echo  '<p class="text-danger">'.$lang_msg['USERS_DELETE_USER_SUCCESS'].'</p>';
        }
?>        
        <table class="table table-hover">
            <th>#</th>
            <th><?php echo $lang_msg['USERS_USER_ID']; ?></th>
            <th><?php echo $lang_msg['USERS_USERNAME']; ?></th>
            <th><?php echo $lang_msg['USERS_EMAIL']; ?></th>
            <th><?php echo $lang_msg['USERS_IS_ADMIN']; ?></th>
            <th><?php echo $lang_msg['USERS_TABLE_ACTIONS']; ?></th>
        <?php
            $i = 0;
            foreach ($users as $u)	{
                $i++;
        ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $u["user_id"]; ?></td>
                    <td><?php echo $u["username"]; ?></td>
                    <td><?php echo $u["email"]; ?></td>
                    <td><?php echo ($u["is_admin"]?$lang_msg['USERS_IS_ADMIN_Y']:$lang_msg['USERS_IS_ADMIN_N']); ?></td>
                    <td><?php echo html_user_options($u["user_id"], $lang_msg, $vars['user']); ?></td>
                </tr>
        <?php
            }
        ?>
        </table>
<?php
}
else {
    echo  '<p class="text-danger">'.$lang_msg['USERS_INVALID_ACCESS'].'</p>';
}
?>        
    </div>
</div> <!-- /container -->


