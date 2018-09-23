<?php
/**
 * Google Maps Draw Module
 * @package drawonmaps
 * 
 * File: html_menu_top.php
 * Description: Site menu
 */
?>
 
<nav class="navbar navbar-default navbar-fixed-top" role="navigation">
    <div class="container">
        <div class="navbar-header">
            <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a class="navbar-brand" href="index.php"><?php echo $lang_msg['MENU_LOGO']; ?></a>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="nav navbar-nav">
                <li<?php echo ($page=='home'?' class="active"':''); ?>><a href="index.php"><?php echo $lang_msg['MENU_HOME']; ?></a></li>
                <li<?php echo ($page=='map_create'?' class="active"':''); ?>><a href="index.php?p=map_create"><?php echo $lang_msg['MENU_CREATE_MAP']; ?></a></li>
                <li<?php echo ($page=='maps'?' class="active"':''); ?>><a href="index.php?p=maps"><?php echo $lang_msg['MENU_BROWSE_MAPS']; ?></a></li>
                <li<?php echo ($page=='map_global'?' class="active"':''); ?>><a href="index.php?p=map_global"><?php echo $lang_msg['MENU_GLOBAL_MAP']; ?></a></li>
                <li<?php echo ($page=='faq'?' class="active"':''); ?>><a href="index.php?p=faq"><?php echo $lang_msg['MENU_GLOBAL_FAQ']; ?></a></li> 
                <li<?php echo ($page=='orders'?' class="active"':''); ?>><a href="index.php?p=orders"><?php echo $lang_msg['MENU_GLOBAL_ORDERS']; ?></a></li> 

            </ul>
            <ul class="nav navbar-nav navbar-right">
                <?php if(isset($vars['user']) && $vars['user']) { ?>

                            <li><a href="index.php?p=deposit" ><?php echo $vars['user']->balance; ?> CIT</a></li>


                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $vars['user']->username; ?> <span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="index.php?p=maps&mymaps"><?php echo $lang_msg['MENU_MYMAPS']; ?></a></li>
                            <li><a href="index.php?p=deposit"><?php echo $lang_msg['MENU_DEPOSIT']; ?></a></li>

                <?php
                    if ($vars['user']->isAdmin()) {
                ?>
                            <li class="divider"></li>
                            <li><a href="index.php?p=users"><?php echo $lang_msg['MENU_USERS']; ?></a></li>
                <?php } ?>   
                            <li class="divider"></li>

                            <!-- <li class="dropdown-header">Nav header</li> -->
                            <li><a href="index.php?logout"><?php echo $lang_msg['MENU_LOGOUT']; ?></a></li>
                        </ul>
                    </li>
                <?php } else { ?>							
                    <li><a href="index.php?p=login"><?php echo $lang_msg['MENU_LOGIN']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
</nav>

 
